# End to End (E2E) App Testing

## Intro: the app & the testing pack

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

## First feature test

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
