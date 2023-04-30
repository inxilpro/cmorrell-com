So, I've been thinking about Event Sourcing a lot. I've done
[podcasts about it](https://overengineered.fm/episodes/ep-5-what-about-event-sourcing),
been involved in [big Twitter threads about it](https://twitter.com/aarondfrancis/status/1652457709319471105),
and have been talking about it with friends and coworkers non-stop lately.

One thing that keeps coming up is the question of a ”good, simple example” of
the value of event sourcing. And I think I came up with one!

So let's set the stage:

  - We start with an application that needs to track the last time a user logged in and
    show it in various parts of the UI.

  - A few months later we add a requirement that admins can view a chronological list of
    all the times a specific user logged into our app.

  - A few more months pass, and we add a new requirement: users who start to log in less
    frequently than they have in the past should receive an email asking if there's
    anything we can do to improve our site.

Given this timeline, let's look at how we might implement these requirements in a traditional
Laravel application and one that uses Event Sourcing (all examples will use the
[Spatie event sourcing package](https://spatie.be/index.php/docs/laravel-event-sourcing),
but the general principles are the same across any implementation).
