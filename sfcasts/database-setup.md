# Test Environment Database Setup

This first test was *too* easy! So let's write another, *more interesting* one.
How about, ahem, `public function testIsInLockDownReturnsTrueIfMostRecentLockdownIsActive()`.
Phew!

Start the same as before: `self::bootKernel()`. The *tricky* thing about this
test is that we need the database to *not* be empty at the start. We need to insert
an active lockdown into the database... so that when we finally call the method
and it executes the query, it will find the record.

[[[ code('034159241e') ]]]

This is a common part of integration tests since they frequently talk to the
database.

## Seeding the Database

No problem! Let's create a lock down! Add `$lockDown = new LockDown()`,
`$lockDown->setReason()` so we know *why* the lockdown is happening, and
`$lockDown->setCreatedAt()` to, how about, 1 day ago. That part isn't super
important yet. Oh, and we don't need to set the status because, if you
look in the class, it defaults to `ACTIVE`.

[[[ code('ad2b6aa68e') ]]]

Saving this is simple too. Grab the `$entityManager` with
`self::getContainer()->get(EntityManagerInterface::class)`. And I'll do
our `assert()` trick with `$entityManager instanceof EntityManagerInterface`
to help my editor. Finish with the usual `$entityManager->persist($lockDown)` and
`$entityManager->flush()`.

To see if this is working, down here, `dd($lockDown->getId())`.

[[[ code('a80db9ac16') ]]]

Let's try it! Run *just* the tests from this file:

```terminal-silent
./vendor/bin/phpunit tests/Integration/Repository/LockDownRepositoryTest.php
```

And... *oh*... it *explodes*. Let's see... Ah! It's having trouble connecting to
the database!

Forgetting about tests for a moment, this is a familiar problem! The *key* to
connecting our app to the database is the `DATABASE_URL` environment variable. I'm
using Postgres, but that doesn't matter.

## Special .env handling for Tests

*Normally*, when we set up our local environment, we customize `DATABASE_URL`
here in `.env`... *or* we create a `.env.local` file and override it *there*.

[[[ code('23a20ca312') ]]]

And, in general, when we boot the kernel in our tests, everything works *exactly*
the same as loading our app in the browser. It *does* boot our code in a Symfony
environment called `test` instead of `dev`... and that does change a *few* things.
But 99% of the behavior is the same.

If you look at the error, the test is having problems connecting to `127.0.0.1` at
port `5432`. That makes sense: it's reading that from our `.env` file. All
very normal.

*But*, there *is* one important difference in the `test` environment. If you create
a `.env.local` file, override `DATABASE_URL`, and run your tests (I'll change this
port to something crazy like `9999`), it *won't* be used! Check out this error! It's
*still* looking for `port 5432`.

In the `test` environment *only*, the `.env.local` file is *not* loaded. So if you
want to configure a `DATABASE_URL` *specifically* for your `test` environment, you
need to put it into `.env.test`: the environment-specific variable file.

Before we move on, make sure to delete that `.env.local` file to avoid any confusion.

## Reading from Docker in your Tests

But in our case, we're *not* going to rely on *any* of these `.env` files. That's
because, if you followed the `README.md` instructions, we're using Docker behind
the scenes. We have a `docker-compose.yaml` file, which starts a Postgres database.
And because we're using the Symfony binary as a web server, it sets the `DATABASE_URL`
*automatically* to point to that container.

When we refresh the page... it's *not* using the `DATABASE_URL` from my `.env`:
it's using the dynamic value that's set by the `symfony` binary. This is something
that we talked about in our Doctrine tutorial.

*However*, that magic is clearly *not* happening in our test! The error makes it
obvious that it's looking at the `DATABASE_URL` from `.env`. And... that's true!
This is because the `symfony` binary doesn't have a chance to *inject* the
`DATABASE_URL` environment variable. To *allow* that, instead of running
`./vendor/bin/phpunit`, run `symfony php vendor/bin/phpunit`... followed by the path
to the test

```terminal-silent
symfony php vendor/bin/phpunit tests/Integration/Repository/LockDownRepositoryTest.php
```

The `symfony php` command is just a way to execute PHP... but by doing this, it
lets the `symfony` binary work its magic.

When we try this... it fails *again*. But check it out! This is a *different* error.
Now it's talking about port `58292`. That's the random port that *my* Docker database
can apparently be reached on. It also says `database "app_test" does not exist`.

## Automatically Suffixed Test Databases

To see what that's about, run:

```terminal
symfony var:export --multiline
```

This shows all the environment variables that the Symfony binary is injecting.
The most important is `DATABASE_URL`. This points at the Docker container... which
for me, is running on port `58292`.

The key detail is this `app` part. That's the *name* of the database that should
be used. So if `DATABASE_URL` is pointing to a database named `app`, why did the
error say that a database called `app_test` doesn't exist?

Before we answer that, I have another question: when we run our tests, do we want
them to use the *same* database that our local app is using? Ideally, *no*! Having
a different database for your tests versus your normal development environment is a
good idea. For one... it's just annoying to run your tests and have it manipulate
your data while developing. And *fortunately*, having two different databases is
something that happens automatically.

Open `config/packages/doctrine.yaml`. Down at the bottom, we have this special
`when@test` block. This is config *only* for the `test` environment. And check
out that `dbname_suffix`! It's set to `_test`. You can ignore the
`%env(default::TEST_TOKEN)%` bit. That relates to a library called ParaTest and,
in our case, it will be empty. So *effectively*, it's just `_test`.

[[[ code('2f8f4e37d5') ]]]

So thanks to this config, in the `test` environment, it takes the `app` config,
adds `_test` to it and ultimately uses a database called `app_test`.

That's really nice! And now that we understand that, all *we* need to do is *create*
that database.

## Creating the Database

At your terminal, run `symfony console` - this is just `bin/console`, but
allows the `symfony` binary to inject the `DATABASE_URL` environment variable -
`doctrine:database:create --env=test`:

```terminal-silent
symfony console doctrine:database:create --env=test
```

And... success!! We also need to create the `schema`: `doctrine:schema:create`

```terminal-silent
symfony console doctrine:schema:create --env=test
```

*Cool*! Try the test *now*:

```terminal-silent
symfony php vendor/bin/phpunit tests/Integration/Repository/LockDownRepositoryTest.php
```

It worked! That `1`... comes from the dump down here.

## Finishing the Query

Let's finish this test. To make life easier, copy the repository line,
then create a new private method: `private function getLockDownRepository()`. Paste,
add `return`, then the return type. Now we don't need the `assert()` because
PHP will throw a big error if this returns something *else* for some reason.

[[[ code('193ca88f68') ]]]

Simplify things up here with `$this->getLockDownRepository()->isInLockDown()`.

[[[ code('5271ef8d04') ]]]

Try the test again to make sure it still passes...

```terminal-silent
symfony php vendor/bin/phpunit tests/Integration/Repository/LockDownRepositoryTest.php
```

It *does*. And *interestingly*, the ID is now `2`. More on that soon.

Replace the dump with `$this->assertTrue()` that
`$this->getLockDownRepository()->isInLockDown()`.

[[[ code('3b9eeaeb94') ]]]

Over in the repository, I'll paste in the real query. This looks for a lockdown
that has *not* ended, and returns true or false.

[[[ code('7e06e79218') ]]]

Let's do this!

```terminal-silent
symfony php vendor/bin/phpunit tests/Integration/Repository/LockDownRepositoryTest.php
```

And... the test *fails*? *Oh*, our *second* test passed, but the *original* test
is suddenly failing. How did that happen?

It turns out, thanks to the second test, when the *first* test runs, the database
is *no longer empty*. In fact, it's piling up with more and more rows each time we
run the tests. Watch, run:

```terminal
symfony console dbal:run-sql 'SELECT * FROM lock_down' --env=test
```

Yikes! This is a *critical* problem: we need to guarantee that the database is in
a predictable state at the beginning of every test. Let's dive into this very
important problem *next*.
