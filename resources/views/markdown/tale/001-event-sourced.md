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

#### …
For brevity's sake, we'll leave out the controller for now. In the event-sourced code, all
it would do is fire the `PageCreated` and `PageUpdated` events rather than creating the 
`Page` models directly.
