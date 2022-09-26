# Mocking: Test Doubles

So right now our `GithubServiceTest` is failing because we need to pass in
a `LoggerInterface` anytime we instantiate the `GithubService` within our test.
We *could* just create a logger instance and pass that in. But... that can get
a bit hairy - we'd need to know what arguments were needed to *create* a logger
instance, and that sounds like waaayy to much work... Instead, lets use PHPUnit's
super mocking abilities!

In our test method in `GithubServiceTest` add a `$mockLogger` by calling
`$this->createMock()` and pass in the name of `LoggerInterface::class`. Right below
pass the `$mockLogger` into our service.

In the terminal, run our tests.

```terminal
./vendor/bin/phpunit
```

And... HA! All of our tests are passing again!

Soo... What is this `createMock()` thing that we're doing? All of the `TestCase`
classes have a method, `createMock()`, that allows us to pass in an object or class
name and get back a stripped down copy of that object that we call a mock.
Now I already ready know what you're about to ask... What happens to the message
when we call the `info()` method on the `LoggerInterface` instance?

Welp, a whole lotta nothing... Internally, PHPUnit creates a logger instance,
strips out all of the logic within each method and returns *nothing* when a method
is called on our mock, *unless* we tell it do something different. By the way,
this mock logger is actually called a test double. In fact, we'll run across a
few different names for mocks like test doubles, stubs, mock objects... huh the
list is endless... All of these names effectively mean the same thing, fake
objects that stand in for real ones. There *are* some subtle differences between
the different names and we'll clue you in along the way.

We still have one minor problem with our test. Anytime we *run* this test, we're
calling the *real* GitHub API when the test calls `getHealthReport()`.
This is very bad mojo... Never use real *services* in your tests. You simply
cannot control their behavior. For example, what happens
if GitHub's API is offline for maintenance or we need to return something different
than what exists on GitHub? Is there a better way?

You guessed it, let's mock the `HttpClient`! But we can't do that as long as we're
creating the client *statically* with `create()`. Static methods are hard to mock,
so instead, in the constructor of our `GithubService` - add a
`private HttpClientInterface $httpClient`. The call the `request()` method on
`$this->httpClient` instead of `$client` and now we can remove the `$client` static
call all together along with the use statement above.

Over in our test, just to make sure everything is *still* working, when we create
our `GithubService`, temporarily pass in `HttpClient::create()`.


Run the tests again in the terminal:

```terminal-silent
./vendor/bin/phpunit
```

Hmm... Yes! We didn't break anything...

But we've only *moved* our problem from our service to our test. We still need to
create a mock for the `HttpClient`. Underneath our `$mockLogger`, `$mockClient`
equals `$this->createMock()` and pass in `HttpClientInterface::class`. Then use the
`$mockClient` instead of the `create()` method in our service.

Back to the terminal to run our tests:

```terminal-silent
./vendor/bin/phpunit
```

And... Oof! Our `Sick Dino` test

> Failed assert the two variables are the same

Hmm... For `Sick Dino`, we're expecting a `HealthStatus::SICK` for `Daisy`. In
our service, we are calling the `request()` method on our mock, making a log
entry for the request, then looping over the array return in our response...
And thats the problem. Remember I just said that whenever PHPUnit creates a mock
object, it strips out all of the logic for each method *within* that mock? Yup,
we're looping over an empty array...

How can we configure our mock to return *something* instead of *nothing*? I'll
show you that coming up next!
