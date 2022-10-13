# Mocking: Mock Objects

Our tests are passing, the dino's are wandering, and life is great! But... let's
think about this for a second. In `GithubService`, when we test `getHealthReport()`,
we're able to control the `$response` that we get back from `request()` by using
a stub. But we also need to ensure that the service is only calling GitHub one time,
*and* it's using the right HTTP method with the correct URL. How do we do that?

In `GithubServiceTest` where we configure the `$mockHttpClient`, add `->expects()`,
and pass in the `once()` `InvocationRule` needed for this method.

Over in the terminal, run our tests...

```terminal
./vendor/bin/phpunit
```

And... Awesome! We've just added an assertion to our mock client that requires the
`request` method to be called *exactly* one time. Let's take it a step further and
add `->with()` to the `$mockHttpClient`. For the arguments, pass in `GET` and the
URL for GitHub: `https://api.github.com/repos/SymfonyCasts/dino-park/`.

Let's try the tests again...

```terminal-silent
./vendor/bin/phpunit
```

And... Huh! We have 2 failures:

> Failed asserting that two strings are equal


Hmm... Ah Ha! My copy and paste skills, are a bit weak... I missed `/issue`
at the end of the URL. Go ahead and add that.

I think that should do the trick, let's find out...

```terminal-silent
./vendor/bin/phpunit
```

Umm... Yes! We're green all day... But best of all, we are 100% certain that we
are using the correct URL and HTTP Method when we call GitHub.

But... What if we actually wanted to call GitHub *more* that just once? Or not at all?
PHPUnit has us covered... There are a handful of `InvocationRule`'s that we can use.
Change `once()` to `never()`.

And watch what happens now:

```terminal-silent
./vendor/bin/phpunit
```

Hm... Yup, we have two failures because:

> request() was not expected to be called.

That's really nifty! Change the `expects()` back to `once()` and just to be sure
we didn't break anything - run the tests again.

```terminal-silent
./vendor/bin/phpunit
```

And... Awesome!

We *could* call `expects()` on our `$mockResponse` to make sure that `toArray()`
*is* being called in our service. But, we already have other tests in place that
would fail if we *didn't* call that method. Let's just pick and choose our battles,
we can always add that later if we need to.

Now that `GithubService` is fully tested, let's keep the dashboard updated in realtime. On
`MainController::index()`, add argument: `GithubService $github`. Anytime someone
visit's the dashboard, Symfony will automatically find our service and inject it
into this method.

Right below the `$dinos` array, add a `foreach()` loop and for `$dinos as $dino`,
say `$dino->setHealth()` by calling `$github->getHealthReport()` using
`$dino->getName()`.

To the browser and refresh...

And... What!

> getDinoStatusFromLabels must be HealthStatus, null returned

Hmm... What is going on here. I can't really tell *why* we're getting this error.
In a future tutorial, we'll actually be able to add a functional test for this, 
but for now it looks like one of our dino's has a status label that we don't 
know about. Let's take a peak back at the issues on GitHub and... HA! "Dennis"
is causing problems yet again. Apparently he's a bit hungry...

In our `HealthStatus` enum, we *don't* have a case for `Hungry` status labels. 
Go figure. Next up, we'll see how we can provide a better exception when this 
happens since its best to leave the investigative work to GenLab...
