# Mocking Symfony's Http Client

Having the ability to mock objects in tests is super awesome, and kind of weird 
and complex all at the same time. If we have simple objects, like `Dinosaur`,
we should avoid the extra lines of code and just instantiate a real `Dinosaur` 
for the test. After all, it's pretty easy to control the behavior of `Dinosaur`
just by calling its real methods. No need for the mock weirdness.

But, for more complex objects, like `HttpClient`, using the *real* client...
can be a headache. The general rule of thumb is to use *mocks* for *complex* 
objects like, services... but for *simple* objects, like models or entities, 
just use the real thing.

Looking back at Symfony's HTTP Client, we *were* able to mock both the client
*and* the response to control its behavior. *But*, because needing to do this
sort of thing is so common, Symfony's HTTP Client comes with some special classes
that can do the mocking *for* us. It comes with two real classes specifically 
made for *testing*: `MockHttpClient` & `MockResponse`. Using PHPUnit's mock system 
is fine, but these exist to make our life even easier.

Check it out: instead of *creating* a mock for `$mockResponse`, instantiate a 
`MockResponse()` passing in `json_encode()` with an array to mimic GitHub's API 
response. Grab Maverick's issue below and paste that into the array. Since 
`MockResponse` is already configured, remove the `$mockResponse` bits below.

[[[ code('fe13320af2') ]]]

For the client, remove `$mockHttpClient` and below, instantiate a new 
`MockHttpClient()` passing in `$mockResponse` instead. Since we're not doing 
anything with `$mockLogger`, cut `createMock()`, remove the variable, and paste 
that as an argument to `GithubService()`.

[[[ code('288873b40c') ]]]

Wow, this is looking better already! But, let's see what happens when we run the
tests:

```terminal
./vendor/bin/phpunit
```

And... Woah! All of the tests are passing!

But, the assertion count did go down to "16" because `MockHttpClient` and `MockResponse`
do *not* actually perform any assertions, like how many times a method is called.

So... what's the actual benefit to using these mock classes? Why not just create
our own via PHPUnit? Ha... Check out this diff of `GithubService`. We removed 11 
lines of code by using the "built-in" mocks in just one test. Imagine how many 
lines of code we could remove by using them in all of our tests. Hm... I think 
that's exactly what we'll do next.
