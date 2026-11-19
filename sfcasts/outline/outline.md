# End to End (E2E) App Testing

## Touring the App & Installing the Testing Pack

### Setup

- Follow the README: `symfony composer install`, `symfony console make:migration`,
  `symfony console foundry:load-fixtures`, `symfony serve -d`
- Open the site

### Tour

- Homepage: "Your ship. Every system. OK." - a repair dock for starships
- Click "Ships in dock": 8 ships waiting for repairs, each card shows its fault count
- Point out departed ships (like USS Wanderlust) aren't listed
- Click USS Espresso: model, captain, arrival, 2 reported faults
- Click "Run full diagnostic": systems appear one by one... "2 FAULTS DETECTED"
- Mention: that's JavaScript calling our API - we'll test it near the end of the course
- Click "Book a repair": USS Espresso is already selected
- Submit empty: validation errors
- Fill it in (`quick@espresso.space`, Critical, "Warp core smells like burnt coffee
  beans."), submit: back on the ship page with "Repair ticket SD-0XX filed..."
- Open the profiler's email panel: a confirmation email was queued for the captain
- Mention: critical repairs also sound a red alert to the engineers in the background
- Click "Engineer login": note the demo crew box
- Log in as `scotty@shipshape.space` / `engage`
- "Repair queue" appears in the nav: open tickets, our new critical one included
- Log out
- Visit `/api/ships`: the same ships as JSON
- Visit `/api/tickets`: 401, the API needs a token (we'll test that later)

### The plan

- Every one of these features will get a test - we won't touch the app's code
- Peek at `src/Factory/` and `src/Story/AppStory.php`: Foundry is already installed, but
  only for fixtures (for now!)

### Install the testing pack

- `symfony composer require --dev symfony/test-pack`
- What it brings: PHPUnit, BrowserKit, CssSelector
- What the recipe adds: `phpunit.dist.xml`, `.env.test`, `tests/bootstrap.php`,
  `bin/phpunit`
- Run `symfony php bin/phpunit`: "No tests executed!" - time to write one

## Writing Your First Feature Test with WebTestCase

### Kinds of tests

- Unit: one class, no Symfony, super fast
- Integration: real services from the container, maybe the database
- End-to-end, aka feature tests (what we'll call them): make a request to the app like
  a user would, check the response - that's this course
- Trade-off: slower, but proves the whole feature works

### Naming & organizing

- One sub-directory per test type: `tests/Unit/`, `tests/Integration/`, `tests/Feature/`
- Unit & integration tests: named after the class they test, mirroring the app's
  namespaces (`src/Service/RepairDesk.php` -> `tests/Unit/Service/RepairDeskTest.php`)
- Feature tests: named after the *feature* they test, not a controller
  (`HomepageTest`, `BookRepairTest`)
- Group them in sub-directories only when it helps (`tests/Feature/Api/...`)

### Homepage test

- `symfony console make:test` -> `WebTestCase` -> `Feature\HomepageTest`
- Open `tests/Feature/HomepageTest.php`: `createClient()`, `request('GET', '/')`,
  `assertResponseIsSuccessful()`, `assertSelectorTextContains('h1', 'Hello World')`
- Rename the method to `testHomepageLoads()`
- Run `symfony php bin/phpunit`: fails! Read the failure - our `h1` doesn't say
  "Hello World"
- Change it to `'Every system'`, run again: passing
- Add `assertPageTitleContains('Starship repair')`
- Add `assertSelectorTextContains('nav', 'Book a repair')`
- Run again: green

### No database (yet)

- The homepage doesn't touch the database, so this "just works"
- Tease: the ship page *does* need the database - next chapter

## Testing Pages that Need the Database

### Ship page test

- `make:test ShipTest`, `testShowsShip()`
- Request `/ships/uss-espresso` (the slug from the URL in the browser)
- Assert: successful, `h1` contains "USS Espresso", page shows "Latte-class"
- Run it: fails with a 500 - "no such table: ship"

### The test database

- Our test isn't using the dev database: `DATABASE_URL` has `%kernel.environment%`
  in it, so tests use `var/data_test.db`
- Point out `dbname_suffix: '_test...'` in `doctrine.yaml`: same idea for MySQL or
  Postgres (SQLite already gets its own file)
- That file is empty: no tables, no ships
- Load the fixtures into it: `symfony console foundry:load-fixtures --env=test`
  (it creates the schema too)
- Run again: passing!

### Why this is brittle

- The test only passes because of data it can't see: the fixtures
- Delete `var/data_test.db` (a fresh clone, CI...) and run: broken again until
  someone remembers the command
- Change a fixture (rename USS Espresso, drop a fault) and an unrelated test fails
- Tests that *change* data (booking a repair) leave it behind for the next test and
  the next run
- Fix it next: each test creates exactly the data it needs, on a fresh database

## Creating Test Data with Foundry

### Quick refresher

- Brief: Foundry is covered in the integration testing course
- It's already installed: `src/Factory/` has a factory per entity, and
  `src/Story/AppStory.php` is what `foundry:load-fixtures` loads
- The same factories work inside tests: no fixtures command needed

### Enable Foundry in PHPUnit

- Foundry's recipe adds its PHPUnit extension to `phpunit.dist.xml`... but Foundry was
  installed before the test pack, so that file didn't exist yet
- Re-run the recipe: `symfony composer recipes:install zenstruck/foundry --force -v`
- `git status`: `<bootstrap class="Zenstruck\Foundry\PHPUnit\FoundryExtension"/>` was
  added inside `<extensions>`... but `--force` also overwrote `src/Story/AppStory.php`
  with the recipe's empty story - restore it: `git checkout src/Story/AppStory.php`
- If you took the integration course: we used the `Factories` and `ResetDatabase`
  traits there - since Foundry 2.9 the extension (plus an attribute) replaces them

### A fresh database for each test

- Add `#[ResetDatabase]` to `ShipPageTest`
- Delete `var/data_test.db` to prove we don't need it anymore
- Run: fails with a 404 - the database is created, but it's empty. Exactly what we
  want: the test has to bring its own ship

### Rewrite the ship page test

- At the top of the test, create the ship:
  `ShipFactory::new()->withFaults(SystemType::WarpCore, SystemType::Navigation)->create([...])`
  with `name` "USS Espresso" and `model` "Latte-class"
- Only set what we assert on - the factory fills in the rest
- Request `'/ships/'.$ship->getSlug()` instead of a hardcoded URL
- Run: passing!
- Everything the test checks is set up right there in the test: nothing hidden in
  fixtures, and nothing left behind for the next test

### Unknown ship: 404

- Add a second test: `testUnknownShipIs404()`
- No ship to create: request `/ships/uss-nope`
- `assertResponseStatusCodeSame(404)`
- Run: both passing. Thanks to the reset, we *know* no ship from the previous test
  is still around
- Tease: next, test the ships in dock listing with a few ships at once
