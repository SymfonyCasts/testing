# Testing Pages that Need the Database

Let's add a feature test that uses the database. Our first target is a ship's page.
In the browser, click "Ships in dock", then click on USS Espresso. We'll test that this
page loads and shows the right text.

Jump over to your terminal and make a new test:

```terminal
symfony console make:test
```

Choose `WebTestCase` again, and call it `ShipTest`. Remember, it gets created right in
`tests/`, so move it into `Feature/`. This class will test everything to do with
ships: the show page now, and the listing page later.

Rename the stub method to `testShowsShip()`. First, we need the URL. Let's be a little
naive and grab it straight from the browser: `/ships/uss-espresso`. Paste that into
the `request()` call. Keep `assertResponseIsSuccessful()`, and change the `h1`
assertion to look for "USS Espresso". Then add
`$this->assertAnySelectorTextContains()`: this looks at *every* `p` tag on the page,
not just the first one. Pass `p` and "Latte-class", since I know the model is in a
`p` tag:



Give the tests a run:

```terminal
symfony php bin/phpunit
```

An error! "no such table: ship". That's a 500 error.

## The Test Database

The reason is that our tests use a *separate* database. Open the `.env` file and look
at `DATABASE_URL`. It's SQLite, and the file it stores the database in is
`var/data_%kernel.environment%.db`. Look in the `var/` directory: there's
`data_dev.db`, which has all our fixtures in it, and a new `data_test.db`. The test
database was created automatically... but nothing was put in it. No tables, no ships.

As an aside, take a look at what our entities look like: in `src/Entity/`, there's
`Ship`, the entity we're checking in this test. And we already have Zenstruck Foundry
installed, with some factories, like `ShipFactory`. Then, in `src/Story/`, open
`AppStory`. This holds the dev fixture data for all the different ships: that's where USS
Espresso comes from.

## Loading Fixtures into the Test Database

So, for our test, let's load the fixtures into the test environment:

```terminal
symfony console foundry:load-fixtures --env=test
```

Run the tests again:

```terminal-silent
symfony php bin/phpunit
```

We're good!

## Why This Is Brittle

But there are a few problems here. First, we always have to remember to run that
command. Delete `var/data_test.db` - like on a fresh clone - and run the tests
again... they fail with that same error. Having to load the fixtures before every test
run is annoying.

Second, where does USS Espresso come from? Well, we know it's in our fixtures, but
fixtures change over time: `AppStory` is really for our *dev* data. There's a
disconnect between the test and the ship it relies on. The test itself should define
the ship it needs.

And third, say a test that ran *before* this one deleted that ship. Now this test
fails, because the ship is gone from the database.

What we need is a way to reset the database before each test, and have each test
create its own data instead of relying on the `foundry:load-fixtures` command. Next
up: let's do that with Foundry!
