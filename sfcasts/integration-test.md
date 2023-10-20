# Integration Test

Hey hey, people! Welcome to episode *two* of our testing series, which is all about *integration testing*. In the first episode, we talked about *unit testing*. It's the *purest* form of testing where you're testing *classes* and the methods *on* those classes. And if your classes require *other* classes, you *mock* those dependencies. It's cool and *beautiful*.

In *this* tutorial, things get *messier*, but also a little more useful in some situations. Instead of *mocking* dependencies, we're going to test with our *live* services, which sometimes means doing things like making *real* queries to a database. That comes with all kinds of exciting complications, and we're going to dive into all of that. But *first*, let's get our app going. You should *absolutely* download the course code from this page and code along with me. After you unzip the file, you'll find a start directory with the same code that you see here, including this nifty little `README.md` file. This has all of the setup instructions, including database setup, because we *do* have a database in this course. If you coded along with us for episode one, download *this* course code before you get started because we've changed a few things, like adding a database and upgrading some dependencies. This tutorial will *still* use PHPUnit 9, even though PHPUnit 10 is already out. That's totally fine because there's not that many user-facing changes in PHPUnit 10.

The last step here is to find your terminal, move into the project, and run

```terminal
symfony serve -d
```

to start the built-in web server at 127.0.0.1:8000. Click that and... here we are! *Dinotopia*: The app where we get to see the status of the dinosaurs inside of our application. And *now*, these dinosaurs are actually coming from the *database*. It's not super fancy, but we *do* have a `Dinosaur` entity. And inside of our *one* controller, we're actually querying for *all* of the dinosaurs, and that's what we're passing into our template, which is what you see here.

Everything with the app is working great, *except* for a *minor* problem where something goes wrong, like Big Eaty (our resident T-Rex) *escapes*, and we don't have a way to lock down the park and notify people. *Basically*, management is worried that *too many* guests are being eaten. So the very first feature we're going to build is a system to initiate a lockdown, and we already have an entity for this, called `LockDown`. It has `$createdAt`, `$endedAt`, and `$status` (which is an `Enum`). Inside of our `Enum`, we have three cases: `ACTIVE`, `ENDED`, or `RUN_FOR_YOUR_LIFE`. Let's try to avoid that last one...

On our `MainController` (our *homepage*), if the most recent lockdown record in the database has an `ACTIVE` status, we're going to render a giant warning message on the screen. To do this, inside of our controller, we need to make a query to find the latest lockdown and see if it's active. In `/src/Repository/LockDownRepository.php`, instead of putting this logic (which finds the latest lockdown and checks its status) *inside* of our controller, we're going to put it right here. Create a new function called `isInLockDown()` which will return a `bool`, and for now, we're just going to `return false`.

Okay, we're going to use a little bit of test driven development here, just like we did in the first episode. Before we write this query and the logic, let's write a test for it. We don't have a test for this `LockDownRepository` class yet, so open `/tests`. In the first tutorial, we had a `/Unit` directory. This time, we've matched our directory structure inside of `/rsrc` for all of the individual things that we're testing. Here, we're going to create a directory called `/Integration`. You don't *need* to organize things like this, but it's fairly common to have unit tests in one directory and integration tests in another. We haven't talked about what an integration test *is* yet, but we'll see that in a second.

Inside of `/Integration`, we're still going to follow the directory structure. Let's create a `/Repository` directory, since this class lives in a repository directory, and inside of there, we'll create a new PHP class called `LockDownRepositoryTest`. We're going to start this like we always do and extend `TestCase` from PHPUnit... and create a `public function testIsInLockDown()`. We'll begin with a super simple test. Say `testIsInLockDownWithNoLockdownRows()`. This will test to see if there are any lockdowns in the database. If there are *none*, `isInLockdown()` is going to return `false`.

All right, let's keep pretending that we're living in the world of unit testing and try to test this just like we did in the previous tutorial. To do that, say `$repository = new LockDownRepository()`. This `LockDownRepository` extends `ServiceEntityRepository`, which extends *another* class from Doctrine. If you look in here, we actually need to pass this a `ManagerRegistry` from Doctrine. And if you hold "command" or "control" and click into this and go to the base class, it gets a *little* complicated. We can see that the `$registry` is used. We take that, call `getManagerForClass()` on it to get the entity `$manager`, and then we pass the entity `$manager` to the parent. So *already*, things are getting a little complex. We'll need to *mock* the registry so that, when `getManagerForClass()` is called, it returns a mocked `$manager`. And inside of our repository, we're eventually going to call something like `$this->createQueryBuilder()`. If you dive into *that*, the way *this* is created is by going to the `_em` property (that's that entity `$manager` that we're planning on mocking) and calls `createQueryBuilder()`, which returns a QueryBuilder. So we need to mock *this* method as well to return a mock QueryBuilder or even a *real* QueryBuilder. You can see how complicated things are getting. This is going to be mock after mock after *mock*. And ultimately, at the end of the day, what are we going to be asserting? Are we going to be asserting that we're calling the *exact* `->from()` method on a QueryBuilder, and make sure this method is called *once* with `lock_down.status =` "something"? Or are we going to try to generate a *real* query and then assert that the query string is correct? No. We're not going to do any of that.

What you're seeing here is a situation where a unit test is *not* the right tool. There's really *two* reasons why it's not the right tool. First, it's too complex. It's a seemingly neverending series of mocks. The *second* reason is it's not actually that useful. If we're creating a complex query inside of `LockDownRepository`, to make that a *useful* test, we would need to execute that query and make sure it works. So unit testing, in this case, is out of the picture.

Instead of creating a fresh `LockDownRepository` here with a bunch of mocks in it, we're going to ask Symfony for our *real* `LockDownRepository` - the one that we would use in our normal code. And we're actually going to call the method on it, let it execute a *real* query, and then assert what we see in the database. That's called an "integration test", and I'll show you how to do that *next*.