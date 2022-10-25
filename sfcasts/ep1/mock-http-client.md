# Mocking Symfony's Http Client

Having the ability to mock objects in tests is *super* awesome! But, mocking is
weird and complex. If we have simple objects, like `Dinosaur`, we can avoid the
extra lines of code and just instantiate a new `Dinosaur` for our test. But, for
more complex objects like `HttpClient`, using the *real* client can be a headache.
The general rule of thumb is to use *mocks* for complex objects like, services.
But for simple objects, like models or entities, just use the real thing.

One of the cool things about using Symfony's HttpClient - it comes with two real
classes *specifically* made for testing. `MockHttpClient` & `MockResposne`. These
two classes are preconfigured mock objects that work right out of the box and can be
used in any test in place of a real client. What's so great about that?

Instead of creating a mock for `$mockResponse`, instantiate a `new MockResponse()`.
Pass in `json_encode()` with an array so we can mimic GitHub's response. Grab
Maverick's issue below and paste that into the array. We don't need to configure
our response anymore than that, so remove the `$mockResponse` bits below.

For the client, remove `$mockHttpClient` and below, instantiate a new `MockHttpClient()`
passing in `$mockResponse` instead. Since we are not doing anything with `$mockLogger`,
cut `createMock()`, remove the variable, and paste it into `GithubService()`'s
constructor.

Wow, this looking better already! But, let's see what happens when we run out tests:

```terminal
./vendor/bin/phpunit
```

And... Woohoo! All of the tests are passing!

In a round about way, we've just used TDD in the test itself... This pretty cool.
But, our assertion count did go down to "16" instead of "X". That's because
`MockHttpClient` & `MockResponse` don't actually perform *any* assertions. Instead
they simply act as silent - configurable replacements for the reaobjects. In most
cases, this is completely ok since our test would *fail* if the API was never called.
But, if you *really* needed to ensure we only called the API 1 time, it's ok to use
`createMock()`.

But, whats the actual benefit to using these preconfigured mocks? Ha... check out
this diff of `GithubService`. We removed 11 lines of code by using the built in mocks
in just one test. Imagine how many lines of code we could remove by using it in
all of the tests.

So what now? Take all that you've learned here and test everything including the
kitchen sink..
While you're doing that, we're hammering away at the next episode in our testing
series - Doctrine Testing...
