# Service Test

Coming soon...

If you click this button to end the lockdown, it hits a die statement. I have created
a controller for this, but it's not hooked up yet. To end a lockdown, we need to find
the active lockdown, change its status to ended, and save it to the database. But
instead of putting that logic right inside of our controller, let's create a service
for it. Now we could use TDD to create this service, but I'm just going to create it
quickly and then test it. It'll make things a little bit more clear. So it could go
anywhere inside of source, but I'll put it in the service directory. Let's create a
new lockdown helper service. And I'm actually just going to paste in some logic here.
It's really boring. So you can see down here we have one method called
endCurrentLockDown. It calls a method on the repository findMostRecent to find the
most recent lockdown, and set its status to ended and flushes it. And up here, we're
just auto wiring the lockdown repository and the entity manager interface. Now to
make this work, this findMostRecent method, that doesn't actually exist on a
repository. So open our lockdown repository, and we're going to do a tiny bit of
refactoring here. I'm going to create a new public function here called
findMostRecent, which is going to return a nullable lockdown. And then I will grab
the code from below. Open that, and then call that lockdown equals this arrow,
findMostRecent. And yes, if we wanted to, we could create an integration task for
findMostRecent. It's up to you, but we'll skip that. So back over in our new lockdown
helper. Awesome, that is now happy. So before we use this lockdown helper, it
probably works, it's pretty simple. Let's test it. So the first question is, is this
a unit test or an integration test? And honestly, either would be fine here. We could
do a unit test and mock the lockdown repository, make sure findMostRecent is called,
and that it returns a lockdown whose status is ended, and that the entity manager has
flush called. A unit test would be a pretty decent fit for this. It's not too
complicated, and that would really test most of what we want to test. Or we can do an
integration test, which will run slower, but be a bit more realistic. So for the sake
of this tutorial, let's do an integration test. And really, you could have both. Down
in the test directory, you could have an integration test that tests certain methods
and a unit test that tests other methods. There's nothing wrong with that. But in
integration, I'm going to create a new service directory. Added there a new PHP class
called LockdownHelperTest. This time, we're going to go straight to
ExtendingKernelTestCase, because we know we need the container. And then we're going
to use our two favorite traits, useResetDatabaseTrait and factories. We're going to
put these in every single method. So one of the things that Foundry recommends is
creating a base class. So somewhere inside of tests, you could create an abstract
KernelTestCase. Put the traits there, and then have all of your integration tests
extend that. All right, down here, let's whip up our test. We're going to say test
end current lockdown. And we're going to start the same way. We're going to start
with self boot kernel, so we have access to the kernel. And then let's think. If
we're going to end a lockdown, we're going to need a lockdown in the database. So the
first thing I'll say is lockdown equals lockdown factory. Create one, and we're just
going to take control of the only thing we care about, which is the status. And that
should be lockdown status active. And since we know our database is always going to
start empty, we know this will be the only row inside of that database table. And
down here, let's grab our lockdown helper. That's going to be self get container,
arrow get, lockdown helper, colon colon class, and I'll use my assert trick to help
my editor to say it's an instance of lockdown helper. Or really commonly, you can use
the trick we used before, which is to have a private function at the bottom. Really
commonly, I'll do this because I'll need it in multiple test methods. But using this
little assert thing is also totally fine, or you can just skip it entirely. It's just
to help your editor. So down here, now we are going to act, so we're going to say
lockdown helper, arrow end current lockdown, I'll say lockdown helper, arrow end
current lockdown. And then this record should have just changed its status in the
database, so we can actually say this arrow assert same, lockdown status ended,
lockdown arrow get status. All right, that's a good looking test, let's try this. By
the way, one quick note on this is that because we're checking lockdown arrow get
status, we're actually just checking to make sure that this lockdown object had its
status changed. We're not actually testing whether it was saved to the database. If
we wanted to actually make a fresh query to the database, you could do something like
lockdown factory, colon colon repository, and you could actually find the most
recent, and do a fresh query for it there. We're going to talk more about the
repository shortcut later. It's a really minor detail, I just wanted to mention that
right here. Anyways, let's try this, symphony php bin slash vendor bin php unit, and
let's actually run tests, integration service, lockdown helper test, and oh, it fails
in a weird error. It says, the lockdown helper service or alias has been removed or
inlined when the container is compiled. What the heck? A really cool thing about
Symfony's service container is that if a service isn't used by anyone in your app,
it's removed from the container. If you think about it, in our app, our actual
application code, like controllers, repository services, nobody is using our new
lockdown helper service. We're not auto wiring this into a controller anywhere, or a
service anywhere, so Symfony is actually removing this from the container, which is
why we're getting that weird error. The fix for this is actually just to make sure
that it's used somewhere in your app. We're going to use it down here in this end
lockdown, so I'm going to auto wire lockdown helper, lockdown helper, and I'm not
even going to use it yet, but just having it there is going to be enough that Symfony
is not going to remove it from our container, and now the test passes, awesome. And
now we can just use this, so let's actually say lockdown helper arrow end current
lockdown, and I'll even do a little redirect to route, and go back to our homepage,
we can try this thing. It's a refresh here, we are in a lockdown, I can refresh,
we're in lockdown, end lockdown, the lockdown is gone, dinos are all back in their
pens. All right, next, I'm going to complicate things and introduce a situation where
we both kind of want to unit test lockdown helper and integration test it at the same
time. The solution is going to be something I call partial mocking.
