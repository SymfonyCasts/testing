# Factory Data Seeding

I have a confession: I've been making us do *way* too much work!

To seed the database, we instantiate the entity, grab
the EntityManager, then persist and flush it. There's nothing wrong with this, but
Foundry is about to make our life a *lot* easier.

## Generating the Factory

At your terminal, run:

```terminal
php bin/console make:factory
```

This command comes from Foundry. I'll select to generate all the factories.

The idea is that you'll create a factory for each entity that you want to create
dummy data for, either in a test or for your normal fixtures. We only need
`LockDownFactory`, but that's fine.

Spin over and look at `src/Factory/LockDownFactory.php`. I'm not going to talk
too much about these factory classes: we already cover them in our Doctrine tutorial.
But this class will make it easy to create `LockDown` objects, even setting
`createdAt` to a random `DateTime`, `reason` to some random text, and `status`
randomly to one of the valid statuses, by default.

## Using the Factory in a Test

Using this in a test is a delight. Say `LockDownFactory::createOne()`.
Here, we can pass an array of any field that we want to *explicitly* set. The only
thing we care about is that this `LockDown` has an `ACTIVE` status. So, set
`status` to `LockDownStatus::ACTIVE`.

That's it! We don't need to create this `LockDown` and we don't need the
EntityManager. That one call takes care of everything.

Watch, when we run the test:

```terminal-silent
symfony php vendor/bin/phpunit tests/Integration/Repository/LockDownRepositoryTest.php
```

It passes! I love that.

## Foundry Proxy Objects

By the way, the `LockDownRepository` method returns the new `LockDown` object...
which can often be handy. But it's actually wrapped in a special *proxy* object.
So if we run the test now, you can see it's a proxy... and the `LockDown` is
hiding inside.

Why does Foundry do that? Well, if you go and find their documentation, they have
a whole section about using this library inside of tests. One spot talks about
the object proxy. The proxy allows you to call all the normal methods on your
entity *plus* several additional methods, like `->save()`, `->remove()` or
even `->repository()` to get another proxy object that wraps the repository.

So it looks and acts like your normal object, but with some extra methods. That's
not important for us right now, I just wanted you to be aware of it. If you do need
the *real* entity object, you can call `->object()` to get it.

## Adding More Objects

Anyway, now that adding data is *so* simple, we can quickly make our test more robust.
To see if we can trick my query, call `createMany()`... to create 5 `LockDown`
objects with `LockDownStatus::ENDED`.

To make sure our query looks only at the *newest* `LockDown`, for the active one,
set its `createdAt` to `-1 day`. And for the `ENDED`, set these to something older.

Let's see if our query is robust enough to still behave correctly.

```terminal-silent
symfony php vendor/bin/phpunit tests/Integration/Repository/LockDownRepositoryTest.php
```

It is!

But... actually... management has some extra tricky rules around a lockdown.
Copy this test, paste it, and rename it to
`testIsInLockdownReturnsFalseIfTheMostRecentIsNotActive`.

To explain management's weird rule, let me tweak the data. Make the first `LockDown`
`ENDED`... then the next, older 5 status `ACTIVE`. Finally, `assertFalse()` at
the bottom.

That... might look confusing... and it kind of is. According to management, when
determining if we're in lockdown, we should ONLY look at the MOST recent `LockDown`
status. If there are older *active* lockdowns... those, apparently, don't matter.

Not surprisingly, when we try the tests:

```terminal-silent
symfony php vendor/bin/phpunit tests/Integration/Repository/LockDownRepositoryTest.php
```

This one *fails*. But, look on the bright side: that test was super-fast to write!
And now we can go into `LockDownRepository` to fix things. I'll fast-forward through
some changes that fetch the most recent `LockDown` *regardless* of its status.

If we *don't* find *any* lockdowns, return false. Else, I'll add an `assert()`
to help my editor... then return true *if* the status does not equal
`LockDownStatus::ENDED`.

And now:

```terminal-silent
symfony php vendor/bin/phpunit tests/Integration/Repository/LockDownRepositoryTest.php
```

We're green!

## Using the LockDown Feature

We've been living in our terminal *so* long that I think we should celebrate by
*using* this on our site. In the fixtures, I've added an active `LockDown` by
default.

Head over to `MainController`... and autowire `LockdownRepository $lockdownRepository`.
Then throw a new variable in the template called `isLockedDown` set to
`$lockdownRepository->isInLockdown()`.

Finally, in the template - `templates/main/index.html.twig` - I already have a
`_lockdownAlert.html.twig` template. If, `isLockedDown`, include that.

Moment of truth. Refresh. Run for your life! We are in lockdown!

Next: we need a way to turn a lockdown *off*. Because, if I click this, it... does
nothing! To help with this new task, we're going to use an integration test on a
different class: on one of our normal services.
