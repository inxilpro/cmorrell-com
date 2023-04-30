### Traditional

In a traditional Laravel app, this is pretty straightforward. We'll just
add a column to our `users` table and then update that column every time
the user logs in:

```php

// First, we'll set up a migration
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('logged_in_at')->nullable();
        });
    }
}

// Next, we'll create an event listener
class UserLoginListener {
    public function handle(Login $event)
    {
        $event->user->update([
            'logged_in_at' => now(),
        ]);
    }
}

// And finally update our EventServiceProvider
class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Login::class => [UserLoginListener::class],
    ];
}

```
