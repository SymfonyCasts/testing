# Integration Test

Coming soon...

Hey, hey, people, welcome to episode two of our testing series, which is all about
integration testing. In the first episode, we talked about unit testing. It's kind of
the purest way of testing. You're testing classes and the methods on those classes.
And if your classes require other classes, you mock those dependencies. It's all cool
and beautiful. Well, in this tutorial, things get messier, but also a little bit more
useful in some situations. Instead of mocking dependencies, we're going to be
actually testing with our live services, which sometimes means doing things like
making real queries to a database. That comes with all kinds of exciting
complications, and we're going to dive into all of that. But first, let's get our app
going. You should absolutely download the course code from this page to code along
with me. After you unzip the file, you'll find a start directory that has the same
code that I have right here, including this nifty little readme.md file with all the
setup instructions, including database setup instructions, because we do have a
database in this course. So if you coded along with us for episode one, download this
course code because we've changed a few things, like added a database, and also
upgraded some dependencies. This tutorial will also still be using phpUnit 9, even
though phpUnit 10 is out, which is largely okay because there's not really that many
user-facing changes in phpUnit 10. All right, the last step in here is going to be to
find a terminal, move into the project, and run symphony serve dash d to start the
built-in web server at 127.0.0.1 colon 8000. Let's click that, and here we are,
Dinotopia, our app where we get to see the status of the dinosaurs inside of our
application. And now these dinosaurs are actually coming from the database. Not super
fancy, but we do have a dinosaur entity. And inside of our one controller, we are
actually querying for all the dinosaurs, and that's actually what we pass into our
template, and that's what we're seeing here. And everything with the app is working
great, except we have this one sort of minor problem where something goes wrong, like
Big Edie, our resident T-Rex escapes, and we don't have a way to lock down the park
and notify people. Basically, management is worried that too many guests are being
eaten. So feature number one that we're going to build is a system where we can have
a lockdown. We actually already have an entity for this. Super simple. It's called
lockdown. It has a created at, an ended at, and a status, which is a enum, and
instead of an enum, you can either be active, ended, or run for your life, which is
not a great situation. So what we want to do from our main controller here, our
homepage, is if there is a, if the most recent lockdown record in the database has an
active status, then we're going to render a giant warning on the screen that says,
that says, things are not good. So to do this, instead of our controller, we're going
to need to make a query to find the latest lockdown and see if it's active. So I'm
actually going to open up lockdown repository, and instead of putting that logic to
find the latest lockdown and check its status inside of our controller, I'm going to
put it right here. So we're going to create a new function here called, is in
lockdown, this is going to return a bool, and for now, I'm just going to return
false. So we're going to use a little bit of test driven development here, just like
we did in the first episode. And before I write this query and the logic, let's write
a test for it. So we don't have a test for this lockdown repository class yet, so
we're going to go into tests. And in the first tutorial, we had a unit directory, and
here we kind of matched our directory structure inside of source for all the
individual things that we're testing. This time, I want you to create a directory
called integration. You don't need to organize things like this, but it's somewhat
common to have unit tests in one directory and integration tests in another
directory. We haven't even talked about what an integration test is yet, that's okay.
We'll see that in a second. Now inside of integration, I'm still going to file the
directory structure. So I'm going to create a repository directory, since this class
lives in a repository directory. Inside of there, we'll call it
lockdownRepositoryTest. And to have this start like we always do, we're going to
extend test case from phpUnits, and I'll create a public function. Test is in
lockdown. And the first test we'll start with is real simple, we'll say
withNoLockdownRow. Oops, that's not right. Test is in lockdown with noLockdownRows.
So we'll test if there's no lockdowns at all in the database, then isInLockdown is
going to return false. All right, so let's keep pretending that we're living in the
world of unit testing, right? So let's try to test this just like we did in the
previous tutorial. Well, to do that, we would create a lockdownRepository, repository
equals new lockdownRepository. Now lockdownRepository extends
ServiceEntityRepository, which extends another class from Doctrine. And if you look
in here, we actually need to pass this a registry, a manager registry from Doctrine.
And if I hold Command or Control to kind of click into this, and we go to like the
base class, it's a little bit complicated here. But you can basically see is the
registry is used. We take the register we call getManager for class on it to get the
entity manager, and then we pass the entity manager to the parent. So you can already
see things are getting a little complex here, we're going to have to mock the
registry so that when getManager for class is called, it returns a mocked manager.
And inside of a repository, you know, we're eventually going to call something like
this arrow createQueryBuilder. Well, if you kind of dive into that, the way this is
created is it goes out to the EM property. That's that entity manager that we're
planning on mocking. And it calls createQueryBuilder, which returns a query builder.
So we're going to have to mock this method as well to maybe return a mock query
builder or maybe a real query builder. You can see how crazy things are getting. It's
going to be mock after mock after mock. And ultimately, at the end of the day, what
are we going to be asserting? Are we going to be asserting that we're calling the
exact correct like from method on a query builder? I'm going to make sure this method
is called once with, you know, mockdown.status equals something. Or are we going to
maybe try to actually generate a real query then assert that the query string is
correct? No, we're not going to do any of that. What you're seeing here is a
situation where a unit test is not the right tool. There's really two reasons why
it's not the right tool. First, it's too complex. It's mock after mock after mock
after mock. The second reason is it's not that useful. If we're creating a complex
query inside of LockDownRepository, to make that an actual useful test, we need to
actually execute that query and make sure it works. So unit testing in this case is
out of the picture. Now, we're actually going to, so instead of kind of creating a
fresh LockDownRepository here with a bunch of mocks in it, we're going to ask Symfony
for our real LockDownRepository, like the one that we would use in our normal code.
And we're actually going to call the method on it and let it execute a real query and
then assert what we see in the database. That is called an integration test, and
we're going to get that going next.
