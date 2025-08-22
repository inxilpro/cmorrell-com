<x-layout title="A Tale of Two Methodologies">
	<x-slot:main>
		<div class="container mx-auto p-4">
			<h1 class="text-5xl text-center font-bold font-slant text-gray-800 mb-6 sm:text-left lg:text-6xl">
				A Tale of Two Methodologies
			</h1>
			
			<x-markdown :file="md_path('tale/intro.md')" />
			
			<div class="text-center font-serif text-6xl text-gray-300 my-12">
				I
			</div>
			
			<x-markdown>
				## Part One: Let's build a CMS
				
				OK, so here's our first requirement:
				
				> Build a simple content management system that lets staff add and
				> edit pages on the website.
			</x-markdown>
		</div>
		
		<x-tale.examples
			:traditional="md_path('tale/001-traditional.md')"
			:event-sourced="md_path('tale/001-event-sourced.md')"
		/>
		
		<div class="container mx-auto p-4">
			<x-markdown>
				## Hm.
				
				On face value, the traditional approach looks much simpler. That's because
				it is! Event sourcing can often feel a little heavy-handed at first (although
				Verbs helps mitigate that!) and works best when you think about separating
				reads and writes (like the [CQRS pattern](https://martinfowler.com/bliki/CQRS.html)).
				
				In our traditional approach, we just create or update a model when a new request
				comes in. In contrast, our event sourcing approach needs to:
				
				1. Dispatch an event when a request comes in
				2. Reacts to that event in our `handle` method to actually write to the database
				
				As we’ll see shortly, this comes with some big advantages, but can be a little
				tricky to wrap your head around at first!
			</x-markdown>
			
			<div class="bg-purple-50 border border-purple-200 rounded-sm p-4 mt-8">
				<div>
					<strong>One little note:</strong> In our event sourced code, we're storing
					extra stuff in our events. That's because storage is cheap, and we're only
					using what we need in our models, so it's best to just store everything you 
					can possibly imagine needing in the future.
				</div>
				<div class="mt-4 italic opacity-70">
					(I sometimes joke that if it was
						reasonable to store humidity levels and ambient noise readings, I would…
						events should represent <strong>what happend</strong> as closely as
						possible to reality.)
				</div>
			</div>
			
			<div class="text-center font-serif text-6xl text-gray-300 my-12">
				II
			</div>
			
			<x-markdown>
				## Part Two: Storing Audit Logs
				
				Now that we've got a basic CMS working, let’s get to our second requirement:
				
				> Store audit history for our CMS, so we know who made what changes to each page
				
				Seems simple enough. Let's get started!
			</x-markdown>
		</div>
		
		<x-tale.examples
			:traditional="md_path('tale/002-traditional.md')"
			:event-sourced="md_path('tale/002-event-sourced.md')"
		/>
		
		<div class="container mx-auto p-4">
			<x-markdown>
				## OK, that was interesting
				
				While both approaches essentially required similar changes, the event sourced
				application was able to retroactively fill the history table using existing events.
				This is one of the benefits of event sourcing: **replaying events**.
				
				Because we can replay events, it makes it possible to work with historical data
				and almost “retroactively” change our application.
			</x-markdown>
			
			<div class="text-center font-serif text-6xl text-gray-300 my-12">
				III
			</div>
			
			<x-markdown>
				## Part Three: Lock It Down
				
				OK. Our CMS has been chugging along for years and things are going great. Both solutions
				work well, and while the traditional app is missing some audit history, there's not
				much we can do about it, so we've just moved on.
				
				It’s time to take on our newest requirement:
				
				> Changes need to be approved by an admin before they go live
				
				We talk it over with our marketing director, and they decides they just want to receive
				an email when a change is submitted, and wants to approve/decline it directly from that message
				(rather than having a new section of the site to check regularly).
			
			</x-markdown>
		</div>
		
		<x-tale.examples
			:traditional="md_path('tale/003-traditional.md')"
			:event-sourced="md_path('tale/003-event-sourced.md')"
		/>
	
	</x-slot:main>

</x-layout>
