# Kernel Test Case

In our application, if we wanted to use `LockDownRepository` to make some real queries, we can do that. We know that we can just autowire `LockDownRepository` into a controller or somewhere else, call a method on it, and *boom*! Everything *works*. *Now* we want to do the *same* thing in our test. Instead of creating the object *manually*, we want to ask Symfony to give us the *real* service that's configured to talk to the *real* database so it can do its *real* logic. *Really*!

To fetch a service inside of Symfony, we need to boot Symfony like normal, and *then* get access to its *service container*. To help with that process, Symfony gives us a helper base class called `KernelTestCase`. There's nothing particularly special about this class. If you hold "command" or "control", you can see that it extends the normal test case from PHPUnit, and it just has methods in it to boot and shut down Symfony's kernel and fetch its container. *But* it also allows us to do something specific. At the top of our test method, we can say `self::bootKernel()`. This boots Symfony in the background, and now its container is *live* and full of real services. We can then grab our service by saying `$lockDownRepository = self::getContainer()` (which is a helper method from `KernelTestCase`) `->get()`. *Then* we're going to pass it the service ID which, in our case, is the class name - `LockDownRepository::class`. And to see what this does, let's add `dd($lockDownRepository)` below it.

By the way, this is an easy way to spot a unit test versus an integration test. A unit test is going to extend `TestCase`, and an integration test is going to extend `KernelTestCase`, because it wants to access *real* services.

Okay, let's run our test! at your terminal, run:

```terminal
./vendor/bin/phpunit
```

Or if you prefer, you can just run

```terminal
./bin/phpunit
```

which is a little shortcut that's set up from Symfony.For tutorial purposes, I'll just run `phpunit` directly. And... yes! *There's* our service! It doesn't look like much, but this lazy object is actually coming from the live service itself, so *this* *is* our real live service.

One thing I want to point out is that `self::getContainer` gives us the *service container*, and then we call the method `get()` on it. This *isn't* something that we do in our normal code. For the most part, it's not *possible* inside of Symfony, or at least it's not something that we *do* or *should do*. If we said something like `$repository = $container->get(LockDownRepository::class)` to get access to the entire container... *that's* just *not* something that we do in our code. We rely on dependency injection - *autowiring*. And for the most part, if we tried that, it *wouldn't* work inside of your application code.

So the key takeaway here is that this `self::getContainer()` gives you a *special* container *only* exists in the test environment. And it's *special* because it *does* allow you to just call a `get()` method and ask for any service you want by its IDs. This is pretty unique to the test environment, but it's *super* convenient.

All right, since we have this `LockDownRepository`, let's try running a *very* simple test with it. You'll notice that, if I call methods on this, it's not going to autocomplete the correct one. That's because my editor doesn't know what this actually returns. Instead, we're going to `assert()` that `$lockDownRepository` is an `instanceof LockDownRepository`. You may have noticed that this *isn't* a PHPUnit assertion. We didn't say `$this->assert`-something. This is just a little PHP function that will throw an exception if `LockDownRepository` is *not* a `LockDownRepository`. It *will* be and this code will never cause a problem. We're really just doing this as a fancy way to help our editor, because we now have all of our methods in there.

Okay, *now* let's say `$this->assertFalse($lockDownRepository->isInLockDown()`. The idea here is that we haven't added any rows to the database, and *because* of that, we should *not* be in lockdown. And since we're just returning false right now, this test *is* going to pass. And... it *does*.

So we're using the real service, but we're not actually making any queries yet. Will this keep working if we actually try to make a query? Let's find out, *next*.
 
