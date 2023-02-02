# Mocking: Test Doubles

So right now, tests are *failing* because we need to pass a `LoggerInterface`
instance to the `GithubService` inside of our test. We *could* just create a
logger and pass that in. But... That can get a bit hairy. Instantiating a logger
object might be simple... but what if it's not? What if we needed to instantiate
an object with 5 required constructor args... and some of those are for *other*
objects that are *also* tricky to create. Chaos!

Fortunately, PHPUnit has our back: with super mocking abilities!

## A Mock Logger

Inside the `GithubServiceTest` create a `$mockLogger` variable set to
`$this->createMock(LoggerInterface::class)`. Pass *this* into
the `GithubService` service.

[[[ code('16bc592495') ]]]

Let's see what happens when we run the tests now.

```terminal
./vendor/bin/phpunit
```

And... HA! All of our tests are passing again!

## But what is a Mock?

Soo... What is this `createMock()` black magic thing that we're using?
`createMock()` allows us to pass in a class or interface and get back a "fake" 
instance of that class or interface. This object is called a mock.

Now I already ready know what you're about to ask... What happens to the message
when we call the `info()` method on the mock `LoggerInterface`?

Welp, a whole lotta nothing... Internally, PHPUnit basically creates a fake class
that implements `LoggerInterface`... except that all of the methods are *empty*.
They do nothing and return nothing.

That is unless we *tell* it do something different. More on that soon.

By the way, this mock logger is actually called a *test double*. In fact, we'll run
across a few different names for mocks like - test doubles, stubs, and mock objects...
All of these names effectively mean the *same* thing: fake objects that stand in
for real ones. There *are* some subtle differences between the different names and
we'll clue you in along the way.

## We Should Always Mock Services

We still have one minor problem with our test. Anytime we run it, we're calling 
the *real* GitHub API. This is bad mojo... In a *unit* test, you should *never* 
use *real* services, like API or database calls. Why? The whole point of a unit 
test is to test that the code inside `GithubService` works. And, ideally, we 
would do that *independent* of any other layers of our app because... we simply 
can't control their behavior. For example, what would happen if GitHub's API is 
offline for  maintenance? Or, tomorrow, GenLab changes `Daisy` from sick to 
healthy! Right now, *both* of those would cause our tests to fail! But they 
should *not*! The unit test for `GithubService` should only fail if it contains
a bug *in* its code, like it's not parsing the labels correctly.

What's the solution? Mock the `HttpClient`.

## Refactoring HttpClient to use DependencyInjection

But... we can't do that as long as we're creating the client *inside* of 
`GitHubService`. Instead, in the constructor, add a 
`private HttpClientInterface $httpClient` argument. 

[[[ code('7d5a55c8fb') ]]]

Then call the `request()` method on `$this->httpClient` instead of `$client`. 
Since we're *now* using dependency injection, we can remove the static `$client` 
entire, along with the `use` statement above.

[[[ code('cf1edb2cab') ]]]

Apart from unit testing, this is just a better way to write your code.

In the test, start by giving the `GithubService` an http client *without*
mocking - `HttpClient::create()` - just to make sure everything is working as expected.

[[[ code('0948ad2237') ]]]

Try the tests:

```terminal-silent
./vendor/bin/phpunit
```

And... cool! We didn't break anything...

## Mocking the HttpClient

*Now* we can mock the `HttpClient`. Below `$mockLogger` add,
`$mockClient = $this->createMock()` and pass in `HttpClientInterface::class`. 
Now pass *this* to our service.

[[[ code('5f69f21efb') ]]]

Back to the terminal to run our tests:

```terminal-silent
./vendor/bin/phpunit
```

And... Oof! Our `Sick Dino` test

> Failed asserting the two variables are the same

Hmm... For `Sick Dino`, we're expecting a `HealthStatus::SICK` for `Daisy`. In
our service, we're calling the `request()` method on our mock, making a log
entry, then looping over the array that was returned in our response...
HA! That's the problem. Remember: whenever PHPUnit creates a mock object, it 
strips out all the logic for each method *within* that mock. Yup, we're looping 
over nothing!

In this case, we need to *teach* the `HttpClient` mock to return a response
that contains a matching issue with a `Status: Sick` label. That would let us 
assert that our label-parsing logic *is* correct.

How do we do that? It's coming up next!
