# Kernel Test Case

Coming soon...

In our application, if we wanted to use LockDownRepository to make some real queries,
we can do that. We know that we can just auto-wire LockDownRepository into a
controller or somewhere else, call a method on it, and boom, everything works. Now,
in our test, we want to do the same thing. Instead of creating the object manually,
we want to ask Symfony to give us the real service that's configured to talk to the
real database so it can do its real logic. To fetch a service from inside of Symfony,
what we're basically going to do is boot Symfony like normal, then get access to its
service container. And to help with that process, Symfony gives us a helper base
class called KernelTestCase. Now, there's nothing particularly special about this
class. If you hold Command or Control, you can see it extends the normal test case
from phpUnit. It just has methods in it to boot and shut down Symfony's kernel, which
basically means Symfony infests its container. So what this allows us to do
specifically is at the top of our test method, we can say self, self, self, colon,
colon, boot kernel. That boots Symfony in the background, and now its container is
live and full of real services. We can then grab our service by saying lockdown
repository equals self, colon, colon, git container, which is a helper method from
kernel test case, arrow, git. And then we're going to pass it the service ID. And for
our services, that's going to equal the class name. So that's just going to be simply
lockdown repository, colon, colon, class. And to see what this is, let's dd lockdown
repository below it. By the way, this is an easy way to spot a unit test versus an
integration test. A unit test is going to extend test case, and an integration test
is going to extend kernel test case, because they want access to real services.
Anyway, let's run our test. So vendor bin phpunit. Or if you want, you can also run
just bin slash phpunit, which is a little shortcut, that setup from Symfony, but I'll
keep, I'll just run a phpunit directly. And yes, there it is. There is our service.
It doesn't look like much this, but this lazy object is actually something that comes
from the real live service itself. So this is our real live service. Now, one thing I
want to point out is that self colon colon get container gives us the service
container, and then we call the method get on it. This is not something that we do in
our normal code. For the most part, it's not possible inside of Symfony, or at least
it's not something that we do or should do for us to get access to the entire
container. Maybe by auto-wiring container interface, and then saying things like
repository equals container arrow get lockdown repository. That's just not something
that we do in our code. We rely on dependency injection, auto-wiring. And for the
most part, like if you tried that, it wouldn't work inside of your application code.
So the key thing I want to point out here is that this self colon get container gives
you a special container that you can use to get access to special container that only
exists in the test environment. And this container is special because it actually
does allow you to just call a get method and ask for any service you want by its IDs.
This is a little bit unique to the test environment, but it's super convenient.
Alright, so since we have this lockdown repository, let's try running a very simple
test with it. By the first thing I'm going to do is you'll notice if I if I call
methods on this, it's not actually going to autocomplete the correct one. Because my
editor doesn't know what this actually returns. So I'm going to assert that lockdown
repository is an instance of lockdown repository. Notice this is not a PHP unit
assertion. I didn't say this arrow assert something. This is just a little PHP
function I use that will throw an exception if lockdown repository is not a lockdown
repository. And it will be in this code will never cause a problem. I do that really
as a fancy way to help my editor, because now you can see that I have all of my
methods in there. Alright, so let's say assert false. That lockdown repository arrow
is in lockdown. So the idea being here is we haven't added any rows to the database.
And so because of that, we should not be in lockdown. And we know that since we're
just returning false right now, this test actually is going to pass and it doesn't.
Did I forget to fix that? And it does. Okay, so we're using the real service, but
we're not actually making any queries yet. Will this keep working when we if we
actually start making a query? Let's find out next.
