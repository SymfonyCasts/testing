# Mocking Symfony's Http Client

Having the ability to mock objects in tests is super awesome, and kind of weird 
and complex all at the same time. If we have simple objects, like `Dinosaur`,
we should avoid the extra lines of code and just instantiate a real `Dinosaur` 
for the test. But, for more complex objects, like `HttpClient`, using the *real* client...
can be a headache. The general rule of thumb is to use *mocks* for *complex* 
objects like, services. But for *simple* objects, like models or entities, 
just use the real thing.

Fortunately, Symfony's HTTP Client can do the mocking for us. It comes with two real
classes specifically made for *testing*: `MockHttpClient` & `MockResposne`. These
two classes are preconfigured mock objects that work right out of the box and can be
used in any test instead of a *real* http client. What's so great about that?

Instead of *creating* a mock for `$mockResponse`, instantiate a `MockResponse()`
passing in `json_encode()` with an array to mimic GitHub's API response. Grab
Maverick's issue below and paste that into the array. Since `MockResponse` is already
configured, remove the `$mockResponse` bits below.

For the client, remove `$mockHttpClient` and below, instantiate a new `MockHttpClient()`
passing in `$mockResponse` instead. Since we are not doing anything with `$mockLogger`,
cut `createMock()`, remove the variable, and paste that as an argument to
`GithubService()`.

Wow, this looking better already! But, let's see what happens when we run the tests:

```terminal
./vendor/bin/phpunit
```

And... Woah! All of the tests are passing!

But, the assertion count did go down to "16" because `MockHttpClient` and `MockResponse`
do *not* actually perform any assertions. Don't worry, I'll show you have we can
assert our API is only called once shortly. I'm sure you're wondering, what's the
actual benefit to using these preconfigured mocks? Why not just create our own?
Ha... Check out this diff of `GithubService`. We removed 11 lines of code by using
the "built-in" mocks in just one test. Imagine how many lines of code we could 
remove by using them in all of our tests. Hm... I think that's exactly what we'll 
do next.
