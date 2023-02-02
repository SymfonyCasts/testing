# Mocking: Mock Objects

Our tests are passing, the dino's are wandering, and life is great! But... let's
think about this for a second. In `GithubService`, when we test `getHealthReport()`,
we're able to control the `$response` that we get back from `request()` by using
a stub. That's great, but it might also be nice to ensure that the service is only
calling GitHub one time *and* that it's using the right HTTP method with the 
correct URL. Could we do that? Absolutely!

## Expect a Method to Be Called

In `GithubServiceTest` where we configure the `$mockHttpClient`, add `->expects()`,
and pass `self::once()`.

[[[ code('d4cd801092') ]]]

Over in the terminal, run our tests...

```terminal
./vendor/bin/phpunit
```

## Expecting Specific Arguments

And... Awesome! We've just added an assertion to our mock client that requires the
`request` method be called *exactly* once. Let's take it a step further and
add `->with()` passing `GET`... and then I'll paste the URL to the GitHub API.

[[[ code('47a458cb01') ]]]

Try the tests again...

```terminal-silent
./vendor/bin/phpunit
```

And... Huh! We have 2 failures:

> Failed asserting that two strings are equal

Hmm... Ah Ha! My copy and paste skills are a bit weak. I missed `/issue` at the
end of the URL. Add that. 

[[[ code('4d846bbca3') ]]]

Let's see if that was the trick:

```terminal-silent
./vendor/bin/phpunit
```

Umm... Yes! We're green all day. But best of all, the tests confirm we're using
the correct URL and HTTP method when we call GitHub.

But... What if we actually wanted to call GitHub *more* than just once? Or... we 
wanted to assert that it was not called at all? PHPUnit has us covered. There are
a handful of other methods we can call. For example, change `once()` to `never()`.

And watch what happens now:

```terminal-silent
./vendor/bin/phpunit
```

Hmm... Yup, we have two failures because:

> request() was not expected to be called.

That's really nifty! Change the `expects()` back to `once()` and just to be sure
we didn't break anything - run the tests again.

```terminal-silent
./vendor/bin/phpunit
```

And... Awesome!

## Carefully Applying Assertions

We *could* call `expects()` on our `$mockResponse` to make sure that `toArray()`
is being called exactly once in our service. But, do we really care? If it's
not being called at all, our test would certainly fail. And if it's being called
twice, no big deal! Using `->expects()` and `->with()` are *great* ways to add
extra assertions... when you need them. But no need to micromanage how many times
something is called or its arguments if that is *not* so important.

## Using GitHubService in our App

Now that `GithubService` is fully tested, we can celebrate by *using* it to drive
our dashboard! On `MainController::index()`, add an argument: 
`GithubService $github` to autowire the new service.

[[[ code('d3adeb62ba') ]]]

Next, right below the `$dinos` array, `foreach()` over `$dinos as $dino` and, inside
say `$dino->setHealth()` passing `$github->getHealthReport($dino->getName())`.

[[[ code('27580fbe41') ]]]

To the browser and refresh...

And... What!

> `getDinoStatusFromLabels()` must be `HealthStatus`, `null` returned

What's going on here? By the way, the fact that our unit test passes but our
page fails *can* sometimes happen and in a future tutorial, we'll write a
functional test to make sure this page actually loads.

The error isn't very obvious, but I think one of our dino's has a status label
that we don't know about. Let's peek back at the issues on GitHub and... HA! "Dennis"
is causing problems yet again. Apparently he's a bit hungry...

In our `HealthStatus` enum, we *don't* have a case for `Hungry` status labels.
Go figure. Is a hungry dinosaur accepting visitors? I don't know - I guess it
depends on if you ask the visitor or the dino. Anyways, `Hungry` is *not* a status 
we expected. So next, let's throw a clear exception if we run into an unknown 
status and test for that exception.
