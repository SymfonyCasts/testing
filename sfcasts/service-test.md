# Testing a Service

If you click this button to end the lockdown... it hits a `die` statement. I
created a controller... but got lazy...

To end a lockdown, we need to find the active lockdown, change its status to ended,
and save it to the database. Easy peasy. But instead of putting that logic inside
our controller, let's create a service.

## Creating the Service

We *could* use TDD, but I'm going to create the class quickly, and *then* we'll test:
it'll be easier to understand.

Inside `src/Service/`, add a new `LockdownHelper` class. I'll paste in
some logic... because it's beautifully boring. We have a method called
`endCurrentLockDown()`, it calls a `findMostRecent()` method on the repository,
sets the status to `ENDED` and flushes. Up here, we autowire `LockdownRepository`
and `EntityManagerInterface`.

The `findMostRecent()` method doesn't exist yet on the repository. So open
`LockDownRepository`... and let's do some refactoring. Create a new public function
called `findMostRecent()`, which will return a nullable `Lockdown`. Then grab the
code from below, paste, return that and call it: `$lockdown` equals
`$this->findMostRecent()`.

And yes, you could create an integration test for `findMostRecent()`, but we'll skip
it.

Back over in `LockDownHelper`... this is happy! Before we use this class, let's test it!

## Unit Test? Or Integration Test?

The first question is, do we need a unit test or an integration test? And honestly,
*either* would be fine. We could do a unit test, mock `LockdownRepository`,
make sure `findMostRecent()` is called, and that it sets the status to `ENDED`
and calls `flush()` on the entity manager. So yea, a unit test *would* be ok: the
mocking isn't too complicated... and it *would* test the logic pretty well.

Or we can write an integration test, which will run a bit slower, but be more
realistic. For the sake of this tutorial, let's do an integration test. And also,
you *could* have both. Heck, there's nothing stopping you from booting the kernel
in one test method... and using mocks in another test method in the same class.
Mocks and the container are two different tools to help you get your work done.

In the `Integration/` directory, create a new `Service/` directory... then a new
PHP class: `LockdownHelperTest`. This time, go straight to extending `KernelTestCase`,
then use our two favorite traits: `use ResetDatabaseTrait` and `Factories`. Since
we'll use these traits in *every* integration test, you can also create a base
class. Somewhere inside of `tests/`, you could create an abstract
`BaseKernelTestCase`, put the traits there, then have all of your integration tests
extend *that*.

Down here, let's whip up our test: `testEndCurrentLockdown()`. And we know how to
start: `self::bootKernel()`.

Let's think. If we're going to end a lockdown... we need an active `LockDown`
in the database. Say `$lockdown` equals `LockDownFactory::createOne()`... and
pass `status` set to `LockDownStatus::ACTIVE`.

Since we know our database will start empty, we know *this* will be the item
that our query returns. Down here, grab the `$lockDownHelper` with
`self::getContainer()->get(LockDownHelper::class)`... and use the `assert()` trick
to tell our editor that this is an `instanceof` `LockDownHelper`.

With the "Arrange" part of the test done, let's act:
`$lockDownHelper->endCurrentLockDown()`.

With any luck, this record *should* have just changed its status in the database.
To prove it, assert that `LockDownStatus::ENDED` equals `$lockDown->getStatus()`.

## Auto-Refreshing in Action

That's a good-looking test! Though there is one tiny detail I should mention. First...
I'm going to tell a lie. By checking `$lockDown->getStatus()`, we're actually *only*
checking that this `LockDown` *object* had its status changed by the code... we're
not *actually* testing whether its new value was *saved* to the database. To test
that, we could make a fresh query to the database, like via
`LockDownFactory::repository()`... then find the most recent. We'll talk more about
the repository shortcut later.

Now, for the truth. You *should* be thinking critically about what you're testing
or not testing like we just did. *However*, because we created the `$lockDown`
variable through Foundry, it's wrapped in a `Proxy`. One of the *main* features
of a `Proxy` is called "auto-refreshing". Each time you access a property or call
a method on your entity, behind the scenes, Foundry queries for the *latest* data
from the database and sets it. So if we *hadn't* flushed the status change to the
database, the test would have *failed*. Foundry actually would have seen that we
had *unsaved* changes on that entity, and would have yelled at us. Pretty cool.

## Inlined or Removed Services?

Ok, let's try this thing! Run:

```terminal
symfony php vendor/bin/phpunit tests/Integration/Service/LockDownHelperTest.php
```

And... what the heck? It says:

> The `LockDownHelper` service or alias has been removed or inlined when
> the container was compiled.

What does that mean? Ok, a *really* cool thing about Symfony's service container
is that if a service isn't used by *anything* in your app, it's *removed* from the
container... which makes our app leaner and meaner.

In our actual *application* code, like controllers, repositories & services, nobody
is using the `LockDownHelper` service. We're not autowiring it into a controller
or a service anywhere. And so, Symfony *removes* this from the container... which
means that it's *not* there in the test.

The fix for this is... just to make sure it's used somewhere! I mean, if we're
writing this code, certainly we intended to... ya know, *use* it.

In the `endLockDown()` action, autowire `LockDownHelper $lockDownHelper`... and I'm
not even going to call anything on it yet. Just having it here will be enough.

And now:

```terminal-silent
symfony php vendor/bin/phpunit tests/Integration/Service/LockDownHelperTest.php
```

The test passes! Woo!

Let's use it: call `$lockDownHelper->endCurrentLockDown()`... then redirect back
to the homepage.

Let's try it! Refresh, we're in a lockdown... "End Lockdown"... it's gone. All
the dinos are back in their pens.

Next: I'm going to complicate things by introducing a situation that will make us
want to unit test *and* integration test `LockDownHelper`... at the same
time. That'll lead us to something I call "partial mocking".
