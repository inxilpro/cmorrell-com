### Traditional

We could potentially install an audit log package, but we decide to
roll our own solution. We'll create a new page history table and model,
and then update our code to track changes:

```php
class Page extends Model
{
    public static function booted()
    {
        static::updated(function(Page $page) {
            $page->history()->create([
                'user_id' => Auth::id(),
                'snapshot' => $page->getAttributes(),
            ]);
        });
    }
}
```

Great! From here on out, when a page is updated, we'll have a nice 
snapshot of the page and metadata about who changed it. In a more
sophisticated audit log, we'd probably use `getDirty()` and store
the old and new values, but we'll just keep things simple for now.
