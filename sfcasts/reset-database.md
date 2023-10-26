# Resetting the Database

It's really common with integration tests or functional tests to talk to the database.
And we almost always need to *seed* the database before the test: to add some rows
to `LockDown` before doing the work and calling the assertions.

In the first tutorial, we talked about a testing philosophy or pattern called AAA:
Arrange, Act, and Assert. With an integration test, the Arrange step commonly involves
adding rows to your database, the Act step is where you call the method and then
Assert is, of course, the assertions at the end.

## Loading Fixtures?

There are two approaches to seeding your database in a test. The first is to
write code *inside* the test to insert all the data you need. The second is to
create and run a set of fixtures.

And our app *does* have fixtures that power our local site. Should we... load
those from inside our test so that it starts with some data in a predictable state?

This sounds nice! But... don't do it! Don't load fixtures in your tests. Why?
Because a good test reads like a story: you should be able to read what data is
added, what method is called, and what behavior is expected.

If you load a set of fixtures... then suddenly assert that we're in a lockdown, it's
not super obvious *why* we're in a lockdown... or what we're even testing! You need
to go dig into the app fixtures to find which LockDown records there are... and
figure out what's going on. I do *not* like that.

So, even though it might feel like a bit more work, the best strategy is to insert
the data you need inside *each* test method. And after the next chapter, it
actually *won't* be much work.

## Clearing the Data

Even more importantly, no matter how you seed your database, we need to make
sure that, before each test starts, the database is *empty*. And we just saw why.

Our original test passed... until our second test inserted a row... which caused
the first to suddenly fail. Boo. Unless your database is in a *perfectly* predictable
state at the start of *each* test, you can't trust them! And the best way to be
predictable is to start empty!

We could override the `setUp()` method and run code here that does that. Fortunately,
we don't need to because there are *multiple* libraries that already solve this
problem. My favorite is Foundry.

## Installing zenstruck/foundry

Run:

```terminal-silent
composer require zenstruck/foundry --dev
```

If you watched our Doctrine tutorial, you'll remember Foundry! But you may not
know about its testing superpowers... which is where it *really* shines.

The main point of this library is to help create dummy data, and we *are* going to
talk about that soon. But it also comes with a super easy way to empty your database
between each test.

To use it, at the top of your test class, say use `ResetDatabase`... and also another
trait called `Factories`.

Run the tests:

```terminal
symfony php vendor/bin/phpunit tests/Integration/Repository/LockDownRepositoryTest.php
```

They pass! We can run them over and over and over again! Before each individual
test method, it empties the database!

By the way, there's another library that does the same thing called
`dama/doctrine-test-bundle`, which can be even *faster* than Foundry's `ResetDatabase`.
Feel free to install that - then use Foundry just for the factory stuff that we'll
talk about soon. They work great together.

## Silencing Deprecations with symfony/phpunit-bridge

Before we move on, you probably noticed that we have a bunch of deprecations!
Seeing deprecations is great... but an indirect deprecation means that it's not
*our* code that's triggering the deprecation: it's one library calling a deprecated
method on *another* library.

I'm not too worried about these... so let's silence them for the rest of the tutorial.
These deprecation warnings come from Symfony's phpunit-bridge package, and we can
*control* how they work.

Open up `phpunit.xml.dist`. Down here, inside the `php` section, add `env`
to set an environment variable called `SYMFONY_DEPRECATIONS_HELPER`. For the value,
an easy way to silence these warnings is to send them to a log file instead:
`logFile=var/log/deprecations.log`.

Close that up. Now when we run the tests:

```terminal-silent
symfony php vendor/bin/phpunit tests/Integration/Repository/LockDownRepositoryTest.php
```

Clean and tidy! And the deprecations are still waiting for us in the log file.

Next: let's leverage Foundry Factories to make seeding our database an absolute
delight!
