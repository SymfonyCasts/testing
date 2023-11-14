# Emails

*All right*, team! We've covered all of the *main* parts of integration testing! Woohoo! It's *delightfully* simple. It's just a *strategy* to grab the *real* services from a container and *test* them, which *ultimately* gives us a more realistic test.

The *downsides* of integration tests are that they run *slower* than unit tests, and they're often more *complex* because you need to think about things like clearing and seeding the database. And sometimes, we don't want real things (like API calls) to happen. In this case, we actually had to use *mocking* to avoid that. The big takeaway here is knowing how to use the right tool - unit testing or integration testing - for the right job. That's *situational* and it's okay to use *both*.

To *finish* the tutorial, I'm going to show you how you can test a few more complex parts of your system, like testing whether emails were sent or messenger messages were dispatched. To do this, we need to give Bob a new superpower - the ability to put the park into lockdown. When he does this, our app should automatically send out an email to all park staff saying

`Hey! Watch out for the dinosaurs!`

So let's get started! Head over to `LockDownHelper.php` and down here, create a new method. We're going to call this to put the park into lockdown, so say `public function dinoEscaped()`. This will be a `void` return type, and we're just going to put some `TODO` comments here outlining what we're actually going to do. We just need to lock down the database and send an email.

To actually *call* this code and trigger the lockdown, let's create a new console command. Say

```terminal
./bin/console make:command
```
and let's call it

```terminal
app:lockdown:start
```

Simple enough! That creates a single class. Let's go check that out. There it is! In here, we're going to inject the `private LockDownHelper $lockDownHelper` and make sure we're calling the `parent` constructor. Nice! And then we're going to delete a bunch of logic here because we want to simplify things. We're just going to say `$this->lockDownHelper->dinoEscaped()`, and below that, `$io->caution('Lockdown started!!!!!!!!!!)`. *Sweet*.

This method doesn't do anything *yet*, but we can go ahead and try our command. Copy the command name... and run:

```terminal
./bin/console app:lockdown:start
```

And... we've got it!

Before we fill the logic into our `LockDownHelper`, let's write a test for it. We already have `testEndCurrentLockDown`. Before we add a new one, let's do that trick where we add a `private function` to help us get the service we're testing. Say `private function getLockDownHelper()`, which will return `LockDownHelper`. And inside of here, we'll just copy the code from above... and return it. *Then*, we can simplify the code up here to just `$this->getLockDownHelper()->endCurrentLockDown()`.

All right, *now* let's create our new test method. Say `public function testDinoEscapedPersistsLockDown()`. Inside, we're going to start the same way we always do - by *booting the kernel*. Then we can call our method with `$this->getLockDownHelper()->dinoEscaped()`. Cool. We *could* run this test right now if we wanted to. We could say:

```terminal
symfony php vendor/bin/phpunit tests/Integration/Service/LockDownHelperTest.php
```

And... it doesn't *fail*, but it *is* risky because we haven't actually performed any assertions. What we want to *assert* is that this *did* insert a row into the database. How can we do that? We *could* grab the entity manager or our repository service, make a query, and then do some assertions on that. *However*, Foundry comes with a nice little trick for this.

After we call our method, we can say `LockDownFactory`. Normally, we would say things like `create` or `createMany`, but this *also* has a method on it called `repository`. This is an object from Foundry that *wraps* your repository and allows you to call different methods on it. So we can treat it as if it's our *real* repository and call `findMostRecent()` or `isInLockDown()`. But it *also* has this `assert()` on it. Let's say `assert()->count(1)` to make sure that the there is *one* record in the database. We *could* go further and *fetch* that record to make sure its status is "active", but we're not going to worry about that right now.

Okay, let's run the test again. This should fail and... *perfect*! It does! *So* let's go paste in some code that will create the lockdown and save it. Easy peasy *boring* code. If we try the test now... our test passes!

All right, let's look at sending that email. Before we can send the email, we need to add an assertion for that. Just like how we can assert things in the database, this gives us some tools for asserting things like if an email was sent, which is *normally* a difficult thing to test for. We can do that by saying `$this->assertEmailCount()`. We won't talk about any of the other methods here, even though they sound similar. We can assert *a lot* of things via email, but simplicity's sake, we're just going to assert that we sent a single email during this request. And if we run that... it *fails*. That's because we don't actually have mailer installed yet. So let's do that!

Run:

```terminal
composer require symfony/mailer
```

If it asks you about Docker configuration, that's up to you, but I'm going to say `Yes permanently`. We'll talk about what that did in a second, but it's not super important.

Similar to a database, with Mailer, we have to configure our Mailer connection parameters. That's done in `.env` via `MAILER_DSN`. Go ahead and uncomment this. This `null` is actually a great default. It means that your emails *won't* actually send in the dev or test environments, and then you can override on your production environment to send it to somewhere real. That's really handy, but if you *do* want to change this to something else, I would probably add this `null` transport to `.env.test` because it's really nice to avoid sending any emails in the test environment.

Okay, let's try our test again. And... *beautiful*! It fails because we haven't sent any emails. Let's do that now. Over in `LockDownHelper.php`, let's inject one more service here: `private MailerInterface $mailer`. Then, down here, since this isn't a *Mailer* tutorial, we're just going to call a new function called `sendEmailAlert()`... and then we'll paste that in. Perfect! Then, if you hover over `Email()` and hit "alt" + "enter", that will add the `Symfony\Component\Mime\Email` `use` statement. That should be good! Back at our terminal... got it! The test *passes*!

By the way, this isn't really related to testing, but one of the cool things about using the Docker integration is, when we installed Mailer, it actually added this little `mailcatcher` service. If you run

```terminal
docker compose down
```

and go back

```terminal
up -d
```

it created this `mailcatcher` service, and when we run our test again, instead of the email (which doesn't typically send), it uses the `null` transport. But because we have that running in the background and we're using the Symfony binary, we can run

```terminal
symfony open:local:webmail
```

and all of our emails are being caught in the background here. If we actually sent an email using our application, that would go here as well. Watch this!

If we run

```terminal
symfony console app:lockdown:start
```

*that's* our lockdown! And if you look over here.. ah! We have two messages! Pretty *sweet*!

Anyway, before we stop talking about testing, I want to show you one more quick tool for testing email messages, and it's *another* library from Zenstruck. Run:

```terminal
composer require zenstruck/mailer-test --dev
```

As we saw, Symfony has built-in tools for testing emails, and they work *great*. The mailer test library is no exception, giving us even *more* ways to test our emails. And it's super simple to use! We're going to add another trait - `use InteractsWithMailer` - and then, down here, instead of `assertEmailCount`, we can say something like `$this->mailer()->`... and then, as you can see, we have a *ton* of different asserts at our disposal. We'll say `assertSentEmailCount(1)`, and below that, `assertEmailSentTo()`, which we'll send to the `staff@dinotopia.com` with the Subject line `PARK LOCKDOWN`. Whoops! Let me fix my typo there... And so you can see that this is the `expectedTo` and then this is a `callable` where we could assert more things or just pass the subject. That sounds simple, but this is just one of the *many* things we could do here with our `mailer-test` library. If you're curious, I encourage you to check out the documentation on all of the *other* cool things `mailer-test` can do. If we run our test again... it *still* passes!

Next up: Let's talk about testing *messenger*.
