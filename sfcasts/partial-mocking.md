# Partial Mocking

Coming soon...

Let's make our LockDown helper a bit more interesting. Let's pretend that when a
LockDown ends, we need to send an API request to GitHub. We actually, in our first
tutorial, we created some code that made some API requests to ask things about this
SymfonyCast DinoPark repository. So let's pretend now that when we end a LockDown, we
need to send an API request maybe to find all issues with some LockDown label and
close them. We're not actually going to do this but let's pretend because it's going
to create a really interesting situation. So we, in the last tutorial, we made a
service, a GitHub service, that kind of wraps the API calls. The one method it has in
it gets a health report from all the dinosaurs. ... ... Let's add a new public
function inside of here called clearLockDownAlerts. And inside, I'm not actually
going to make any API calls. I could, but I'll just kind of fake it with a log. I'll
put a log message. And this is where we would use our imagination that we're going to
make an API call. Cool? And we're also going to pretend that we've already tested
this in some other way. Unit test, integration test, whatever. The point is we're
confident that this method definitely works. So over in LockDown Helper, let's use
this. So we can auto-wire GitHub service, GitHub service, and down here ... ...
LockDownAlerts. Alright, let's try the test. We're not asserting anything. ... ...
And that makes sense. In our test, we're asking Symfony for our LockDown Helper, so
it's going to handle passing us the new GitHub service argument. And since our GitHub
service is not actually making a real API call, everything's fine. But what if GitHub
service did contain the real logic to actually make a HTTP request over to GitHub?
That could cause a few problems. One, it would definitely slow down our test because
API recalls are slow. Two, it might fail because when it checks our repository, there
maybe aren't any issues that have this LockDown label on it. And three, if it does
find issues with LockDown label, it might close them on our real production
repository even though this is just a test. And even further, ... ... ... ... ... ...
... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ... ...
... ... ... ... ... ... ... ... ... ... ...
