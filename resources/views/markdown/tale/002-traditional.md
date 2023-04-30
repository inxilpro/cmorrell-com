### Traditional

```php

// First, we're going to have to change our database structure
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('logged_in_at');
        });
    
        Schema::create('logins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('ip_address', 50);
            $table->timestamp('created_at');
        });
    }
}

// And create/update our models
class Login extends Model
{
}

class User extends Authenticatable
{
    public function logins()
    {
        return $this->hasMany(Login::class);
    }
}

// Then we'll update our listener
class UserLoginListener {
    public function handle(Login $event)
    {
        $event->user->logins()->create([
            'ip_address' => Request::ip(),
        ]);
    }
}
```
