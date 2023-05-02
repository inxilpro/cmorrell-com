So, I've been thinking about Event Sourcing a lot. I've done
[podcasts about it](https://overengineered.fm/episodes/ep-5-what-about-event-sourcing),
been involved in [big Twitter threads about it](https://twitter.com/aarondfrancis/status/1652457709319471105),
and have been talking about it with friends and coworkers non-stop lately.

One thing that keeps coming up is the question of a “good, simple example” of
the value of event sourcing. And I think I came up with one!

So let's set the stage:

  - Some non-technical folks at your company need to edit a handful of marketing
    pages hosted on your site. Because of the existing architecture, you decide that
    the best approach is to create a simple in-house CMS. All the CMS needs to do
    is allow staff members to create and update pages.

  - A few months later, the company CEO calls you up because one of the marketing pages
    was updated with incorrect information on it. She wants to know who made that change.
    When you tell her that you're not tracking who changed each page, she asks you to
    add some audit history so that if this ever happens again it's possible to find out
    who made the mistake.

  - Two years pass, and now the company is much bigger. While audit logs help trace
    mistakes when they happen, they don't prevent newer, inexperienced employees from
    making improper edits in the first place. The decision is made to add an approval
    workflow for any page designated as “important” and it’s up to you to implement it. 

Given this timeline, let's look at how we might implement these requirements in a traditional
Laravel application and one that uses Event Sourcing (all examples will use the
[Spatie event sourcing package](https://spatie.be/index.php/docs/laravel-event-sourcing),
but the general principles are the same across any implementation).

**Note:** we’ll be glossing over a bunch of implementation details
in the name of brevity, and focusing as much as possible on the places
where the two approaches differ.
