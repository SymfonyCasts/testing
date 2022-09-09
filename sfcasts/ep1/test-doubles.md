So right now we have our tests are failing because we need no. So right now our
GitHub service test test get health report returns, correct health status for Dino,
with the data set sick Dino is failing because we're getting an argument count error,
too few arguments to function. GitHub service construct zero were passed in, and
exactly one was expected. We're getting the same error for a healthy D set. Two. If
we look back at our service, we can see that in our construction we're we are
requiring a logger interface instance to be passed in. Anytime we create a GitHub
service us back in our tests, we're passing in nothing. So let's go ahead and fix
this. Using PHP units, mocking super abilities, all of the test case classes or no,
our test case class that we're extending our test from includes a method called
create mock. So let's do mock logger = this create mock. This create mock method
requires that we pass in a string for the object or class that we want to create a
mock for. So we're going to pass in logger interface class. And in this, we can go
down to our service on the next line and pass in our mock logger,

Move back to our test, uh, move back to our terminal vendor bin PHP unit. And there
we go. We have our eight tests and 11 assertions and they're all passing. So what is
this actually doing this create mock method? Well, create mock is taking our longer
interface that we passed in or any other class, uh, any other class or object. And
it's creating, it's making a copy of that object, stripping out all the logic within
its methods and it returns null for everything. Now I know you're about to ask. If we
look in our service here, what happens to this log message? When we call this logger
info after calling our request from the HTTP client? Well, nothing. When we call this
logger, because we're now using a mock our test, uh, the info method is taking in all
of our, uh, is taking in our, our two arguments, the request items issue, and the
array, and it's doing nothing with them. This info method, our mock is an empty
method. Then of course our mock just returns. No, by the way, this mock logger is
actually called a test double. Now, in fact, we'll run across a few different names
for mocks test doubles, stubs, mock objects. The list is endless only these methods
or all of these different names effectively mean the same thing, fake objects that
stand in for real ones. There's some subtle differences, uh, between the different
names and we'll clue you in along the way,

But now we still have a problem with our test here. When we call our, uh, service,
get health reports method, and we look back here in our service, we're still calling,
uh, we're, we're calling GitHub's, uh, API instead of creating the HTTP client within
our service. And then calling this, we need to mock this HTT, ah, we need to mock the
HTTP client. So we don't have to use the real API anymore. Now we're creating the
client with a static method and static methods are extremely difficult to mock and
they're even harder to test. So rather than, uh, using a static method to create our
client, that's come up Ector constructor, and we're going to use dependency injection
to pass in a private HTTP client instance. And we'll call it HTTP client. Then down
here for our response, instead of calling client request, let's change this to this
client, uh, to this HTTP client. And now we can get rid of our static. Uh, we can get
rid of our, uh, static, uh, method here, line 20 and back up at the top. Let's go
ahead and remove that use statement as well for the HTTP client,

Move back into our test and we have our GitHub service that we need to pass a client
for, just to make sure that everything's working. Let's go ahead and do HTTP client
create, move back into our terminal. And we'll do once again, vendor and PHP unit
create everything's still working, but again, we've only just moved the problem.
We're not creating the static client. We're not creating the client using the static
method in our class anymore, or in our service. We're actually doing our test now. So
we're going to make a mock for this. So right under our mock logger, let's do mock
client = this create mock HTTP client interface. And we'll pass that in to her get
hub service here on her test. So mock client let's move back into her terminal. We'll
run our test again, and now we have a failure GitHub service test test get health
report returns, correct health status for Dino, with dataset sick, Dino failed
asserting that the two variables reference the same object. Hmm. We only have one
failure and one's passing. Let's figure out what's going on. So for sick Dino, we're
expecting our health status to be sick for Daisy. If we look in our service,

When we call the, uh, we call get Hub's API, we log that request. And then we go
through our, uh, responses array, and we find the Dino. And remember down here, no,
we call the HTP client. We get a response, we log the response, or we log the, uh,
status code for that response. And then we go over, turn that or JSON into an array.
And we look for the Dino's name. Well, the problem is right here in two array.
Remember I just said that whenever we create a mock that the mock logger or that the,
remember what I just said, that when we create a mock, which is what we're doing for
the HTTP client, the PHP unit strips out the logic for each method and just returns
Nu well, for our response here, we're supposed to be getting an array or a JSUN
string back, and now we're not getting anything. So the two array method has nothing
to iterate over, coming up next. We'll show you how to take this mock client that
we're creating and configure it. So we actually do return back a response when we
call our get health reports method.
