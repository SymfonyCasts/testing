# Partial Mocking

Let's make our `LockDownHelper` more *interesting*. Let's say that, when a lockdown
ends, we need to send an API request to GitHub. In our *first* tutorial, we wrote
code that made API requests to get info about this `SymfonyCasts/dino-park`
repository. Now, we're going to *pretend* that, when we end a lockdown, we need to
send an API request to find all of the issues with a "lockdown" label and *close*
them. We're not... *actually* going to do this, but we'll go through the motion
because it'll create a really interesting situation.

## This Setup: Making API Calls from our Service

In that first tutorial, we made a GitHub service that wraps the API calls. Its
one method grabs a health report for all the dinosaurs. Add a *new*
`public function` called `clearLockDownAlerts()`. Inside, pretend we're making
an API call - we don't really need to in order to hit our fun situation - then,
at least, log log a message.

Cool! We'll also pretend that we've already tested this in some way - unit tests,
integration tests, etc. The *point*: we're *confident* that this method works.

Over in `LockDownHelper`, to make our fake API calls, we'll autowire
`GithubService $githubService`... and down here, after `flush()`, say
`$this->githubservice->clearLockDownAlerts()`.

Okay! Try the test!

```terminal-silent
symfony php vendor/bin/phpunit tests/Integration/Service/LockDownHelperTest.php
```

We haven't changed anything and... it *still* passes. That makes sense. In our test,
we ask Symfony for `LockDownHelper`, so it handles passing the new `GithubService`
argument when it creates that service. And since the`GitHubService` isn't *actually*
making a real API call, everything is *fine*.

*But* what if `GithubService` *did* contain real logic to make an actual HTTP request
to GitHub? *That* could cause a few problems. First, it would definitely slow
down our test because API recalls are *slow*. Second, it might *fail* because, when
it checks the repository, we may not have *any* issues with this `LockDown` label.
And *third*, if it *does* find issues with that label, it might close them on our
*real* production repository... even though this is just a test.

Furthermore - I know, I'm on a roll - if we wanted to test that the
`clearLockDownAlerts()` was actually *called*, in an integration test, the only way
to do that is by making an API call from our test to *seed* the repository with
some issues (*creating* an issue with a `LockDown` label), calling the method, then
making *another* API request from our test to verify that the issue is closed.
*Yikes*. That's *too* much work!

## Mocking only Some Services?

I hope you're yelling at your computer at this point:

> Ryan! This is the whole point of mocking - what we learned in the first tutorial!

Yea, totally! If we mocked `GitHubHelper`, we would avoid any API calls *and*
have an easy way to assert that the method on it was called. So, darn, we basically
want to mock our *one* dependency... but use the *real* services for the *other*
dependencies. Is that possible? It is! With something I call "partial mocking".

## Injecting a Mock into the Container

When we ask the container for the `LockDownHelper` service, it instantiates the *real*
services that it needs and passes them to each of the three arguments. What we
*really* want to do is have it pass the *real* service for `$lockDownRepository`
and `$entityManager`, but a *mock* for `$githubService`. And Symfony gives us a way
to do that!

Check it out. *Before* we ask for `LockDownHelperService`, create a `$githubService`
mock set to `$this->createMock(GitHubService::class)`. Below that, say
`$githubService->expects()` and, to make sure this fails at first, use
`$this->never()`. Finish with `->method('clearLockDownAlerts')`.

If we stop now and run the test:

```terminal-silent
symfony php vendor/bin/phpunit tests/Integration/Service/LockDownHelperTest.php
```

It still passes. We created a mock, but no one is *using* it. We need to tell
Symfony:

> Hey! We want to replace the `GitHubService` in the container with this mock.

Doing that is simple: `self::getContainer()->set()` passing the ID of the service,
which is `GithubService::class`, then `$githubService`.

Suddenly, *that* becomes the service in the container, and *that* is what will be
passed to `LockDownHelper` as the *third* argument.

Try the test!

```terminal-silent
symfony php vendor/bin/phpunit tests/Integration/Service/LockDownHelperTest.php
```

Because of the `$this->never()`... it *fails*! `clearLockDownAlerts()` was *not*
expected to be called, but it *was* called... since we're calling it down here.

Change the test from `$this->never()` to `$this->once()` and try again...

```terminal-silent
symfony php vendor/bin/phpunit tests/Integration/Service/LockDownHelperTest.php
```

It *passes*! This is *such* a cool strategy when you have this situation.

Next: Let's look at how we can test if our code caused certain *external* things
to happen, starting with testing emails.
