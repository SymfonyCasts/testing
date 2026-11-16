# Touring the App & Installing the Testing Pack

Hey friends! Welcome to our end-to-end app testing course for Symfony! End-to-end tests
- I usually call them *feature tests*, and that's what we'll call them in this course -
are my favorite tests to write. They mimic what your users actually do on your site:
visiting pages, seeing results, clicking buttons, submitting forms... the real deal.

Once your setup is right, I also find them the easiest and most fun tests to write.
There's no mocking and no juggling dependencies: you just say "hit this URL, click these
buttons, fill in this form", then assert on what you expect to see afterwards. They're
also *great* for support. When a customer reports a problem, they usually tell you the
steps to recreate it: "I went to this page, filled in this form, clicked submit and got
an error". Write a feature test that follows those *exact* steps. Now you have a failing
test case: fix the bug, prove the test passes, then get back to your customer to tell
them it's fixed.

## Touring Shipshape

To get started, download the course code from this page and follow the `README.md` to
get set up. When you're ready, start the web server:

```terminal
symfony serve -d
```

Open the app in your browser and... say hello to Shipshape! It's a starship repair dock,
and this is the homepage. Click "Ships in dock" in the top nav to see every ship
currently waiting for repairs. Each card shows the ship's reported faults... and this
one is all OK.

Click on a ship's card to land on its page. Here, click "Run full diagnostic": it checks
each system, one by one. This runs through some JavaScript, and we *will* get into
testing that specifically.

As an anonymous user, you can also book a repair: click "Book a repair" right here on
the ship's page. The form already has this ship selected. Submit the form without
filling anything else in and... validation errors. A nice spot for some tests! Now
fill it in for real: use `quick@espresso.space` for the email, keep the priority as
"Normal", and for "What's wrong?", write "The warp core smells like burnt coffee!". Click
"Book repair" and... we're redirected to the ship's page with a flash message: our
engineers are on it!

The form sent an email too. Down in the web debug toolbar, hover over the status code:
it shows the POST request that redirected us here. Click its token to open that request
in the profiler, then click "Emails" on the left: a confirmation email was sent back to
us. And if we had chosen the "Critical" priority, the engineers would also be notified
immediately.

Engineers can log in too. Click "Engineer login" in the nav: there are some demo users
listed right below the form. Log in as `scotty@shipshape.space` with password `engage`.
Scotty gets a new "Repair queue" link in the nav. Click it to see all the submitted
repairs... with ours down at the bottom. Then click "Log out".

Finally, there's an API with the same functionality as the web interface. In the address
bar, go to `/api/ships`. I'll check "Pretty print" to make this look nicer: a list of all the ships in JSON. Now
try `/api/tickets`... a 401! That's for engineers only, and it requires an API token.

That's the quick tour! We're going to add end-to-end tests for *all* of these
features. And we won't change any app code: in this course, we're just writing the
tests.

## Installing the Testing Pack

First, we need some testing tools. Over in your terminal, run:

```terminal
symfony composer require symfony/test-pack
```

This is a group of dependencies that provide nice testing tools for Symfony. To see what
we got, run:

```terminal
git status
```

Some stuff was added to `.gitignore`, and of course, `composer.json`, `composer.lock`
and `symfony.lock` changed. We also have a new `.env.test` file, a `bin/phpunit` script,
a `tests/` directory and `phpunit.dist.xml`, which is the configuration for PHPUnit.

Jump over to your editor and open `.env.test`. It sets some test-specific environment variables.
Then check out `phpunit.dist.xml`: a sensible default config for getting started with PHPUnit,
added by its recipe.

Finally, in `composer.json`, look at the packages installed under `require-dev` at the
bottom: PHPUnit, Symfony's BrowserKit and Symfony's CssSelector. That's the default
setup for testing with Symfony.

## Running `bin/phpunit`

Let's see what happens when we run our tests:

```terminal
symfony php bin/phpunit
```

"No tests executed!" PHPUnit is up and running, now we just need to fill it with tests.

Next up: let's create our first feature test!
