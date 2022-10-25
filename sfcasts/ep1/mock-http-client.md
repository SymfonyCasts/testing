# Mocking Symfony's Http Client

Having the ability to mock objects in tests is *super* awesome! But, mocking is
weird and complex. If we have simple objects, like `Dinosaur`, we can avoid the
extra lines of code and just instantiate a new `Dinosaur` for our test. But, for
more complex objects like `HttpClient`, using the *real* client is really the only
way to go. General rule of thumb, for simple objects like entities, dont use mocking
but for *services*, mocking is the way to go.

One of the cool things about using Symfony's HttpClient - it comes with two real
classes *specifically* made for testing. `MockHttpClient` & `MockResposne`. These two
classes are preconfigured mock objects that work right out of the box and can be
used in any test in place of a real client.

Instead of creating a mock for `$mockResponse`, instantiate a `new MockResponse()`.
To mimick GitHub, pass `json_encore([])` and grab Maverick's issue below and pass
that into the encoder. Because `MockResponse` is already preconfigured, remove the
`$mockResponse` bits below. For `$mockHttpClient`, replace the expectation with
`= new MockHttpClient()` passing in `$mockResponse.`

Let's keep up this refactoring and remove the `$mockLogger` all together. Instead,
well call `$this->createMock(LoggerInterface::class)` in the constrcutor.

Wow, this looking better already! But, let's see what happens when we run out tests:

```terminal
./vendor/bin/phpunit
```

And... Woohoo! All of the tests are passing!

In a round about way, we've just used TDD in the test itself... This pretty cool.
But, our assertion count did go down to "16" instead of "X". That's because
`MockHttpClient` & `MockResponse` do not actually perform *any* assertions them
selves. Instead they simply act as silent - configurable replacements for the real
objects. In most cases, this is completely ok! But, if you *really* needed to ensure
this method was only called exactly X number of times. It would be better to reach
for `createMock()` instead.

But, whats the benefit to using the mocks? Check out this diff of `GithubService`.
We removed 11 lines of code by using these preconfigured mock objects...

So what now? Take all that you've learned here and test everything including the
kitchen sink..
While you're doing that, we're hammering away at the next episode in our testing
series - Doctrine Testing...
