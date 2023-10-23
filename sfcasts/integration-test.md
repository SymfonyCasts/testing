# Hello Integration Tests!

Hey hey, people! Welcome to episode *two* of our testing series, which is all about
*integration testing*. In episode 1, Anakin accidentally triggered the auto-pilot
on a star fighter... which then taught us all about *unit testing*! What luck!

Unt tests are the *purest* form of testing where you test *classes* and the methods
*on* those classes. And if a class requires *other* classes, you *mock* those
dependencies. It's cool and *beautiful*... and totally doesn't lead to the dark
side, I promise.

In *this* tutorial, things get *messier*, but also more useful in the right
situations! Instead of *mocking* dependencies, we're going to test with real *live*
services... which sometimes means our tests will cause *real* things to happen,
like *actual* queries to the database! That comes with all kinds of exciting
complications! And we're going to dive into all of them.

## Project Setup

But *first*, let's activate our *own* autopilot and get our app going! Testing
is fun, so download the course code from this page and code along with me. After
you unzip the file, you'll find a `start/` directory with the same code that you
see here, including this nifty `README.md` file. This has all the setup
instructions, including database setup, because we *do* have a database in this
course. If you were with us for episode one - welcome back - and be sure to download
*this* course code because we've changed a few things, like adding a database and
upgrading some dependencies.

Oh, and this tutorial uses PHPUnit 9, even though PHPUnit 10 is out.
That's fine because there aren't many user-facing changes in PHPUnit 10.

The last step in the README is to find your terminal, move into the project, and
run

```terminal
symfony serve -d
```

to start a local web server at https://127.0.0.1:8000. Click that and... here
we are! *Dinotopia*: The app where we get to see the status of the dinosaurs inside
our park. And *now*, these dinosaurs are coming from the *database*. It's not
fancy, but we have a `Dinosaur` entity. And inside our *one* controller, we
query for *all* the dinosaurs... and that's what we pass into the template...
which is what we see here.

## Checking for a "Lock Down"

Everything with the app is working great. Well... except for that one, *minor*
problem. You see, sometimes Big Eaty (that's our resident T-Rex) *escapes*, and we
don't have a way to lock down the park and notify people. *Basically*, management
is worried that *too many* guests are being eaten. So the first feature we're
going to build is a system to initiate a lockdown... and we already have an entity
for this! It's called, creatively, `LockDown`... with `$createdAt`, `$endedAt`,
and `$status` (which is an `Enum`). Inside the `Enum`, there are three cases: `ACTIVE`,
`ENDED`, or `RUN_FOR_YOUR_LIFE`. Let's... try to avoid that last one...

On our `MainController` (our *homepage*), if the most recent lockdown record in the
database has an `ACTIVE` or `RUN_FOR_YOUR_LIFE` status, we need to render a giant
warning message on the screen.

To help with this, open `src/Repository/LockDownRepository.php`. To figure
out if we're in a lockdown, add a new method called `isInLockDown()` which will
return a `bool`. For now, just `return false`.

## Creating the Test

Let's use some test driven development! Before we write this query, let's add
a test for it. We don't have a test for the `LockDownRepository` class yet, so open
`tests/`. In the first tutorial, we created a `Unit/` directory and matched the
directory structure inside of `src/` for all the classes we need to test.

This time, create a directory called `Integration/`. You don't *need* to organize
things like this, but it's fairly common to have unit tests in one directory and
integration tests in another. We haven't talked about what an integration test *is*
yet, but we'll see that in a minute.

Inside of `Integration/`, we're still going to follow the directory structure. Create
a `Repository/` directory since this class lives in `src/Repository/`... and inside,
a new PHP class called `LockDownRepositoryTest`.

Start like we always do: extend `TestCase` from PHPUnit. Call the first method
`testIsInLockDownWithNoLockdownRows()`. This will test that, if the lockdown
table is empty, then the method should return `false`.

Ok, let's keep pretending that we're living in the world of unit testing and
try to test this... like we did in the previous tutorial. To do that, say
`$repository = new LockDownRepository()`.

## Uh Oh, Instantiating this Object is Hard!

But, hmm. `LockDownRepository` extends `ServiceEntityRepository`, which extends
*another* class from Doctrine. If you look, to instantiate it, we need to pass
a `ManagerRegistry` from Doctrine. And if you hold "command" or "control" and click
into this... and go to the base class, it gets complicated. It calls
`$registry->getManagerForClass()` to get the entity manager... and it passes that
to the parent. So *already*, we're going to need to mock the registry... so that
when `getManagerForClass()` is called, it returns a mocked entity manager.

Inside our repository, we will eventually call `$this->createQueryBuilder()`. If
you dive into *that*, it uses the `_em` property (that's that entity manager that
we're planning to mock) and calls `createQueryBuilder()`, which returns a `QueryBuilder`.
So we also need to mock *this* method on `EntityManager` to return a mock
`QueryBuilder`.

This is getting crazy! We have a mock, to return a mock, to return another
mock. And ultimately, what would we assert? Would we assert that our code
calls the `->andWhere()` method on QueryBuilder with the correct arguments? Or
are we going to... somehow have the QueryBuilder generate a *real* query string...
then assert that the string... looks correct to us?

## Why A Unit Test is the Wrong Tool

No: we're going to do *none* of that. What we're seeing is a situation
where a unit test is *not* the right tool. And there are *two* reasons. First,
it's too complex! Creating a unit test will require a seemingly never-ending series
of mocks. And *second*, a unit test wouldn't be useful! If we're creating
a complex query inside of `LockDownRepository`, to make that a *truly* useful test,
we need to actually *execute* that query and make sure it returns the results we
expect *from* the database.

So, instead of creating a fresh `LockDownRepository` with a bunch of mocks, we're
going to ask Symfony to give us the *real* `LockDownRepository`: the one that we
would use in our normal code. The one that, when we call a method on it from our
test, will execute a *real* query to the database.

That's called an "integration test", and I'll show you how to do it *next*.
