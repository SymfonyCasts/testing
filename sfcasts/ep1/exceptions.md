# Testing Exceptional Exceptions

Okay.

All right. So I think the best way to handle, uh, for us to become aware if gen lab
creates any new status labels that we don't know about yet, like say status drowsy is
to throw an exception in our code.


We're going to take a break from TDD for a moment and look at `GitHubService`.
We need `getDinoStatusFromLabels` to throw an exception when it comes across
a status label that doesn't exist in the `HealthStatus` enum. In the foreach loop,
if a label doesn't start with `Status:` we move on with life. Otherwise we chop
off the `Status:` prefix and set whats left on `$status`. This is the spot where
we need to throw an exception.

Cut `HealthStatus::tryFrom($status)` from the return and add `$health =` and paste.
When we call `tryFrom($status)` we'll either get back a `HealthStatus` or `null`.
So say `if (null === $health)` then `throw new \RuntimeException()`. For the message,
`sprintf(%s is an unknown status label!)` and pass in `$status`. Below return `$health`.

But, if the issue doesn't have a status label, we still need to return a `HealthStatus`.
So above replace `$status` with `$health = HealthStatus::Healthy` because unless
GenLab adds a "Status: Sick" label, all of our dinos are healthy.


Now we need a test for this. In `GithubServiceTest`, this first method has alot of
the logic we'll need to test our exception. Copy the test and paste it at the bottom.
Then change the name to `testExceptionThrownWithUnknownLabel` and remove the method
arguments, since we won't need a data provider. Inside, chop of the assertion leaving
just the call to `$service->getHealthReport()`. Instead of `$dinoName`, pass in
`Maverick`. In `$mockResponse`, remove Daisy from `willReturn` and change Mavericks label from
`Healthy` to `Drowsy`.

Move to the terminal and test with:

```terminal
./vendor/bin/phpunit
```

And... Ouch! `GithubServiceTest` failed because:

> RuntimeException: Drowsy is an unknown status label!

This is actually good news. It means service is doing exactly what we want it to.
But, how do we make this test pass?

In the test, right before we call `getHealthReport()`, add `$this->exceptException()`
passing the name of `\RuntimeException::class`.

Try the tests again:

```terminal-silent
./vendor/bin/phpunit
```

Um... Yes! 10 tests and 16 assertions are all passing!

Hmm... If we did manage to dork up our code on accident, a `RuntimeException`
*could* be coming from someplace else. To make sure we're testing the *correct*
exception, add `$this->exceptExceptionMessage('Drowsy is an unknown status label!')`.

Then run our spell checker again:

```terminal-silent
./vendor/bin/phpunit
```

And... HA! We've added another assertion that is passing and we don't have any
typo's!

Along with the message assertion, PHPUnit also has expectations for an exceptions
code, the exception object, and even the ability to pass a regex to match the message.
By the way, all of these `expect` methods are basically the same thing as the
`assert` methods. But, the need to be called *before* you trigger an action rather
than *after*. Like `getHealthReport()`. And just like assertions, if we change
the expected message from `Drowsy` to `Sleepy` then run the test:

```terminal-silent
./vendor/bin/phpunit
```

Hmm... Yup! We'll see the test fail because `Drowsy` is not `Sleepy`. Let's change
that back in the test and there you have it...

I think it's time for a bit of refactoring in this service test. Hmm... Ah, thats
right, Symfony can help us out with that in a big way. We'll do that next!
