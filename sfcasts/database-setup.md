# Database Setup

This first test was *too* easy. Almost cheating, really. We're returning `false` and `isInLockDown`. So let's write another, *more interesting* test. How about `public function testIsInLockDownReturnsTruIfMostRecentLockdownIsActive()`. Whew... that was a long one! And we'll start the same way we did before - with `self::bootKernel()` - so we have things going on in the background. The *tricky* thing about this is that we need the database to *not* be empty at the start. We need to be able to insert a lockdown record into the database that's *active* so that when we finally call our method and it executes the query, it will find that record. This is a critical part of integration tests, just because integration tests frequently talk to the database. We need to make sure our database is in a *known* state before the test.

Lucky for us, this is no problem. We just need to create some lockdowns and save it. Say `$lockDown = new LockDown()`... `$lockDown->setReason()` so we know *why* the lockdown is happening, and then we'll give it a reason here - `Dinos have organized their own lunch break`. Finally, we'll say `$lockDown->setCreatedAt()`, and we'll set that to one day ago - so `new \DateTimeImmutable('-1 day')`. That part isn't super important yet. And we won't need to set the status because, if you look here, you can see that the status is set to `ACTIVE` by default. Cool!

Okay, we created a basic entity object. *Now* we need to *save* it, and that's pretty simple too. We can get the `$entityManager` out by saying `self::getContainer()->get(EntityManagerInterface::class)`. We'll also do our `assert()` trick with `$entityManager instanceof EntityManagerInterface`, just to help our editor autocomplete. The next part is pretty familiar. We'll say `$entityManager->persist($lockDown)` and `$entityManager->flush()`, just like we would in our application code.

To see if this is working, down here, add `dd($lockDown->getId())`. Okay, let's try it! We're going to focus this test a little bit and run

```terminal
./vendor/bin/phpunit tests/Integration/Repository/LockDownRepositoryTest.php
```

to *only* run stuff from this class. And... *oh*... it *explodes*. Let's see here... Ah! It looks like it's having problems connecting to our database. This is a very known problem. When we start our application, we need to configure the database so everything works properly. The *key* behind this is the `DATABASE_URL` environment variable. I'm using Postgres, but it doesn't really matter.

So let's switch gears for a moment. *Normally*, when you set up your local environment, you're going to customize this `DATABASE_URL` here in `.env`, *or* you could create a `.env.local` file, override this *there*, and point it to wherever your local database connection is. Whatever you have as `DATABASE_URL` is what will be used in your test. You can see that it's having problems connecting to `127.0.0.1` at `port 5432` because it's reading that right from our `.env` file. This is all just *normal* database setup stuff, with *one* *tiny* but important difference in the test environment. For example, if you create a `.env.local` file, override `DATABASE_URL`, and run your tests (I'll change this port to something crazy like `9999`), it *won't* be used. Check out this error! It's *still* looking for `port 5432`. This is a special thing in the test environment where it *ignores* your `.env.local` file. So if you wanted to configure a `DATABASE_URL` *specifically* for your test environment, you need to put it into `.env.test` - the environment-specific test file. Before we move on, make sure to delete that `.env.local` file to avoid any confusion.

In our case, we're *not* going to rely on *any* of these `.env` files. That's because, if you followed the `README.md` instructions to set up a project, we're using Docker behind the scenes. We have a `docker-compose.yaml` file, which loads a Postgres database *for* us. And because we're using the Symfony binary as a web server, it's setting the `DATABASE_URL` *automatically*. So when we go over here and refresh the page... it's not using this `DATABASE_URL` from my `.env`. That's actually being overridden automatically to point to the Docker container that we're running. This is something that we talked more about in our Doctrine tutorial.

*However*, it looks like that's *not* happening in our test. When we run our test, it looks like the `DATABASE_URL` is just pointing to what we have in `.env`. And that's *exactly* what's happening. The Symfony binary doesn't have a chance to *inject* the `DATABASE_URL` environment. To *allow* that, instead of

```terminal
./vendor/bin/phpunit
```

we're going to run

```terminal
symfony php vendor/bin/phpunit
```

with that specific test. This `symfony php` is just a way to execute PHP, but by doing this, it's going to inject that `DATABASE_URL` variable.

Okay, when we try this... it fails *again*. But check it out! This is a *different* error. This time, it's talking to `port 58292`. That's a random port that our Docker database can be reached on, so that number might be different on your computer. It also says `database "app_test" does not exist. Huh?

To see what's going on, we can run:

```terminal
symfony var:export --multiline
```

This is going to show us all of the environment variables that the Symfony binary is injecting, the most important of which is `DATABASE_URL`. And here's where you can see it pointing at our Docker container which, for me, is running on port `58292`. A key thing to notice here is `app`. That's the name of the database that it's pointing to. So if our `DATABASE_URL` is pointing to a database named `app`, why did the error say that a database called `app_test` doesn't exist?

Before we answer that, I have another question for you. When we're running our tests, do we want our tests to use the *same* database that our local application is running? Ideally, *no*. Having a different database for your test versus your normal development environment is a good idea. For one, it's just annoying to run your tests and have it manipulate your data while you're developing. And *fortunately*, having two different databases is something that happens out of the box.

Open up `/config/packages/doctrine.yaml`. Down at the bottom, we have this special `when@test` block. This is configuration *only* for the test environment. And check out this `dbname_suffix`! It's set to `_test`. You can ignore this `%env(default::TEST_TOKEN)` bit. That relates to a library called ParaTest and, in our case, that's going to be empty. So *effectively*, it's just `_test`. Thanks to this config in the test environment, it's going to take our app and look for a database called `app_test`, which is really nice. And *that* explains why that database didn't exist. So all *we* need to do is just *create* that database.

Back over here, we can say

```terminal
symfony console
```

(this is running `./bin/console`, but *through* our Symfony binary so we can inject the `DATABASE_URL` environment variables), with

```terminal
doctrine:database:create --env=test
```

It will run this in the test environment so the database is `app_test`, and then it will try to create that. And if we try it... it *works*! We also need to create our `schema`, so if we add that here and rerun it... that works as well. *Cool*.

This project *does* have some data fixtures in it, so should we also execute our fixtures in the test environment? *No*, and we'll talk more about that soon. Right now, let's run our test again. And... yes! It hit our dump - `1`. That `1` is coming from our dump down here.

Okay, let's finish this test. We're going to need the `LockDownRepository` again, and to make things easy, we can copy this line and create a new private method down here. Say `private function getLockDownRepository()`, paste that code in there, add `return`, and we can use the typehint `LockDownRepository`. And now, we don't need the `assert()` because PHP will throw a big error if this returns something else for some reason. To simplify things up here, we're going to say `$this->getLockDownRepository()->isInLockDown()`. If we try the test again to make sure that still passes... it *does*. And *interestingly*, you can see the ID is now `2` in the database for our ID over here, so let's replace that. In this case, we want `$this->assertTrue()` that `$this->getLockDownRepository()->isInLockDown()`. If we tried that now, it would fail because this is just empty. We need a query for this, so say `return $this->createQueryBuilder()` with the alias `lock_down`, `->andWhere('lock_down.status != :endedStatus')`, `->setParameter('endedStatus', LockDownStatus::ENDED)`, `->setMaxResults(1)`, `->getQuery()`, and `->getOneOrNullResults()`. If this *finds* something, then we *are* in lockdown, and if *not*, we're *not* locked down, so say `!== null` to finish that.

All right, try the test now, and... it *fails*? *Oh*, our *second* test passed, but our *original* test is suddenly failing. How did that happen? As it turns out, thanks to our second test, when our *first* test runs, the database is no longer empty. In fact, it's piling up with more and more rows each time we run our tests. I'll show you. Run:

```terminal
symfony console dbal:run-sql 'SELECT * FROM lock_down' --env=test
```

There it is! This is a critical problem, and we need to make sure that we can guarantee the database is in a predictable state at the beginning of every single test. Let's dive into this very important problem *next*.
