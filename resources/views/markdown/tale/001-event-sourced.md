### Event Sourced

In an event sourced Laravel app, we need to set up a few more things. Let's start by
getting all the boilerplate out of the way:

```php

// First, we'll create an event for logins
class UserLoggedIn extends ShouldBeStored
{
	public function __construct(
	    public CarbonInterface $logged_in_at,
	    public string $device_id,
	    public string $request_id,
	    public string $session_id,
	    public string $ip,
	    public string $ua,
	) {
	}
}

// Next, we'll add the necessary columns to our users table
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('logged_in_at')->nullable();
            $table->uuid('aggregate_uuid');
        });
    }
}

// Then we'll set up an aggregate root for our User
class UserAggregateRoot extends AggregateRoot
{
    public static function retrieveForUser(User $user): self
    {
        return self::retrieve($user->aggregate_uuid);
    }

	public function recordLogin(Request $request): self
	{
		return $this->recordThat(new UserLoggedIn(
		    logged_in_at: now(),
            device_id: $request->cookie('device_id'),
            request_id: $request->header('x-request-id'),
            session_id: $request->session()->getId(),
            ip: $request->ip(),
            ua: $request->userAgent(),
		));
	}
}

// Now we can set up our listener (using the same changes to
// the event service provider as our traditional code)
class UserLoginListener {
    public function handle(Login $event)
    {
        UserAggregateRoot::retrieveForUser($event->user)
            ->recordLogin(Request::instance());
    }
}

// And finally (phew!) we can set up a projector to project
// our data to the user model
class UserLoginProjector extends Projector implements ShouldQueue
{
	public function onUserLoggedIn(UserLoggedIn $event)
	{
	    User::query()
	        ->where('aggregate_uuid', $event->aggregateRootUuid())
	        ->update(['logged_in_at' => $event->logged_in_at]);
	}
}
```
