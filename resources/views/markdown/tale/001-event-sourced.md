### Event Sourced

In an event sourced Laravel app, we'll need to create a couple events:

```php
#[AppliesToState(PageState::class)] // ignore this for now
class PageCreated extends Event
{
	public function __construct(
	    public string $slug,
	    public string $title,
	    public string $body,
	    public int $author_id,
	    public string $ip,
	    public string $ua,
	    public ?int $page_id = null,
	) {
	}
	
	public function handle() {
        return Page::create([
	        'id' => $this->page_id,
            'slug' => $this->slug,
            'title' => $this->title,
            'body' => $this->body,
            'author_id' => $this->author_id,
        ]);
    }
}

class PageUpdated extends Event
{
	// Constructor omitted for simplicity's sake
	
	public function handle() {
        return Page::find($this->page_id)
	        ->update([
                'slug' => $this->slug,
                'title' => $this->title,
                'body' => $this->body,
            ]);
    }
}
```

And we'll also need a controller:

```php
class PageController
{
    // ... standard CRUD endpoints and views ...

    public function store(PageRequest $request)
    {
        $page = PageCreated::commit(
            slug: $request->input('slug'),
            title: $request->input('title'),
            body: $request->input('body'),
            author_id: Auth::id(),
            ip: $request->ip(),
            ua: $request->userAgent(),
        );
        
        return to_route('pages.edit', $page);
    }
    
    public function update(PageRequest $request, Page $page)
    {
        PageUpdated::fire(
            slug: $request->input('slug'),
            title: $request->input('title'),
            body: $request->input('body'),
            author_id: Auth::id(),
            ip: $request->ip(),
            ua: $request->userAgent(),
        );
        
        return to_route('pages.edit', $page);
    }
}
```
