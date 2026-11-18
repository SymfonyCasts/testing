# Writing Your First Feature Test with WebTestCase

Before we write our first test, let's review the three types of tests that are pretty
common. In the `tests/` directory, I like to create a directory for each type: it
helps separate them visually.

## Unit Tests

Create the first directory: `Unit/`. As a refresher, unit tests test a single unit -
a class - and any complex dependencies that class has, you mock. These are the
fastest tests to run. To organize them, I mirror the namespace structure of the
`src/` directory inside `Unit/`.

## Integration Tests

The next type is integration tests: create an `Integration/` directory. These boot a
Symfony kernel, which gives you access to the service container. For the most part,
they don't need mocks... with some exceptions, like mocking the HTTP responses from
an API you call. They're a bit slower than unit tests, especially since they can hit
the database. Same as with unit tests, I mirror the `src/` namespace structure inside
`Integration/`.

## Feature Tests

The last type, and the focus of this course, is end-to-end - or *feature* - tests:
create a `Feature/` directory. These are typically the slowest, because they make
actual requests against your app. But there are lots of cool tools we can use to
speed them up.

I name these by *feature*: something like `BookRepairTest` or `HomepageTest`. I only
use sub-directories when I'm grouping a set of features together.

## Using `make:test`

To create our first test, jump over to your terminal and run:

```terminal
symfony console make:test
```

This gives us a bunch to choose from. `TestCase` is for unit tests. `KernelTestCase`,
with access to Symfony's services, is for integration tests. And `WebTestCase` is the
one we want right now. Don't worry about the last two.

Choose `WebTestCase`, then name it `HomepageTest`.

Let's go take a look! It was created directly in `tests/`, so move it into `Feature/`
and make sure its namespace is now `App\Tests\Feature`. It stubbed out an initial test
for us: first, rename the method to `testHomepageLoads()`:



Because this class extends `WebTestCase` from FrameworkBundle, we have access to
special tooling for making requests against our app. Keep in mind that this boots
your app's kernel in memory: it's not loading a web browser or anything like that.
That keeps these tests as fast as possible, since nothing like Chrome needs to load
in the background.

## Running the Test

Let's run this and see what we get:

```terminal
symfony php bin/phpunit
```

A failure! That makes sense: we didn't change anything in the stub. It's telling us
that the `h1` selector containing "Hello World" failed, on line 15 of `HomepageTest`.
Take a peek at that line. The assertion above it *did* pass: we got a 200 on our
homepage. That's great! But of course, we don't have "Hello World" in an `h1` tag.

So what *can* we test? Look at the homepage in your browser. Let's assert that the
page title includes "Starship repair" and that "Book a repair" shows up in the nav
bar.

Back in the test, replace the `h1` assertion with
`$this->assertPageTitleContains('Starship repair')`. Then, add an assertion on some
HTML: `$this->assertSelectorTextContains()`. The selector is the HTML element we want
to look at: `nav`, because that's our nav bar. And we want it to contain "Book a
repair":



Run the test suite again:

```terminal-silent
symfony php bin/phpunit
```

Passing! 5 assertions and we're all good.

That was an easy one, since the homepage is a static page. Next up: let's test a page
that needs the database!
