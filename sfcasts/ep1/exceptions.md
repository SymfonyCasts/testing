# Testing Exceptional Exceptions

Do you remember when we were seeing this exception because our app *didn't* know
how hungry Maverick really was? Welp, we've fixed that, but we still need to take
care of one minor detail. Make it more obvious next time genlab throws us a curve
ball.

To do that, we're going to take a break from TDD for a moment and look at `GitHubService`.
We need `getDinoStatusFromLabels` to throw an exception when it comes across
a status label that doesn't exist in `HealthStatus`. In the foreach loop,
if a label has the "Status:" prefix, we chop that off and set what's left on `$status`.
I think this would be a good spot to throw an exception if `HealthStatus` returns
`null`.

Cut `HealthStatus::tryFrom($status)` from the return and right above add `$health =` 
and paste.
When we call `tryFrom($status)` we'll either get back a `HealthStatus` or `null`.
So say `if (null === $health)` then `throw new \RuntimeException()`. For the message,
`sprintf(%s is an unknown status label!)` and pass in `$status` and then return 
`$health`.

But, if the issue doesn't have a status label, we still need to return a `HealthStatus`.
So above replace `$status` with `$health = HealthStatus::Healthy`, because unless
GenLab adds a "Status: Sick" label, all of our dinos are healthy.


In `GithubServiceTest`, lets add a test for the exception. Hmm... This first test
has a lot of the logic we'll need to do that. Copy the test and paste it at the bottom.
Then change the name to `testExceptionThrownWithUnknownLabel` and remove the method
arguments. Inside, remove the assertion leaving just the call to
`$service->getHealthReport()` and instead of `$dinoName`, pass in `Maverick`. In 
`$mockResponse`, remove Daisy from `willReturn` and change Mavericks label from
`Healthy` to `Drowsy`.

Alrighty, lets give this a shot:

```terminal
./vendor/bin/phpunit
```

And... Ouch! `GithubServiceTest` failed because of a:

> RuntimeException: Drowsy is an unknown status label!

This is actually *good* news. It means `GithubService` is doing exactly what we want
it to do. But, how do we make this test pass?

In the test, right before we call `getHealthReport()`, add `$this->exceptException()`
passing in `\RuntimeException::class`.

And... Try the tests again:

```terminal-silent
./vendor/bin/phpunit
```

Um... awesome sauce! 10 tests and 16 assertions are all passing!

Hmm... If we did manage to dork up our code on accident, a `RuntimeException`
*could* be coming from someplace else. To make sure we're testing the *correct*
exception, say `$this->exceptExceptionMessage('Drowsy is an unknown status label!')`.

Then run our spell checker again:

```terminal-silent
./vendor/bin/phpunit
```

And... HA! We've added another assertion that is passing and we don't have any
typo's in our message. WooHoo!

Along with `expectsExceptionMessage`, PHPUnit has expectations for the exception
code, object, and even the ability to pass a regex to match the message.
By the way, all of these `expect` methods are just like the `assert` methods. 
The big difference is that they must be called *before* the action you're testing
rather than after. And just like assertions, if we change
the expected message from `Drowsy` to `Sleepy` and run the test:

```terminal-silent
./vendor/bin/phpunit
```

Hmm... Yup! We'll see the test fail because `Drowsy` is not `Sleepy`. Let's change
that back in the test and there you have it... Dinotopia's gates are now open and
Bob is much happier now that our app is updated in real-time with GenLab! To 
celebrate, let's make *our* lives a bit easier by using a touch of Http Client
magic to refactor our test.
