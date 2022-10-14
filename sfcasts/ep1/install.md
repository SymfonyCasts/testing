# PHPUnit Install

Hey everyone! Welcome to PHPUnit: testing with a bite! The tutorial where we discover,
to our horror, that yet *another* Dinosaur theme park has built their systems... without
any tests. It won't matter whether or not the raptors can open doors... if the fences
never turn on.

Our park is called Dinotopia. And, to help wrangle our prehistoric friends, we've
written a simple app that shows us which dinos are where and... how they're feeling.
As you'll see, it's great! Except for the complete lack of tests.

## App Setup

Anyways, to learn the most about testing and guarantee that nothing deadly will escape
from *your* application, you should code along with me. After clicking "Download" on this
page, unzip the file and move into the `start/` directory to find the code you see here.
Check out the `README.md` for all the setup details.

The *last* step will be to open up a terminal and run:

```terminal
symfony serve -d
```

to start a local web server on `127.0.0.1` port `8000`.

Cool! Move over to your browser, open a tab, go to `localhost:8000`... and
yes! Our Dinotopia Status app!

## The App: Dinotopia Status

This simple app has the name
of each dino, genus, size, and which enclosure the dino is currently hanging out in.
Down here at the bottom, we also have a link to GenLab's
super secret `dino-park` repository on GitHub. OoooO. This is where the engineers regularly
post updates to let Bob, our resident park ranger, know which dinos are feeling good,
need their medicine, or have escaped. Wait, What?! Hopefully, GitHub doesn't go offline
when *that* happens.

And that's where we come in! We've already built the first version of the Dinotopia Status
app. Looking at the code behind this, it's pretty simple: one controller

[[[ code('8077dc53c3') ]]]

one `Dinosaur` class...

[[[ code('922f8aa370') ]]]

and exactly *zero* tests. *Our* job is to fix that. We're also going to *add* a feature
where we read each dino's status from GitHub and render it. That'll help Bob avoid going
into the enclosure of Big Eaty - our resident T-Rex - when his status is "Hungry". Those
accidents involve a *lot* of paperwork. And thanks to our tests, we'll ship that feature
bug-free. You're welcome, Bob!

If you're new to testing, it can be intimidating. There are Unit tests, functional tests,
integration tests, acceptance tests, math tests! The list is almost endless. We'll talk
about all of these - except for math tests - throughout this series. In this tutorial,
we're going to zoom in on unit tests: tests that cover one specific piece of code - like
a function or method.

Oh, and by the way, tests are also *super* fun. It's automation! So buckley up.

## Installing PHPUnit

What's the first step to writing tests? Installing PHP's defacto standard testing tool: PHPUnit.
Move over to your terminal and run:

```terminal
composer require --dev symfony/test-pack
```

This `test-pack` is a Symfony "pack" that will install PHPUnit - which is all we need
right now - as well as some other libraries that'll come in handy later.

After it finishes, run:

```terminal
git status
```

Cool! In addition to installing the packages, it looks like some Symfony Flex recipes
modified and created a few other files. Ignore these for now. We'll talk about each
one at some point in this series when they become relevant.

Ok, we're ready to write our *first* test! Let's do that next.

