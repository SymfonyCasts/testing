# Testing Emails

When we go into lock down, we need to send an email. Before we write the code
to do that, let's add an assertion for it.

## Asserting an Email is Sent

How? Symfony has our back: it gives us a few methods related to emails,
like `$this->assertEmailCount()`. We can assert *a lot* of things about emails, but
for simplicity's sake, we'll stick to this simple count.

Run the test:

```terminal-silent
symfony php vendor/bin/phpunit tests/Integration/Service/LockDownHelperTest.php
```

Epic fail, because... we don't even *have* mailer installed yet. Let's do
that! Run:

```terminal
composer require symfony/mailer
```

If it asks about Docker configuration, that's up to you, but I'm going to say
`Yes permanently`. We'll talk about what that did in a minute, but it's not super
important.

Similar to a database, we need to configure our Mailer connection parameters. That's
done in `.env` via `MAILER_DSN`. Uncomment this. The `null` transport is a great
default. It means that emails *won't* actually be sent in the dev or test
environments. And then you can override on your production environment to set it
to something real.

If you *do* want to change this to something else in the `dev` environment, I would
probably add this `null` transport to `.env.test`... because it's *really* nice to
avoid sending any emails from our tests.

Alright, roll the testing dice again:

```terminal-silent
symfony php vendor/bin/phpunit tests/Integration/Service/LockDownHelperTest.php
```

Better! It fails because we haven't sent any emails. Let's do that!

## Sending the Email

Over in `LockDownHelper`, autowire one more service:
`private MailerInterface $mailer`. Then, down here, since this isn't a *Mailer*
tutorial, call a new `sendEmailAlert()` method... and I'll paste that in.
Hover over the `Email` class and hit "alt" + "enter" to add the
`Symfony\Component\Mime\Email` `use` statement.

All set! Hustle back to the command-line:

```terminal-silent
symfony php vendor/bin/phpunit tests/Integration/Service/LockDownHelperTest.php
```

Got it! The test *passes*!

## Seeing Emails via MailCatcher

By the way, this isn't related to testing, but one cool things about using the Docker
integration is, when we installed Mailer, it added this `mailcatcher` service.
Run:

```terminal
docker compose down
```

Then

```terminal
docker compose up -d
```

to start the new service. Then run the test again. It still passes. *However*,
because the `mailcatcher` service is running *and* we executed our tests through
the Symfony binary, it overrode the `MAILER_DSN` environment variable and *pointed*
it at MailCatcher. What... *is* MailCatcher?

To find out, run:

```terminal
symfony open:local:webmail
```

Sweet! MailCatcher is a fake email service with a little web GUI to see the emails
your app has sent. If we sent an email via our real app, that would show up here.

Watch. Run:

```terminal
symfony console app:lockdown:start
```

Lockdown! And when you check MailCatcher... ha! We have two messages! Pretty cool!

## Using zenstruck/mailer-test

Anyway, before we stop talking about emails, I want to show you one more tool.
And it's *another* library from Zenstruck. Run:

```terminal
composer require zenstruck/mailer-test --dev
```

Symfony has built-in tools for testing emails, and they work *great*.
This `mailer-test` library gives us even *more* tools, and it's
simple to use!

Add another trait to our test - `use InteractsWithMailer` - and then, down here,
instead of `assertEmailCount`, we can say `$this->mailer()->`... and then,
woh, we have a *ton* of different asserts at our disposal. Say
`->assertSentEmailCount(1)`, and below that, `assertEmailSentTo()` with
`staff@dinotopia.com` and Subject line `PARK LOCKDOWN`. Whoops! Let me fix my typo.
You can see that this is the `expectedTo` and then this is a `callable` where
we could assert more things or just pass the expected subject.

This is pretty simple, but it's one of the *many* things we can do with this
library. Check out the docs to learn about everything.

Run the test again:

```terminal-silent
symfony php vendor/bin/phpunit tests/Integration/Service/LockDownHelperTest.php
```

All good! Next up: let's talk about testing *messenger*.
