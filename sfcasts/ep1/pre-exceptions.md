All right. So we're using our service and our controller now, but we're getting this
type error

And our GitHub service get Dyna status from labels. Method is the return value must
be of type E uh, enum health status, but no is returned instead. And we can see here
based on the exception, uh, output that Symfony's given us is that when our service
tries to guess the name, uh, of the health status from the label, it's returning
null, obviously instead of actually giving us healthy or sick, if we look at our
issues, I think it's because Dennis finished his daily exercise routine and his
status is hungry. We know we have the status, sick, I status healthy labels accounted
for, but I don't think we have hungry in there. Let's move back into our code and
pull up our health status enum and yeah, that's it. We don't have hungry in here. So
let's add a new case case hungry = hungry, move back to our browser and let's
refresh. Awesome. We got our app back up and running, but Dennis is not accepting
visitors. Remember the only time we don't want, uh, a dinosaur to have visitors is,
is to be sick, but a hungry di come on, who doesn't want to see that it

Move back into our, yeah. Move back into our code and let's open up our dinosaur test
so we can fix this. And let's see here. Test can get set data test down has correct
size description from length test is accepting visitors by the default. Uh, we want
to keep that test. Uh, test is not accepting visitors. If Dino is sick, I think we're
going to rework this test here because all we're doing is we're sending the health
and then we're inserting that false is, is accepting visitors. Let's go ahead and add
a data provider. So below here, public function test, oh, public function. Uh, what's
the name of my provider. Oh yeah. Health status provider. And this is going to return
a generator.

And now we still want to keep this test case where we're asserting false, that a sick
Dino is not accepting visitors, but we also want to assert true that a healthy Dino
is accept or that a hungry Dino is accepting visitors. So let's add our sick Dino
case and we will return, uh, an array. So we'll yield sick. Dino is not accepting
visitors. And of course this array is going to be, let's see, we need a health status
of sick. And then we want to assert that false will be is accepting visitors right
below this. We can go ahead and add our next case, which will be hungry. Dino is
accepting visitors and our health status will be hungry. And we want to assert that
hungry donors are accepting visitors. All right, let's come back up here to our test
method. And we will tell this test method to use the data provider. So at data
provider, and we're going to use the health status provider.

All right, we can go ahead and rename this method too, because test is not accepting
visitors of sick. It's not really doing that anymore. Let's change that to test is
accepting visitors based on health status. And we will pass in a health status
argument, and this is going to be health status. And then we're also going to pass in
a ion and this will be our expected visitor status. Awesome. All right, let's fix up
our test here so we can continue use bumpy for a dinosaur and this now, instead of
sitting the set, the health, uh, sick, oops, we want to set the health to our health
status that our provider is giving us. And then assert false is accepting visitors.
Let's change this to selfer saying that expected health status is the same as Dino is
accepting visitors. All right, move into your terminal type in vendor bin PHP unit.
But this time we're going to show you something a little bit different. Cool, little
trick PHP unit can do pass in a filter flag. And for the argument type in test is
accepting visitors based on health status. Enter, check this out. We have two tests
and two assertions in one failure. Our dinosaur test test is accepting visitors based
on health status with our hungry <inaudible> is not accepting visitors. Uh, data set
failed, asserting that false is identical to true.

We'll fix this in just a second, but this filter flag that we provided up here, uh,
to PHP unit, what this is doing, it's telling PHP unit that we only want to run tests
that have tests is accepting visitors based on health status. As part of their name,
let's go back and into dinosaur class so we can fix our accepting visitors method.
And we'll scroll down to is accepting visitors and ha there's the problem. This
health is identical to health status healthy. Mm let's. Change this to this health
status is not identical to health status sick. Then we return true. Otherwise we
return false. Alright, let's move back into our browser and vendor been PhD unit.
We're going to do our filter plug again, but aside from, uh, passing in test method,
names or test classes, we can also do hungry. I know is accepting visitors. Great. We
have one test and one assertion. This was the test that was failing. As you can see,
we fixed two problems. We can, we fixed the two problems, one our browsers up and
running. Again, two, we have passing tests and three we've learned how to use the
filter flag with PHP unit, just to make sure that our tests are back up and running.
Let's go ahead and run vendor and PHP unit

And great all nine tests. And the 14 assertions are passing..
