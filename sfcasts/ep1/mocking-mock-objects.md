# Mocking: Mock Objects

Our tests are passing, the dino's are wandering, and life is great! But let's
think about this for a second. In our `GithubService`, we are able to control
the `$response` that we get back anytime we call the `request()` method. Then
we are testing that our service handles that response correctly. Something that
is as equally important as the response we are getting back, is *how* we telling
the client to *ask* for that data.

In our `GithubServiceTest` where we configure our `$mockHttpClient`, add a
method call to `->expects()`. This requires us to pass an `InvocationRule` as an
argument. While there are many to choose from, use `once()`.

In the terminal, run our tests...

```terminal
./vendor/bin/phpunit
```

And... Awesome! We now know for sure that we are calling the method `request` on
our mock exactly *one* time. Let's take it one step further and call `->with()`
on `$mockHttpClient`. For the arguments, pass in `GET` and the GitHub API URL -
`https://api.github.com/repos/SymfonyCasts/dino-park/` then run the tests again.

```terminal-silent
./vendor/bin/phpunit
```

And... Huh! We have 2 failures:

> Failed asserting that two strings are equal


Ah Ha! I didn't copy the complete URL. Back in our test at the end of the URL,
add `/issue`. Let's make sure that the tests are passing.

```terminal-silent
./vendor/bin/phpunit
```

And... Yes! We're green all day...

Although, what if we actually wanted to call the `request` method *more* that just once?
We could use any of the `InvokationRule`'s provided by PHPUnit. Let's change `once()`
to `never()` and run our tests again.

```terminal-silent
./vendor/bin/phpunit
```

Hm... Yup, we have two failures because:

> HttpClientInterface::request() was not expected to be called.

That's really nifty! Change the `expects()` back to `once()` and just to be sure
we're didn't break anything - run our tests again.

```terminal-silent
./vendor/bin/phpunit
```

And... Awesome!

We *could* call `expects()` on our `$mockResponse` to make sure that `toArray()`
*is* being called in our service. We don't really care if this method is called,
because we already have other tests in place that would fail if we *didn't* call
`toArray()`. Let's just pick and choose our battles and not worry about
that for now. We can always come back later and add that if needed.

Now that are service is fully tests and we have the up most confidence that it
is working. Open the `MainController` and in our `index()` method, add a
`GithubService $github` argument. Symfony will automatically find our service
and inject it into this method anytime it's called.

Right below our dino's array add a `foreach()` loop for `$dinos as $dino` and
then we'll `$dino->setHealth()` by calling `$github->getHealthReport()` using
`$dino->getName()`.

Over in the browser, refresh...

And... What!

> getDinoStatusFromLabels must be HealthStatus, null returned

Hmm... What is going on here. I can't really tell *why* we're getting this error.
In a future tutorial, we'll actually be able to add a test for this, but for now
it looks like one of our dino's has a status label that we didn't account for.
Let's take a peak back at the issues on GitHub and... HA! There's the problem.
Of course... `Dennis` has a `Status: Hungry` label.

In our `HealthStatus` enum, we *don't* have a case for `Hungry`. Go figure. Next
up, we'll take a look at how we can provide a better exception when this happens...
