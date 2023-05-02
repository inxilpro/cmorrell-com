### Event Sourced

In our event sourced application, we can just add a new projector to save
page history data:

```php
class PageHistoryProjector extends Projector
{
	public function onPageUpdated(PageUpdated $event)
	{
	     Page::query()
	        ->firstWhere(['aggregate_uuid' => $event->aggregateRootUuid()])
	        ->history()
	        ->create([
	            'user_id' => $event->author_id,
                'snapshot' => [
                    'slug' => $event->slug,
                    'title' => $event->title,
                    'body' => $event->body,
                ],
            ]);
	}
}
```

Now any update to a page will log a history record. But we can go a step further 
and actually build up that table with historical data from all our existing 
`PageUpdated` events:

```shell

php artisan event-sourcing:replay App\\Projectors\\PageHistoryProjector

```

Once that command runs, we'll have full audit history from the beginning of
our app. In our imaginary scenario, we'd actually be able to point to Aaron
as the culprit, and get a big fat raise!
