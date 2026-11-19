# Creating Test Data with Foundry

We have a test for the ship page, but we need a way to reset the database and create
our data right inside the test, so it's nice and easy to read. My tool of choice is
Zenstruck Foundry. We already have it installed, and it has PHPUnit integration... but
we installed PHPUnit *after* Foundry, so the recipe never got to add its PHPUnit
config.

## Reinstalling the Foundry Recipe

The fix is to reinstall the recipe:

```terminal
symfony composer recipe:install zenstruck/foundry --force --reset
```

Let's see what that changed with:

```terminal
git status
```

It reset our Foundry config *and* our `AppStory`. Go look at `AppStory`: it's cleared
out completely! Restore both:

```terminal
git restore src/Story/AppStory.php
```

And:

```terminal
git restore config/packages/zenstruck_foundry.yaml
```

Check `git status` again... perfect: only `phpunit.dist.xml` changed. Take a quick
look: the recipe added few lines down at the bottom:



This is Foundry's PHPUnit extension, and it lets us use the database reset feature
and factories in our tests.

## Resetting the Database

Let's see what that looks like! Above the `ShipTest` class, add the `#[ResetDatabase]`
attribute. This tells Foundry to reset the database before each test in this class.

## Creating the Ship in the Test

Next, at the beginning of our test, create the ship we want: `$ship = ShipFactory::createOne()`. Foundry fills in
defaults for us: open `ShipFactory` and look at `defaults()`: it sets fake data for
everything. So we only override the fields this test actually checks: the ship name
and the model. Pass `'name' => 'USS Espresso'` and `'model' => 'Latte-class'`.

Down in the request, we know the ship's slug is made from its name... but we have the
ship right here, so let's be totally sure and use it: `'/ships/'.$ship->getSlug()`.
Slug generation isn't what we're testing here anyway. Everything else stays the same:



Just to be doubly sure, delete `var/data_test.db`. Then jump over to your terminal and
run the test suite:

```terminal
symfony php bin/phpunit
```

An error: "Booting the kernel before calling `createClient()` is not supported". You
might see this when using Foundry. `createClient()` checks whether the kernel has
already been booted in this test... and it's hard to see, but behind the scenes,
`createOne()` boots the kernel: it needs Doctrine from the container to save the
ship. The fix is simple: move the `createClient()` call above `createOne()`:



Run the tests again:

```terminal-silent
symfony php bin/phpunit
```

And all green!

## Testing the 404

Let's add one more quick test: the case where a ship isn't found. Copy the whole test
method and paste it below. We don't need to create a ship this time, and the URL can
stay as `/ships/uss-espresso`. It could be anything, really: the database should be
empty. Instead of asserting on the content, write
`$this->assertResponseStatusCodeSame(404)`. Then rename the method to
`testUnknownShipIs404()`:



We know the test above *does* create a USS Espresso. But thanks to `#[ResetDatabase]`,
the database is reset before this test starts, so that ship is gone. Run the test
suite again:

```terminal-silent
symfony php bin/phpunit
```

Three tests, all passing!

Next up: let's test the ships listing page!
