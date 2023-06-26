### Event Sourced

In an event sourced Laravel app, we need to set up quite a few more things. 
First, let's create some events:

```php
class PageCreated extends ShouldBeStored
{
	public function __construct(
	    public string $slug,
	    public string $title,
	    public string $body,
	    public int $author_id,
	    public string $ip,
	    public string $ua,
	) {
	}
}

class PageUpdated extends ShouldBeStored
{
	// Same as PageCreated for simplicity's sake
}
```

Next, we’ll create our aggregate root, which serves as the gateway between
our application and our events.

```php
class PageAggregateRoot extends AggregateRoot
{
	public function create(string $slug, string $title, string $body): self
	{
		return $this->recordThat(new PageCreated(
		    slug: $slug,
            title: $title,
            body: $body,
            author_id: Auth::id(),
            ip: request()->ip(),
            ua: request()->userAgent(),
		));
	}
	
	public function update(PageRequest $request): self
	{
	    // Basically the same as 'create' for now
	}
}
```

Next, we'll create a projector, which will react to our events and create
the appropriate database models.

```php
class PageProjector extends Projector
{
	public function onPageCreated(PageCreated $event)
	{
	     Page::create([
	        'uuid' => $event->aggregateRootUuid(),
            'slug' => $event->slug,
            'title' => $event->title,
            'body' => $event->body,
            'author_id' => $event->author_id,
        ]);
	}
	
	public function onPageUpdated(PageUpdated $event)
	{
	     Page::query()
	        ->firstWhere(['uuid' => $event->aggregateRootUuid()])
	        ->update([
                'slug' => $event->slug,
                'title' => $event->title,
                'body' => $event->body,
            ]);
	}
}
```

And finally, we'll create our controller.

```php
class PageController
{
    // ... standard CRUD endpoints and views ...

    public function store(PageRequest $request)
    {
        $uuid = Str::uuid();
        
        PageAggregateRoot::retrieve($uuid)
            ->create($request->input('slug'), $request->input('title'), $request->input('body'));
        
        return to_route('pages.edit', Page::firstWhere(['uuid' => $uuid]));
    }
    
    public function update(PageRequest $request, Page $page)
    {
        PageAggregateRoot::retrieve($page->uuid)
            ->update($request->input('slug'), $request->input('title'), $request->input('body'));
        
        return to_route('pages.edit', $page);
    }
}
```
