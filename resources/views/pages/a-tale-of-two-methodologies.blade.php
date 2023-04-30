<x-layout>
	<x-slot:main>
		<div class="container mx-auto p-4">
			<h1 class="text-5xl lg:text-6xl font-bold font-slant text-gray-800 mb-6">
				A Tale of Two Methodologies
			</h1>
			
			<x-markdown :file="md_path('tale/intro.md')" />
			
			<div class="text-center font-serif text-6xl text-gray-300 my-12">
				I
			</div>
			
			<x-markdown>
				## Part One: Storing the last logged in timestamp
				
				OK, so here's our first requirement:
				
				> track the last time a user logged in and show it in various parts of the UI.
			</x-markdown>
		</div>
		
		<x-tale.examples 
			:traditional="md_path('tale/001-traditional.md')"
			:event-sourced="md_path('tale/001-event-sourced.md')"
		/>
		
		<div class="container mx-auto p-4">
			<x-markdown>
				## What we've done so far
				
				OK, so now we have step one implemented in both a traditional Laravel
				approach and an event-sourced approach. On face value, the traditional
				approach looks much simpler. That's because it is. Event sourcing definitely
				comes with more boilerplate and works best when you follow the
				[CQRS pattern](https://martinfowler.com/bliki/CQRS.html) of separating
				reads and writes. For some features, event sourcing is not the right approach.
				But as I hope to show in the next few sections, all this work upfront comes
				with some serious upside down the road.
				
				Before moving on, there are a couple of other things to note:
				
				- In our event sourced code, we're storing a lot of extra stuff in our
				`UserLoggedIn` event. That's because storage is cheap, and we're only
				projecting what we need, so it's best to just store everything you can
				possibly imagine needing in the future.
				
				- We also added an `aggregate_uuid` to the `users` table in our event
				sourced code. This lets us refer to a User's identity before it's even
				persisted to the database, but we're not going to do that step in our
				examples.
			</x-markdown>
			
			<div class="text-center font-serif text-6xl text-gray-300 my-12">
				II
			</div>
			
			<x-markdown>
				## Part Two: Storing All Logins
				
				Now that we've completed part one, we'll work on the second requirement:
				
				> A few months later we add a requirement that admins can view a chronological
				> list of all the times a specific user logged into our app.
				
				Alright. Before we were storing a single `logged_in_at` field on our users
				table. But now we need more data! Let's take a stab at it!
			</x-markdown>
		</div>
		
		<x-tale.examples
			:traditional="md_path('tale/002-traditional.md')"
			:event-sourced="md_path('tale/002-event-sourced.md')"
		/>
		
		<div class="container mx-auto p-4">
			<x-markdown>
				## OK, that was interesting
				
				While both approaches essentially required the same changes to our schema
				and models, the event sourced application was able to retroactively fill
				the `logins` table using historical events. This is one of the benefits
				of event sourcing: **replaying events**.
				
				*Note:* In either approach, we can use a technique described by Jonathan Reinink
				in his [Eloquent Performance Patterns](https://laracasts.com/series/eloquent-performance-patterns)
				course to access the most recent login using a scope and a dynamic relationship.
			</x-markdown>
		</div>
	
	</x-slot:main>

</x-layout>
