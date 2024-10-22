The question around using Models and IDs in [Verbs](https://verbs.thunk.dev/) comes up a lot
on the Verbs Discord, so I thought I'd put together a summary of my current thoughts.

In Verbs, it's best to have a clear flow of data *from* events, *to* everywhere else. In an
ideal world, your events are the single source of truth in your application (although this
often can't be entirely the case because you may have data that predates adding Verbs to your project).

## Best-case scenario

So let’s talk about the “ideal” scenario, where your models are all derived from events. Here's a
*highly simplified* User model:

```php
Schema::create('users', function(Blueprint $table) {
    $table->snowflakeId(); // Helper function in `glhd/bits`
    $table->string('name');
    $table->string('email')->unique();
});
```

When a user registers, we fire an event:

```php
class UserRegistered extends Event
{
    public function __construct(
        public string $name,
        public string $email,
        
        // When a state ID is nullable, Verbs will assume that this is a "create"-style
        // event, and auto-populate the value with a new Snowflake ID for us. 
        #[StateId(UserState::class)]
        public ?int $user_id = null,
    ) {}
    
    public function handle()
    {
        User::create([
            // Note how we *explicitly* set the user's ID here. This is because
            // the `user_id` value is globally-unique in our app, so we don't have
            // to worry about things like collisions. If we ever need to replay this
            // event, we'll just truncate the entire users table and recreate
            // everything from scratch.
            'id' => $this->user_id,
            'name' => $this->name,
            'email' => $this->email,
        ]);
    }
}
```

```php
UserRegistered::fire(
    name: $request->input('name'),
    email: $request->input('email'),
    // We don't need to pass `user_id` because Verbs handles that for us
);
```

Later, if that user becomes an admin, we can just use the ID on the model, because
we know that the ID is managed by our events (along with all the other data): 

```php
class UserPromotedToAdmin extends Event
{
    public function __construct(
        public int $user_id,
        // Protected properties aren't stored by Verbs, so they can be useful
        // for storing data for optimization purposes. Here, we'll allow optionally
        // providing the User model so that we don't have to query it a second
        // time if that's not necessary.
        protected ?User $user = null,
    ) {}
    
    public function handle()
    {
        // Load the user in case we don't have it (e.g. if we're replaying)
        $this->user ??= User::find($this->user_id);
        
        $this->user->update(['role' => 'admin']);
    }
}
```

```php
class User extends Model
{
    // ...

    public function promoteToAdmin()
    {
        UserPromotedToAdmin::fire(
            user_id: $this->getKey(),
            user: $this, // This is optional--just an optimization
        );
    }
}
```

### A basic timeline

Because **all the data** (including the ID) comes from our events, we can safely
use it across our app. Let's look at a basic timeline of events playing out in our app:

1. A `UserRegistered` event fires with a snowflake ID of `…27776` (snowflakes tend to be long)
2. A second `UserRegistered` event fires with a snowflake ID of `…24800`
3. A `UserPromotedToAdmin` event fires for user `…27776`
4. _We need to change our code in some way, and afterward we truncate `users` and replay_
5. Our first `UserRegistered` event replays with the same snowflake ID of `…27776`
6. Our second `UserRegistered` event replays with the same snowflake ID of `…24800`
7. Our `UserPromotedToAdmin` event replays for user `…27776`

As you can see, our IDs are all managed inside our events, so when we replay them, everything
is re-created exactly as it was the first time. This is good!

## Worst-case scenario

OK, so now let’s look at a similar approach that relies on auto-incrementing IDs (which are
managed by our database, not our events).

```php
Schema::create('users', function(Blueprint $table) {
    $table->id(); // Regular, auto-incrementing ID
    $table->string('name');
    $table->string('email')->unique();
});
```

When a user registers, we fire a similar event, except we rely on the database
to create an auto-incrementing ID for us:

```php
class UserRegistered extends Event
{
    public function __construct(
        public string $name,
        public string $email,
    ) {}
    
    public function handle()
    {
        User::create([
            'name' => $this->name,
            'email' => $this->email,
        ]);
    }
}
```

Then, in subsequent events, we rely on that auto-incrementing ID (this 
is the exact same code as above, but in this case, the ID is managed
**outside** our events): 

```php
class UserPromotedToAdmin extends Event
{
    public function __construct(
        public int $user_id,
        protected ?User $user = null,
    ) {}
    
    public function handle()
    {
        $this->user ??= User::find($this->user_id);   
        $this->user->update(['role' => 'admin']);
    }
}
```

### Revisiting our timeline

Let's look at the same timeline if we were to use auto-incrementing IDs:

1. A `UserRegistered` event fires with an ID of `1`
2. A second `UserRegistered` event fires with an ID of `2`
3. A `UserPromotedToAdmin` event fires for user `1`
4. _We need to change our code in some way, and afterward we truncate `users` and replay_
5. Our first `UserRegistered` event replays with an ID of `3`
6. Our second `UserRegistered` event replays with an ID of `4`
7. Our `UserPromotedToAdmin` event replays for user `1`—but now there **isn’t** a user with the ID `1` so our app crashes

Because some data is managed in our events, and some (the IDs) are managed outside them,
we run into a bug (this is especially true when replaying data). This is bad!

## Compromise scenario

So what can you do if you have models that use auto-incrementing IDs that predate your
events? The approach I like to take is “adopting” models into our event system and
assigning them unique IDs in the process.

So, given the same `users` table above, we might add a new column:

```php
Schema::table('users', function(Blueprint $table) {
    // You can (and probably should) still use snowflakes here, but I'm going
    // to use UUIDs just to show that snowflakes aren’t an absolute requirement
    // for Verbs. We're going to leave the value nullable for now, since in this
    // example we're going to assume that there's some data that pre-dates events.
    $table->uuid('universal_id')->nullable()->unique();
});
```

For all our existing users, we can fire a one-time “adoption” event that
brings that user into our event system:

```php
class LegacyUserImported extends Event
{
    public function __construct(
        public int $legacy_user_id,
        public string $universal_id,
        public string $name,
        public string $email,
    ) {}
    
    public function handle()
    {
        // On first run, we're going to adopt the legacy user into our
        // event system. On subsequent replays, we'll treat this
        // event as a "create" event
        Verbs::isReplaying()
            ? $this->recreateLegacyUser()
            : $this->adoptLegacyUser();
    }
    
    protected function adoptLegacyUser()
    {
        User::find($this->legacy_user_id)
            ->update(['universal_id' => $this->universal_id]);
    }
    
    protected function recreateLegacyUser()
    {
        // Note that the user will be assigned a new `id` column value here,
        // which will probably be an issue. You can either: a) set the `id`
        // value here, too, which may require some other intervention on your
        // part (like setting the table's auto-increment value to something very
        // high so that no new users are accidentally assigned the ID before
        // you finish replaying), or b) queue up a job to update related models
        // with the new ID (which may cause issues if you replay multiple times
        // unless you're careful)
        $user = User::create([
            'universal_id' => $this->universal_id,
            'name' => $this->name,
            'email' => $this->email,
        ]);
    }
}
```

```php
User::whereNull('universal_id')->each(function(User $user) {
    LegacyUserImported::fire(
        legacy_user_id: $user->getKey(),
        universal_id: Str::uuid(), // Create a new unique ID for them
        name: $user->name,
        email: $user->email,
    );
});
```

And when a new user registers after our event system is in place, we fire an event:

```php
class UserRegistered extends Event
{
    public function __construct(
        public string $universal_id,
        public string $name,
        public string $email,
    ) {}
    
    public function handle()
    {
        User::create([
            'universal_id' => $this->universal_id,
            'name' => $this->name,
            'email' => $this->email,
        ]);
    }
}
```

```php
UserRegistered::fire(
    // We'll create a new UUID when we fire the event. This UUID will
    // be the only ID that we care about inside our events. Our model
    // will still have an auto-incrementing ID, but we won't rely on that
    // anywhere that we're *writing* data.
    universal_id: Str::uuid(),
    name: $request->input('name'),
    email: $request->input('email'),
);
```

On subsequent events, we’ll exclusively use our `universal_id` value for queries,
which means we’re back to relying entirely on event data:

```php
class UserPromotedToAdmin extends Event
{
    public function __construct(
        public int $user_universal_id,
        protected ?User $user = null,
    ) {}
    
    public function handle()
    {
        $this->user ??= User::query()
            ->where('universal_id', $this->user_universal_id) // We're using our new column, not `id`
            ->sole();
        
        $this->user->update(['role' => 'admin']);
    }
}
```

### The compromise timeline

Some of our data predates events, and some comes exclusively from them. What does
that look like?

1. We have an *existing* user with the ID `1`
2. We fire a `LegacyUserImported` event for user `1` assigning them a UUID `7c40fd6b…`
3. A `UserRegistered` event fires with an auto-incrementing ID of `2` and a UUID of `031c1b0e…`
4. A `UserPromotedToAdmin` event fires for user with universal ID `7c40fd6b…`
5. _We need to change our code in some way, and afterward we truncate `users` and replay_
6. Our `LegacyUserImported` event replays and creates a user with universal ID `7c40fd6b…`
7. Our `UserRegistered` event replays with the same UUID `031c1b0e…`
8. Our `UserPromotedToAdmin` event replays for user `7c40fd6b…`

In this case, even though the actual `id` column is still managed outside our events, that's
OK because all writes rely on the `universal_id` column instead. That's good enough!

(It's worth noting that this approach can be useful in applications that need to use UUIDs
or ULIDs for some reason, since those are generally worse primary keys than 64-bit integers
for lookup. Your regular application code can still query/join by `id` in reads, keeping them
fast, and you only have to use the `universal_id` index when doing writes.)

### Or, just decide that some models are off-limits for replay

In reality, you may have a ton of data that predates adding Verbs to your app, and much
of that data spans dozens of tables. A pragmatic approach, in that case, is to just accept
that your existing data *cannot* be truncated and re-created, and only replay events that
create new models.

In this scenario, you lose some of the flexibility but get a simpler implementation in exchange,
which is often worth it.

In that case, our import event might look something like:

```php
class LegacyUserImported extends Event
{
    public function __construct(
        public int $legacy_user_id,
        public string $universal_id,
    ) {}
    
    public function handle()
    {
        // Just assume the user is alway in the DB with this ID
        User::find($this->legacy_user_id)
            ->update(['universal_id' => $this->universal_id]);
    }
}
```
