# KernelTestCase: Fetching Services

In our app, if we wanted to use `LockDownRepository` to make some real queries, we
could autowire `LockDownRepository` into a controller - or somewhere else - call
a method on it, and *boom*! Everything would work.

*Now* we want to do the *same* thing in our test: instead of creating the object
*manually*, we want to ask Symfony to give us the *real* service that's configured
to talk to the *real* database so it can do its *real* logic. *Really*!

## Booting the Kernel

To fetch a service inside of a test, we need to boot up Symfony *then* get access
to its *service container*: the mystical object that holds every service in your
app.

To help with this, Symfony gives us a base class called `KernelTestCase`. There's
nothing particularly special about this class. Hold "command" or "control" to see
that it extends the normal `TestCase` from PHPUnit. It just adds methods to boot
and shut down Symfony's kernel - that's kind of the heart of Symfony - and fetch
its container.

## Fetching Services

At the top of our test method, start with `self::bootKernel()`. Once you call
this, you can imagine you have a Symfony app running in the background, waiting
for you to use it. *Specifically*, this means we can grab any live *service* from
our app. Do that with `$lockDownRepository = self::getContainer()` (which is a helper
method from `KernelTestCase`) `->get()`. *Then* pass the service ID which, in our
case, is the class name: `LockDownRepository::class`.

To see if this works, `dd($lockDownRepository)`.

By the way, unit tests and integration tests, generally, look the same: you call
methods on an object and run assertions. If your test happens to boot the kernel
and grab a real service, we give it the name "integration test"... but that's just
a fancy way of saying: "A unit test... except we use real services".

Okay, let's try this! At your terminal, run:

```terminal
./vendor/bin/phpunit
```

You can also run `./bin/phpunit` - which is a shortcut setup for Symfony. But I'll
stick to running `phpunit` directly.

And... yes! *There's* our service! It doesn't look like much, but this lazy object
is something that lives in the *real* service.

## The Special Test Service Container

So, simple! `self::getContainer` gives us the *service container*... and then we
call `get()` on it. But I *do* want to point out that accessing the service container
and grabbing a service from it is *not* something we do in our *application* code.
For most services, which are private, doing this won't even work! Instead, we rely
on dependency injection and *autowiring*.

But in a test, there *is* no dependency injection or autowiring. So, we *need* to
grab services like this. And the only reason this *works* is because
`self::getContainer()` gives you a *special* container that *only* exists in the
`test` environment. It's *special* because it *does* allow you to call a `get()`
method and ask for *any* service you want by its IDs. So this *is* a unique
superpower to the `test` environment.

## Running Code & Asserting

Ok, since we have `LockDownRepository`, let's try running a *very* simple test.
Notice that, if I try to call a method on this, I don't get the correct autocompletion.
That's because my editor doesn't know *what* the `get()` method returns. To help
it,  `assert()` that `$lockDownRepository` is an `instanceof LockDownRepository`.
This *isn't* a PHPUnit assertion: we didn't say `$this->assert`-something. This is
just a PHP function that will throw an exception if `LockDownRepository` is *not*
a `LockDownRepository`. It *will* be... and this code will never cause a problem...
but now our editor *will* autocomplete the right methods.

Say `$this->assertFalse($lockDownRepository->isInLockDown())`.

The idea is that we haven't added any rows to the database... and *because* of that,
we should *not* be in lockdown. And since the method just returns false right now...
this test *should* pass:

```terminal-silent
./vendor/bin/phpunit
```

And... it *does*! So we're using the real service... but it's not, yet, making any
queries. Will this keep working if we *do* make a query? Let's find out, *next*.
