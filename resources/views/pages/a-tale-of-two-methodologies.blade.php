<x-layout>
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
				## Whew!
				
				On face value, the traditional approach looks much simpler. That's because 
				it is! Event sourcing definitely comes with more boilerplate and works best 
				when you follow the [CQRS pattern](https://martinfowler.com/bliki/CQRS.html) 
				of separating reads and writes.
				
				In our traditional approach, we just create or update a model when a new request
				comes in. In contrast, our event sourcing approach:
				
				1. Dispatches an event when a request comes in
				2. Reacts to that event in our projector to actually write to the database
				3. Fetches the resulting model from the database
				
				Separating reads from writes has its advantages, but we’ve definitely taken
				~20 lines of code and split them across ~90 lines and 5 files.
			</x-markdown>
			
			<div class="bg-purple-50 border border-purple-200 rounded p-4 mt-8">
				<strong>One little note:</strong> In our event sourced code, we're storing a lot 
				of extra stuff in our events. That's because storage is cheap, and we're only
				projecting what we need, so it's best to just store everything you can
				possibly imagine needing in the future.
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
				
				Now our last requirement comes in:
				
				> Pages designated as “important” need changes to be approved before they go live
				
				Let's get going!
				
			</x-markdown>
		</div>
		
		<x-tale.examples
			:traditional="md_path('tale/003-traditional.md')"
			:event-sourced="md_path('tale/003-event-sourced.md')"
		/>
	
	</x-slot:main>

</x-layout>
