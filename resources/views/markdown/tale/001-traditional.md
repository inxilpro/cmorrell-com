### Traditional

In a traditional Laravel app, we’ll mostly accomplish this with a simple
RESTful MVC setup:

```php
class PageController
{
    // ... standard CRUD endpoints and views ...

    public function store(PageRequest $request)
    {
        $page = Page::create([
            'slug' => $request->input('slug'),
            'title' => $request->input('title'),
            'body' => $request->input('body'),
            'author_id' => Auth::id(),
        ]);
        
        return to_route('pages.edit', $page);
    }
    
    public function update(PageRequest $request, Page $page)
    {
        $page->update([
            'slug' => $request->input('slug'),
            'title' => $request->input('title'),
            'body' => $request->input('body'),
        ]);
        
        return to_route('pages.edit', $page);
    }
}
```
