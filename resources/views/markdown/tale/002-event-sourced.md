### Event Sourced

In our event sourced application, we can just add a little more code to
store our audits:

```php
class PageUpdated extends Event
{
	public function handle() {
	    // ...
	    
        $page->history()
            ->create([
                'user_id' => $this->author_id,
                'snapshot' => [
                    'slug' => $this->slug,
                    'title' => $this->title,
                    'body' => $this->body,
                ],
            ]);
	}
}
```

Now any update to a page will log a history record. But we can go a step further 
and actually build up that table with historical data from all our existing 
`PageUpdated` events:

```shell
mysql -e "truncate table pages"
php artisan verbs:replay
```

Because our `Page` models are 100% derived from our events, we can just
clear out that table and replay our events. Not only do we have an audit
history moving forward, but we can also point to Aaron as the culprit, 
and get a big fat raise!
