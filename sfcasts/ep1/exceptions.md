# Testing Exceptional Exceptions

Okay.

All right. So I think the best way to handle, uh, for us to become aware if gen lab
creates any new status labels that we don't know about yet, like say status drowsy is
to throw an exception in our code.

Let's

Move back over here to our GitHub service class. And we're going to take a break from
TDD for a moment. So looking at our function, we call the GitHub API. We go through
the list of issues. And if the issue title contains our dinosaur name, we call the GI
Dino status from labels method,

Excuse me.

And we are passing the array of labels from that issue to this method. So looking
here in our method, we take that array of labels. And if the label doesn't start with
status, we carry on. Otherwise we remove the status pre, uh, status prefix, and then
trim it up. So I think this is the spot where we should throw an exception.

Let's

Create a let's go ahead and we'll copy. We'll cut the, uh, health status block right
here from our return and paste it right below our filter. All right. And then we'll
say if or no, we'll say health = health status, try from our status and now our TRIR
block, or, or try from method. Uh, we pass it a string and internally this method
attempts to match up our value, like healthy, sick, or, uh, hungry to this string.
And if it does it returns us or enum, if it doesn't it's going to return. No. So what
we can do here is if no = health, we'll throw a new runtime exception and we'll go
ahead and provide this exception a message. So let's do sprint F and we want it to
say if label is an unknown status label, exclamation mark, and then now we need to
pass the label from GitHub. And let's go ahead and close that line out. Cool. Down
here to return statement, we can just go ahead and, and return health. But if the
issue has no labels or the array is empty, we still need to return a health status.
So instead of using the status variable, let's change this to health = health status
healthy, because remember, unless the gin lab tells us that Diana was sick, we always
consume that they're healthy. Great. Now that we have this, let's go ahead and write
up a test, move to our GI up service test class, and we need to scroll down

Hmm.

Our existing test method here. It has a lot of the same code that we're going to need
for, uh, testing and exception. So let's just copy this test method. Let's just copy
this test method and scroll down here to the bottom. And we will paste it here below
our data provider. Awesome. All let's change the name of this to, uh, test exception,
thrown with unknown label, and we're not going to use a data provider. So we can just
go ahead and remove both of these arguments to our test method. And now let's scroll
down here to our assertion, which we're not going to need. So let's just remove this
assertion. Uh, we still need to call get health report and <affirmative>, we'll
change this Dino name to Maverick seeing as we're not using a data provider, let's
take a look at our response. And anytime we call the method to array, we are
returning an array of issues from GitHub and we only need one. So let's go ahead and
remove Daisy and we'll keep Maverick. And for this, uh, labels, let's change the
status healthy to status drowsy. Cool. Now let's move back into our terminal and
vendor bend PHP unit.

Awesome GitHub service test, test exception thrown with an unknown label has a
runtime exception. Drowsy is an unknown status label. This is great. Our app will
throw an exception. Now, if it doesn't recognize a status label, but how do we make
sure how do we make our test pass when we're testing that it does throw an exception?

Hmm.

Move back to your test and scroll down here right before we call the get health
report method. Let's add a, this expect exception, this expect exception and the
expect exception method takes a string, which is an exception. In this case, it is a
runtime exception that we're throwing. So we'll do runtime exception class

Move

Back to our test vendor, bend PHP, unit trait. We have 10 tests, 16 assertions, and
they're all passing. Let's add in one more to, uh, assertion so that we know our, our
app is only thrown in the exception from an unknown status label. And not because we
dorked up some other part of our code and that is throwing a runtime exception. So
right below here, we can do this expect exception, and we can actually test for a
message, a code, a message matches or an object. Let's use message. And now our
message in this case is drowsy is an unknown status label.

Ah,

Move back to a terminal. Let's run our test. One more time with vendor bend PHP unit
and great. We have 10 tests and 17 assertions. We don't have a need for it here, but
we could check, uh, the status code of the exception if it was important, uh, by
using expect, expect exception code and passing their code, just like we did with the
message here. One last thing about exception, assertions, all of these except methods
expect methods. Uh, in our test, they are treated just like assertions. If we're
expecting an exception or message to be thrown and is never thrown, our tests are
going to fail. It's quickly change drowsy to sleepy and move back to the terminal.
Run our test again. <laugh> of course they get up service test test exception, thrown
with unlabel is failed. Asserting that exception message drowsy is an unknown status
label contains sleepy is an unknown status label. We kind of knew that was going to
happen. Let's go ahead and we'll fix that real quick and we should be all set, change
that back to drowsy, and we are good to go coming up next. I'll show you a pretty ni
nifty little trick on how we can clean up some of these mocks here with, uh, some of
the built in powers of Symfony's HTTP client.
