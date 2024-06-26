<x-layout title="Digging into Laravel Relationships">
	<h1 class="text-5xl lg:text-6xl font-bold font-slant text-gray-800">
		Digging into Laravel Relationships
	</h1>
	
	<div class="bg-blue-100 p-4 border border-blue-200 rounded my-4 flex items-center">
		<div class="w-10">
			<svg class="w-6 h-6 text-blue-800 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
				<path d="M256 8C119.043 8 8 119.083 8 256c0 136.997 111.043 248 248 248s248-111.003 248-248C504 119.083 392.957 8 256 8zm0 448c-110.532 0-200-89.431-200-200 0-110.495 89.472-200 200-200 110.491 0 200 89.471 200 200 0 110.53-89.431 200-200 200zm0-338c23.196 0 42 18.804 42 42s-18.804 42-42 42-42-18.804-42-42 18.804-42 42-42zm56 254c0 6.627-5.373 12-12 12h-88c-6.627 0-12-5.373-12-12v-24c0-6.627 5.373-12 12-12h12v-64h-12c-6.627 0-12-5.373-12-12v-24c0-6.627 5.373-12 12-12h64c6.627 0 12 5.373 12 12v100h12c6.627 0 12 5.373 12 12v24z" />
			</svg>
		</div>
		<div class="flex-1">
			I've been spending some time digging into the anatomy of Laravel relationships. I'm
			using this a place to keep notes that I may some day work into a more useful article.
		</div>
	</div>
	
	<x-markdown>
	
	### Model > HasRelationships
	This trait is more related to the relationship attributes and instances—setting,
	getting, checking the existence of, etc. The other functionality revolves around
	instantiating `Relation` objects and guessing key names/etc when not provided.
	
	#### $relations
	This is an array that holds all the loaded relationships for the model.
	
	#### $touches
	This is a list of all relationships that should be "touched" when the model is saved.
	
	#### static function resolveRelationUsing($name, Closure $callback)
	This lets you define relationships outside of the model itself, which are then resolved when
	`Model::__call()` is used to load the relationship. Not really recommended unless you're dynamically
	creating relationships for models inside a package.
	
	#### hasX, morphX, belongsToX, etc
	These methods are just helpers to instantiate a Relation object.
	
	#### touchOwners()
	This loops through all the `$touches` models and calls `$this->$relation()->touch()`. It
	also runs recursively, so that each relation that's touched also calls `touchOwners()`
	on all its configured `$touches`.
	
	### Eloquent Builder
	
	#### $eagerLoad
	This is the array of relationships that need to be eager loaded. They're set using `with()`.
	
	#### eagerLoadRelations()
	Calls protected method `eagerLoadRelations` for each `$eagerLoad`
	
	#### eagerLoadRelation()
	
	1. Get the relation via `getRelation()` (which loads it without constraints and
	applies any nest `with()` statements as needed)
	
	2. Calls `addEagerConstraints()` on the relation
	
	3. Applies any custom constraints to the relationship query that were passed in as
	arguments to the `with()` call (just calls the closure)
	
	4. Matches the results back to the parent models by calling `match()` on the relation
	
	### Eloquent Builder > QueriesRelationship
	
	#### has()
	The `has()` method is used under the hood for `whereHas`/`orHas`/`doesntHave`/etc. It's
	responsible for checking for the existence or count of a relation inside a model query.
	
	The basic anatomy of a `has()` call:
	
	1. Get an instance of the relation by calling `getRelationWithoutConstraints()`—this specifically
	loads the relationship without any default constraints applied.
	
	2. Call `getRelationExistenceQuery()` or `getRelationExistenceCountQuery()` depending on
	whether the `$count` matters (if we're doing `>= 1` then `where exists` is the same
	semantics but a faster query, and if we're doing `< 1` then the same is true for
	`where not exists`).
	
	3. If a callback was added, apply the callback using `Builder::callScope` which is
	responsible for merging in `where` statements/etc. The most important thing to note
	here is that if our callback adds additional `where` statements to the query, the
	`callScope` method will wrap them in a group so that `or where` statements don't impact
	the rest of the query.
	
	4. Call `addHasWhere` to add our newly instantiated subquery to the builder:
	
	1. This first calls `Builder::mergeConstraintsFrom` to ensure that any custom
	constraints that are defined in the model's `newBaseQueryBuilder` or
	`newEloquentBuilder` methods are properly applied.
	
	2. Then it either calls `addWhereExistsQuery` or `addWhereCountQuery` using the same
	heuristics from above, adding the either a `Where` or `Exists` statement to the
	underlying query builder.
	
	#### withAggregate()
	The `withAggregate()` method is responsible for all the `withCount`/`withMax`/etc calls:
	
	## Relation Interface
	These are the relationship methods that seem to actually matter to outside callers:
	
	- `Relation::noConstraints` — tells relationships to load without the default constraints. This
	sets a static property that relationships have to check during `__construct()`
	- `getRelationExistenceQuery` — gets the version of the relationship for checking the existence
	of the related models. This is usually a `where exists ( ... )` subquery.
	- `getRelationExistenceCountQuery` — same as above but for when the count matters
	- `getRelated` — get the model for use in the relationship
	- `getRelationCountHash` — get an alias name that can be used when joining a table to itself
	or for other cases where a distinction needs to be made between multiple references to the
	same table. Generally results in `"laravel_reserved_X"` where `X` is incremented each time.
	- `getResults` — get the results of the query in a non-eager call
	- `getEager` — get the results of the query in an eager call
	- `addEagerConstraints` — tells the relation to apply eager constraints to the query
	- `match` — responsible for matching results back to parent models
	- `initRelation` — initialize the relation before query. In the case of a hasMany, for instance,
	this will set up an empty Eloquent Collection
	- `touch` — updates the `updated_at` timestamp for the related model
	- `getQuery` — returns the underlying **Eloquent** query builder (`getBaseQuery` gets the underlying
	base query builder, but it doesn't seem like that's used anywhere in the framework)
	
	## General Notes
	In `HasRelationships::morphTo` the framework uses the lack of attributes to determine if a relation
	should be instantiated in an eager/non-eager context.
	
	It's necessary to handle self-relationships where a table may be joined in multiple
	times, and therefore needs to do something like `getRelationExistenceQueryForSelfRelation`
	
	## Half-baked thoughts :)
	
	If we're trying to build a more generic implementation of relationships, the things that
	matter in particular are:
	
	- `getRelationExistenceCountQuery` (`getRelationExistenceQuery` is technically just an optimization)
	- `match`
	- `addEagerConstraints`
	
	We neeed to be able to create the following queries:
	
	```mysql
	select count(*)
	from related_table
	where related_table.local_key = parent_table.foreign_key
	```
	
	```mysql
	select *
	from related_table
	where related_table.local_key = ?
	```
	
	```mysql
	select *
	from related_table
	where related_table.local_key in (?, ?, ?)
	```
	
	This means that we need an API something like:
	
	```php
	// For simple cases where you really only need to match related instances
	// back using a single attribute
	RelatedModel::query()
	->where('special_attribute', '>', 42)
	->asRelation()
	->match($model); // same as ->matchOn($model, $model->getKeyName(), (new RelatedModel()->getForeignKey())
	
	// For complex cases where you need to handle custom logic when matching
	// results back to the eager-loaded collection
	RelatedModel::query()
	->where('special_attribute', '>', 42)
	->asRelation()
	->match(function(array $models, Collection $results, string $relation) {});
	```
	
	In non-eager contexts, `match($model)` would just add a simple constraint to the
	query builder (i.e. `where user_id = 1`). We'd need a way to indicate the direction
	of the matching (i.e. belongs to or has), which maybe would need to be a separate
	function.
	
	In eager contexts, this could easily generate the `where in()` in the same manner
	and then do the matching the same way traditional relationships work.
		
	</x-markdown>
	
</x-layout>
