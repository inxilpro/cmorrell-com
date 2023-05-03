### Traditional

OK, so we're going to need to refactor some things. Our controller can no longer just 
update the `pages` table directly, because some changes may require approval. We’ll
need to add a new concept of drafts or revisions, and some approval process.

Let's add some new controllers for handling drafts. To simplify things, we'll just 
use an email approval process, rather than building a whole Draft CRUD UI. Admins
will receive a preview of the draft in their inbox, and have an “approve” button
they can click directly in the message.

```php
class DraftsController
{
    public function store(DraftRequest $request)
    {
        $draft = Draft::create([
            'slug' => $request->input('slug'),
            'title' => $request->input('title'),
            'body' => $request->input('body'),
            'page_id' => $request->input('page_id'),
            'author_id' => Auth::id(),
        ]);
        
        Mail::send(new DraftRequiresApproval($draft));
        
        flash('Your changes have been sent for approval!');
        
        return to_route('pages.index');
    }
}

class DraftApprovalsController
{
    public function store(DraftApprovalRequest $request, Draft $draft)
    {
        if (! $draft->page) {
            $draft->page()->associate(Page::create([
                'slug' => $draft->slug,
                'title' => $draft->title,
                'body' => $draft->body,
                'author_id' => $draft->author_id,
            ]));
            $draft->save();
        } else {
            $draft->page->update([
                'slug' => $draft->slug,
                'title' => $draft->title,
                'body' => $draft->body,
            ]);
        }
        
        return to_route('pages.show', $draft->page);
    }
}
```

We'll also have to update the pages controller to handle some permissions checks:

```php
class PageController
{
    public function create()
    {
        if (Auth::user()->cannot('publish', Page::class)) {
            return to_route('drafts.create');
        }
    
        // Original 'create' implementation
    }
    
    public function update(Page $page)
    {
        if (Auth::user()->cannot('publish', $page)) {
            return to_route('drafts.create', ['page_id' => $page->id]);
        }
    
        // Original 'update' implementation
    }
}
```

OK, now when someone goes to create a new page or edit an existing one,
they'll be redirected to the drafts workflow if needed. And when a draft
is created, an admin will be notified to approve it.
