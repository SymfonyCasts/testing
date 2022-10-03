# Mocking Symfony's Http Client

There we go. Having the ability to use mocking their test is really awesome, but
mocking is a bit weird and complex. It's not as simple as using the real normal
object to keep this complexity to a minimum. We should only mock objects that are
complex and not easy for us to initiate and control. If we look at our dinosaur test
here, we ki we could have mocked these, uh, the dinosaur object here, but that's
adding extra lines of code and there's no real benefit to it. It's just making our
tests harder to read. And when we could have just used the real object instead, the
general rule of thumb, wait. No, but that compared to our service with the mock HTP,
with our HTTP client, the logger and the response, these classes, if we tried to use
the real objects, that might be a little bit difficult and we can't control 'em as
we've seen earlier. So the general rule of thumb is you mock services, not models.
There are of course, exceptions to this rule, but no, and there are of course
exceptions to this rule. All right. So Symfony's HTP HTTP client is indeed a service,
but the library comes with two real classes specifically made for testing the mock
HTTP client and the mock response.

These two classes are preconfigured mock objects that work right out of the box, but
they don't need to be used together. We can use these in any type of test in place of
a real HTTP client. Let's go ahead and let's go ahead and clean our, uh, test
exception, throw 'em with an unlabel, uh, method up real quick. For starters, we're
going to remove, uh, for starters, we're going to, instead of using the, or creating
a mock based on the response interface, let's just change this to new mock response
inside that response. We need to provide a, uh, either we need to provide the body
for our response. So remember GitHub is returning J to us. So we're going to do the
same thing here. We're going to JSON and code and array. And within the let's go
ahead and pass our issue that we're testing. All right? And we can just remove this,
uh, this configuration here and for our mock HTTP client, we can do the same thing.
So let's get rid of this up here, and we will take this to = new mock = new mock HTTP
client. And we only need to pass the response that we want our client to return to
us.

All right. So now we have our mock client. We have our service and you notice we're
not really doing anything with this logger up here. So instead of setting the logger
on available, let's just go ahead and cut the, uh, the mock out where we create the
mock remove this line. And down here in our service, we'll just pass the, uh, create
mock, uh, method call in, uh, directly into our service. Well, this is looking better
already. Let's move over to the, uh, CLI and type in vendor, bin PHP, unit, vendor,
bin, PHP, unit, awesome, 10 tests and 16 assertions. This is super cool. You can note
that our assertion test went down from 17 to 16, and that's because we're no longer
performing, uh, the expects self once on our HT or on our mock HTTP client anymore.
And that's really okay if you really needed to use this. No, that is because neither
of, of these mocks perform any assertions themselves like expects self. Once. If we
look up in our test method above, we can see that we're calling accepts self once,
and this is actually an assertion, the mock HT GB client, and the mock response. They
cannot perform those same assertions.

They simply act as silent configurable replacements for an HTTV client interface to
show you, uh, but the trade off for not be able to use those is let's take a look at
a diff and we'll go right, click on our GitHub service test, get and show diff we'll
close out. Our we'll close out our source tree over here. And look at this. We
started off look how much code we've removed. We've reduced our test class just by
using the Symfony mocks by about 11 lines. That is pretty cool. All right. And I
believe that is a wrap. So done.
