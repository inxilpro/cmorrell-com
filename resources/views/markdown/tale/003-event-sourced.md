### Event Sourced

```php
class PageAggregateRoot extends AggregateRoot
{
	public function createDraft(PageRequest $request): self
	{
	    return $this->recordThat(new DraftCreated(
		    // ...
		));
	}
}
```

```php
class PageController
{
    public function store(PageRequest $request)
    {
        $aggregate_uuid = Str::uuid();
        $aggregate_root = PageAggregateRoot::retrieve($aggregate_uuid);
        
        if (Auth::user()->can('publish', Page::class)) {
            $aggregate_root->create($request);
        } else {
            $aggregate_root->createDraft($request);
            flash('Your changes have been sent for approval!');
        }
        
        return to_route('pages.edit', Page::firstWhere(['aggregate_uuid' => $aggregate_uuid]));
    }
    
    public function update(PageRequest $request, Page $page)
    {
        $aggregate_root = PageAggregateRoot::retrieve($page->aggregate_uuid);
        
        if (Auth::user()->can('publish', $page)) {
            $aggregate_root->update($request);
        } else {
            $aggregate_root->createDraft($request);    
            flash('Your changes have been sent for approval!');
        }
        
        return to_route('pages.edit', $page);
    }
}
```

```php
class PageProjector extends Projector
{
	public function onChangeApproved(ChangeApproved $event)
	{
	    // Apply changes to page
	}
}
```
