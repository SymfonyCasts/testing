# The Repository Test Helper

*All right*, team! We've covered all the *main* parts of integration testing!
Woohoo! It's *delightfully* simple: just a *strategy* to grab the *real* services
from a container and *test* them, which... *ultimately* gives us a more realistic
test.

The *downsides* of integration tests are that they run *slower* than unit tests,
and they're often more *complex*... because you need to think about things like
clearing and seeding the database. And sometimes, we don't want real things (like
API calls) to happen. In this case, *did* use a bit of *mocking* to avoid that.
The big takeaway is, like everything, using the right tool - unit testing or
integration testing - for the right job. That's *situational* and it's okay to use
*both*.

To *finish* our journey, let's learn how to test a few complex parts of our system,
like testing whether emails were sent or messenger messages were dispatched. To do
this, we need to give Bob a new superpower: the ability to put the park into lockdown.
When he does this, our app should send an email to all park staff saying:

> Hey! Watch out for the dinosaurs!

## Creating the Command

Head over to `LockDownHelper`. Down here, create a new method. We'll call this to
put the park into lockdown, so how about `public function dinoEscaped()`. Give it
a `void` return type and just put some `TODO` comments here outlining what we're
actually going to do. We need to save a `LockDown` to the database and send an email.

To *call* this code and trigger the lockdown, let's create a new console command.
At the terminal, run:

```terminal
php bin/console make:command
```

Name it `app:lockdown:start`.

Simple enough! That created a single class: go check it out. Here it is! Inside,
inject the `private LockDownHelper $lockDownHelper` and make sure to call the `parent`
constructor. Nice!

Then delete pretty much all of this logic... and replace it with
`$this->lockDownHelper->dinoEscaped()` and then,
`$io->caution('Lockdown started!!!!!!!!!!)`.

Dangerous. This method doesn't do anything *yet*, but we can go ahead and try our
command. Copy the command name... and run:

```terminal
php bin/console app:lockdown:start
```

And... got it!

## Creating the Test

Before we fill in the logic for the new method, let's write a test for it. But first,
let's do that trick where we add a `private function` to help us get the service
we're testing: `private function getLockDownHelper()`, which will return
`LockDownHelper`. Inside, copy the code from above... and return it. *Then*, we can
simplify the code up here to just `$this->getLockDownHelper()->endCurrentLockDown()`.

All right, *now* create the new test method:
`public function testDinoEscapedPersistsLockDown()`. Start the same way we always
do - by *booting the kernel*. Then call the method with
`$this->getLockDownHelper()->dinoEscaped()`.

Cool! It's not interesting, but try test anyway:

```terminal
symfony php vendor/bin/phpunit tests/Integration/Service/LockDownHelperTest.php
```

And... it doesn't *fail*, but... it *is* risky because we haven't performed any
assertions.

## Database Assertions via the Repository

What we want to *assert* is that this *did* insert a row into the database. To
do that, we *could* grab the entity manager or our repository service, make a query,
do some assertions on that. *However*, Foundry comes with a nice little trick for
this.

After we call the method, say `LockDownFactory`. Normally, we would call things like
`create` or `createMany`, but this *also* has a method on it called `repository`.
This returns an object from Foundry that *wraps* the repository - much like how
it wraps our entities in a `Proxy` object. This means we can call *real* repository
methods on it - like `findMostRecent()` or `isInLockDown()`. But it *also* has
extra methods on it, like this `assert()`. Say `->assert()->count(1)` to make sure
that the there is *one* record in this table. We *could* go further and *fetch* that
record to make sure its status is "active", but I'll skip that.

Run the test again.

```terminal-silent
symfony php vendor/bin/phpunit tests/Integration/Service/LockDownHelperTest.php
```

This should fail and... *perfect*! It does!

I'll go paste in some code that creates the `LockDown` and saves it. Easy peasy
*boring* code.

If we try the test now... our test passes!

Next: let's send the email *and* test that it was sent. We'll do this with some
core Symfony tools *and* also with another library from zenstruck.
