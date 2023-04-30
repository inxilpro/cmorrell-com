### Event Sourced

Our event sourced application can use the same basic changes to our
`users` table and models. Starting from there…

```php

// Rather than changing the listener, we can just update our projector:
class UserLoginProjector extends Projector implements ShouldQueue
{
    public function onUserLoggedIn(UserLoggedIn $event)
    {
        User::query()
            ->where('aggregate_uuid', $event->aggregateRootUuid())
            ->first()
            ->logins()->create([
                'ip_address' => $event->ip,
                'created_at' => $event->logged_in_at,
            ]);
    }
}

```

Once our projector is updated, we can replay all our existing `UserLoggedIn`
events:

```shell

php artisan event-sourcing:replay App\\Projectors\\UserLoginProjector

```

### Neat…

Once our events have finished replaying, our application will have full
login history as though we had implemented it this way from the start.
If, in the future, we want to show the User Agent as well as the IP address,
we can just update our projector again, truncate our `logins` table,
and replay the events again!
