# Factory Seeding

Coming soon...

To seed the database at the start of this test, we're instantiating an entity,
grabbing the EntityManager, and persisting and flushing it. And there's nothing wrong
with that, but Foundry makes life a lot easier. So at your terminal, run bin console
make factory. This is a command that comes from Foundry. And I'm going to select to
generate them all. So you're going to have a factory for each class where you want to
create dummy data for that class. We only need LockDownFactory right now, but that's
fine. All right, now spin over and let's actually look at
SourceFactoryLockDownFactory. I'm not going to talk much about these factory, too
much about these factory classes. We talk more about them in our doctrine tutorial.
But out of the box, this is going to help us create LockDown objects. And it's going
to set createdAt to a random date time, reason to some random text, and status
randomly to one of the Lock3 LockDown statuses. But of course, as you'll see, when we
create a new object, we can take control and override any of those if we want. But
out of the box, it gives us really great data. So using this in a test is absolutely
delightful. So actually, let's create the object first before I delete it. We'll say
LockDownFactory, colon, colon, create one. And then here, we can pass an array of the
fields that we want to take control of. Now, the only thing we really care about is
that this LockDown has an active status. That actually is the default status inside
of our entity. But just to be explicit about it, I'm going to set status here to
LockDownStatus, colon, colon, active. That'll just help make my test really obvious
that we're creating an active LockDown. And now, that's it. We don't need to create
this LockDown. We don't need the EntityManager. That takes care of saving that entire
thing. Watch, when we run it, it passes. So I love that. Now, by the way, the
LockDownRepository method actually will return the new LockDown object, which can
often be handy. But it's actually wrapped in a special proxy object. So if we run the
test now, you can see it's a proxy, and the LockDown is actually hiding inside of it.
Why does Foundry do that? Well, if you go and find their documentation, you can
search for ZenStruck and click into the documentation, they have a whole section
about using this library inside of the tests. And they also have a spot here about
the object proxy. Now, because you have this object proxy, what it allows you to do
is you can really call all the normal methods that you would call on it, and those
will work perfectly fine. But there are several additional methods you can call, like
you can actually call PostSave or PostRemove to delete, or even PostRepository to get
another proxy object that wraps the repository. So it looks and acts like your normal
object. It just has some extra methods on it. And that's not really important right
now. I just wanted you to be aware of it. If you do, for some reason, need the actual
entity object itself, you can call ArrowObject to get it. But for now, I'm just going
to delete all of that stuff. So the great thing now is that seeding the database is
so easy. It keeps our tests short, readable, and we can even make them more
complicated. So to try to trick my query, let's create many. And let's create,
actually, I forgot the argument here. Let's create five other lockdowns with lockdown
status ended. And now, just to make sure that we're kind of taking care of the
newest, I'm going to make our active one a newDateTimeImmutable minus one day. And
these new ones down here, we'll make these older. So we'll say they're minus two
days. So our test should still pass, but we're kind of getting a little more
complicated. So our test should still pass, but we're kind of getting a little more
complicated. Make sure we don't get confused by the fact that there are older ended
lockdowns. And we don't, the test still passes. All right, let's make another test to
really make sure we've got our logic correct here. So I'm going to copy this test,
duplicate it, and rename it to testIsInLockdownReturnsFalse if the mostRecent is not
active. And check this out. I'm going to change things here. I'm going to make this
first one status ended. And I'm gonna make this other, these other five all status
active. And we're going to assertKatiePeriod. And we're going to pass, And I'm gonna
make this other, these other five all status active. And we're going to assert that
this returns false. Now that might be look confusing. And it kind of is. But what
we're gonna do here is we're pretending that our business use case is that all we
really care about looking at is the most recent lockdown in the database. If the most
recent lockdown in the database is ended, we don't care if there are a bunch of other
lockdowns. Maybe we, every new lockdown just basically invalidates all of the old
ones. So not surprisingly, when we try running this, it fails. It fails. And the
great thing is it was so easy to set up this failing test. And now we can go down to
lock repository and fix it. So you see what we're looking for here is we're finding
any lockdown with the ended status. And if we find one, we return. So now we're gonna
do is we'll say lockdown equals, we're gonna find the most recent lockdown. I'm gonna
take out the and where. And we'll change this to order by lockdown dot created at
descending. So quite literally just find the first lockdown in the most recent
lockdown, no matter what. And then if we don't have that, then we must not be in
lockdown because the database is empty. And if we are, I'm gonna use my little assert
thing down here just to help my editor. So assert lockdown is an instance of
lockdown. And finally, we found the one most recent lockdown. So we can say return
lockdown arrow get status does not equal lockdown status ended. So it's not ended.
That means it is active when we are in lockdown. It's a little bit more complicated
now. But it passes. And the key thing was, we're creating some really nice tests very
easily and seeing the database. All right, I think we should celebrate by actually
using this on our site. In our fixtures, which I have already loaded, we actually do
have a lot active lockdown in there. So head over to main controller. And let's auto
wire lockdown repository. Lockdown repository. And then we'll just throw a new
variable in the template called is locked down, set to lockdown repository arrow is
in lockdown. And then finally, in the template for this page, templates main index
dot HTML twig, I already have a lockdown alert template. We're not using this yet.
It's gonna be pretty sweet. I'm gonna say, if is locked down. And we're just going to
include that. All right, moment of truth. Refresh. Run for your life. We are in
lockdown. All right, so next, we need a way to turn a lockdown off right now. If I
click this, it does nothing. And to help with that, we're going to use an integration
test on a different class, a normal service that we create.
