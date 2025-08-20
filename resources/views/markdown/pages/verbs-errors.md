# Verbs errors

Error handling in Verbs can be tricky, because it's hard to know where
exactly to put your validation logic. Do you want to put it in the event (and
then lose some API niceties that Laravel provides), or put it somewhere **before**
your event gets fired, and trust that data has been validated? (Or, do you do
both and have a little duplication of code?)

Verbs has an minimally-documented `onError` helper that can help address this
issue. Let's take a look.

Imagine you had the following event:

```php
class MoneyDeposited extends Event
{
    public function __construct(
        public int $cents,
    ) {}
    
    public function validate()
    {
        $this->assert($this->cents > 0, CentsMustBePositive::class);
        $this->assert($this->cents <= 10_000_00, DepositOverLimit::class);
    }
    
    // ...
}
```

Verbs lets you easily translate that exception in your controller to 
a validation error:

```php
class BankAccountController
{
    public function deposit(Request $request) 
    {
        MoneyDeposited::make()
            // If you return an array from `onError`, it will be translated
            // to a validation exception automatically by Verbs
            ->onError(fn(Throwable $e) => match($e::class) {
                CentsMustBePositive::class => ['cents' => 'You must deposit at least one cent'],
                DepositOverLimit::class => ['cents' => 'You can only deposit up to $10,000 at a time'],
            })
            ->commit(cents: $request->integer('cents'));
    }
}
```

Now, when you hit the `BankAccountController::deposit` endpoint, you'll get nice
validation messages. But if you fire `MoneyDeposited` from anywhere else in your
app, invalid inputs will result in an exception!
