# Testing Exceptional Exceptions

Do you remember when we were seeing this exception because our app *didn't* know
how hungry Maverick really was? Welp, we've fixed that, but we still need to take
care of one minor detail. Next time GenLab throws us a curve ball, like setting 
"Status: Antsy" on a dino, `GithubService` should show us the that label in the
exception.

## Where can we throw an exception?

To do that, we're going to take a break from TDD for just a moment. In 
`getDinoStatusFromLabels()`, if a label has the "Status:" prefix, we chop that 
off, set what's left on `$status`, and pass that into `tryFrom()` so we can
return a `HealthStatus`. I think this would be a good spot to throw an exception
if `tryFrom()` returns `null`.

Cut `HealthStatus::tryFrom($status)` from the return and right above add `$health =` 
and paste. Then `if (null === $health)` we'll `throw new \RuntimeException()` with 
the message, `sprintf(%s is an unknown status label!)` passing in `$status` and then
below return `$health`.

But, if the issue *doesn't* have a status label, we still need to return a `HealthStatus`.
So above replace `$status` with `$health = HealthStatus::HEALTHY`, because unless
GenLab adds a "Status: Sick" label, all of our dinos *are* healthy.

## Is the exception thrown?

Let's add a test for the exception in `GithubServiceTest`. Hmm... This first test
has a lot of the logic we'll need to do that. Copy the test and paste it at the bottom.
Change the name to `testExceptionThrownWithUnknownLabel` and remove the method
arguments. Inside, remove the assertion leaving just the call to
`$service->getHealthReport()` and instead of `$dinoName`, pass in `Maverick`. 
For `$mockResponse`, remove Daisy from `willReturn()` and change Mavericks label from
`Healthy` to `Drowsy`.

Alrighty, lets give this a shot:

```terminal
./vendor/bin/phpunit
```

And... Ouch! `GithubServiceTest` failed because of a:

> RuntimeException: Drowsy is an unknown status label!

This is actually *good* news. It means `GithubService` is doing *exactly* what we
want it to do. But, how do we make this test pass?

Right before we call `getHealthReport()`, add `$this->exceptException()`
passing in `\RuntimeException::class`.

And... Try the tests again:

```terminal-silent
./vendor/bin/phpunit
```

Um... awesome sauce! 10 tests and 16 assertions are all passing!

## Prevent typo's in the exception message

Hmm... If we did manage to dork up our code on accident, a `RuntimeException`
*could* be coming from someplace else. To make sure we're testing the *correct*
exception, say `$this->exceptExceptionMessage('Drowsy is an unknown status label!')`.

Then run our spell checker again:

```terminal-silent
./vendor/bin/phpunit
```

And... HA! We've added another assertion that is passing and we don't have any
typo's in our message. WooHoo!

## Test more than the exception message

Along with `expectExceptionMessage()`, PHPUnit has expectations for the exception
code, object, and even the ability to pass a regex to match the message.
By the way, all of these `expect` methods are just like the `assert` methods. 
The big difference is that they must be called *before* the action you're testing
rather than after. And just like assertions, if we change
the expected message from `Drowsy` to `Sleepy` and run the test:

```terminal-silent
./vendor/bin/phpunit
```

Hmm... Yup! We'll see the test fail because `Drowsy` is not `Sleepy`. Let's change
that back in the test... And there you have it! Dinotopia's gates are now open and
Bob is much happier now that our app is updated in real-time with GenLab! To 
celebrate, let's make *our* lives a bit easier by using a touch of HttpClient
magic to refactor our test.
