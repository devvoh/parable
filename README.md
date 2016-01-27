## Fluid PHP Framework

Fluid is intended to be a tiny, fast and out-of-your-way PHP framework that just lets you get stuff done. To achieve this,
Fluid includes only what is needed to create small to medium-sized web applications, but allows any PSR-4 compatible library
to be added easily.

I envision Fluid to be used mostly for fast prototyping of back-end logic and small to medium-sized actual applications.

Laravel and similar big frameworks attempt to be your buddy through every aspect of development. And I love Laravel.
But Fluid is different - it simply attempts to provide you the shortest route to a working application. Don't get me
wrong, it's pretty complete, but it's taking shortcuts and that's by design.

Don't try to build something huge on Fluid. That's what Laravel and Symfony are for. Fluid is for smaller to medium-sized
projects.

## Requirements

- PHP 5.4+ (and according to php7cc, it's php7 compatible, but I haven't tried it yet!)
- Sense of adventure

## Documentation & More

Documentation is currently non-existent, but I am more than willing and available to answer any questions. I am also very
open to suggestions and improvements. Fluid is what I personally need in a framework, and if it seems to fit what you
need as well 'except for these small things', I would love to hear from you to see if maybe we can't work it in somehow.

## License

Fluid PHP Framework is open-sourced software licensed under the MIT license.

## Details

Fluid is built on top of Devvoh Components, a package soon to be spun off into its own project, which will feature
composer compatibility. The following components are currently in this package, all in a different state of 'done':

- Cli, basic functionality to build a command-line application with.
- Curl, to interface with external addresses, very limited as of yet (get website & download file). Want to add REST
  methods (POST/GET/UPDATE/PATCH/DELETE) at the very least.
- Database, PDO wrapper.
- Date, easy way of working with timezone-adjusted DateTime objects.
- Debug, pre-tagged dumps and print_r's, with optional dies after the dump.
- Dock, simple system to add and trigger front-end hooks.
- GetSet, component to work with $_GET, $_POST, $_SESSION, $_COOKIE and custom resources.
- Hook, simple system to add and trigger back-end hooks.
- Log, simple logging library.
- Query, relatively feature-complete SQL query builder.
- Response, basic response object.
- Rights, simple binary-based rights system.
- Router, relatively feature-complete array-based router.
- SessionMessage, works in conjunction with GetSet to offer easy session messages.

Fluid offers most of these Components through the App::class, using lazy-load getters, offering all of the above
as singleton instances. This keeps the load down to what you actually use.

## To-do (the candid list)

- Components need to be built into more complete libraries and require at least some documentation.
- Error handling and exception throwing is woefully inadequate at the moment. This is partly because I believe that PHP's
  default errors are clear enough for developers who would ever try Fluid and Fluid isn't really complex enough to require
  Exceptions everywhere. My opinion on this might change in the future. For now, I've stopped implementing Exceptions
  until I decide either way.
- Query now only supports simple left joins, it should support more complex joins and inner joins.
- Though PDO supports other types of databases than the default SQL ones (sqlite & mysql), Query probably doesn't,
  since it builds for sqlite specifically (and just happens to work on mysql). Should look into at least PostgreSQL.
- Get as much feedback and criticism as I can. Can't improve without outside critiques.
- Keep following PSR-2 'almost', because ha-ha I like single-line accolades and I don't care what you say.
- Fluid itself (without components) is ~30k now. I don't want to set a hard maximum since it'll get as big as it needs
  to be but I'll be damned if it ever gets over 200k. I can't imagine it ever needing to be that big. If it does,
  something went either very wrong or very right.

And most of all, I'm just going to keep whittling away at this as time goes on.

Any questions? Find me at [devvoh.com](http://devvoh.com) or ask me a question by adding an issue on github.