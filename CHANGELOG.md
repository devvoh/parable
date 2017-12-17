# Parable PHP Framework Changelog

### 1.0.0

Considering the 0.11.x branch as Release Candidate 1 and the 0.12.x branch as RC2, it's time to ship final Parable. This release brings a major clean-up, documentation, and some useful additions.

__Changes__
- All method doc blocks now have explanatory text, even if it's superfluous, for documentation purposes.
- `\Parable\Console\App` now supports adding multiple commands in one go, using `addCommands([...])`.
- `\Parable\Console\Command\Help` now can generate a string for the usage of a command. Try it yourself: `vendor/bin/parable help init-structure`. Usage is also added to any exception caught by `\Parable\Console\App`'s exception handler.
- `\Parable\Console\Input` received the following updates:
  - `\Parable\Console\Input::getKeyPress()` has been added. It will wait for a single key press and return its value immediately. Special characters like arrow keys, escape, enter, etc, will be returned as a string value accordingly.
  - `\Parable\Console\Input::enableShowInput()` and its buddy `disable` are now available for you to use. If disabled, hides the user's input as they enter it. `Input` will call the `enable` on destruct to prevent its effects lingering after exiting the script.
  - `\Parable\Console\Input::enableRequireReturn()` and its buddy `disable` are now available for you to use as well. If disabled, no longer requires an enter before returning input.
- `\Parable\Console\Output` received the following updates:
  - `\Parable\Console\Output::writeBlockWithTags()` was added, making it possible to write a block with multiple tags.
  - `\Parable\Console\Output::getTerminalWidth()` will return the columns available in the current terminal window. `getTerminalHeight()` will return the lines available.
  - `\parable\Console\Output::isInteractiveShell()` will return whether the script is running in an interactive terminal session or not.
- `\Parable\Console\Parameter` has been rewritten, and options and arguments are no longer just arrays of data, but actual classes. This allows much more fine-grained control over whether, for example, an option has been provided but there's no value to go with it.
- `\Parable\Framework\App` received the following updates:
    - It now has a `HOOK_LOAD_ROUTES_NO_ROUTES_FOUND` constant and triggers it when, you guessed it, no routes are found.
    - Quickroutes! It now has `get()`, `post()`, `put()`, `patch()`, `delete()`, `options()`, `any()` and `multiple()` methods, so there's an easy way of defining callback routes without having to set up the entire structure. `any()` accepts literally any method, or you can pass an array of the methods to `multiple()` as its first parameter.
    - It also has `setErrorReportingEnabled($bool)` and `isErrorReportingEnabled()`. By default it's set to off. You can add `parable.debug` to the Config and set it to true to enable it.
    - It can now set the default timezone if you add a `parable.timezone` value to the config. 
- `\Parable\Framework\Dispatcher` received the following updates:
  - It can now return the route it dispatched by calling `getDispatchedRoute()`.
  - It now triggers two more events: `HOOK_DISPATCH_TEMPLATE_BEFORE` and `HOOK_DISPATCH_TEMPLATE_AFTER`. Use this to do something between a controller/callable being called and the template being loaded.
- `\Parable\Framework\Mailer` now supports setting a different mail sender. Default is, as it was, php's own `mail()`.
- `\Parable\Framework\Mailer` now can act on three config values:
  - `parable.mail.sender`, which should be the class name of the `SenderInterface` implementation you want to use.
  - `parable.mail.from.email`, the email for the for address.
  - `parable.mail.from.name`, the name for the for address.
- `\Parable\Framework\View` now accepts more classes to be registered for use within Views. Call `$view->registerClass($property, $className)` and you can do `$this->property_name->stuff()` in your views.
- `\Parable\GetSet\Base` now also has `setResource`, for when you want to switch, or set it using a method rather than overwriting a property.
- `\Parable\GetSet\Base::get()` now accepts a second parameter `$default` which is the value to return when the requested `$key` is not found. Added by @dmvdbrugge in PR #30. Thanks!
- `\Parable\Http\Request` now has `isOptions()` to check for OPTIONS method requests.
- `\Parable\Http\Request` now has constants for all methods and all accepted methods are in `Request::VALID_METHODS`.
- `\Parable\Http\Response` now has `setHeaders()` so you can add a bunch of headers in one call, `removeHeader($key)` so you can remove a header, and `clearHeaders()` to, y'know, actually, I think you got this.
- `\Parable\Http\Response::clearContent()` was added, in case you want to just want to call `appendContent()` multiple times rather than one `setContent()` and then those appends.
- `\Parable\Log\Writer\NullLogger` was added, for when you want to log nowhere at all.
- `\Parable\Mail\Mailer` now obviously also supports setting a Mail Sender. Default is, well, none. That's all up to you to configure. (Hey, psst, `Framework\Mailer` already tries to do that for you!)
- `\Parable\Routing\Route` received the following updates:
  - In `setUrl()`, it now prepends a '/' if it isn't provided. Now you can add a url without the prepended slash if you find that cleaner.
  - It now makes sure all methods set on it are uppercase, for more consistent matching.
  - It now receives its own name and can be retrieved by calling `getName()` on it.
  - It now has `setValues()`, in case you want to overwrite all values. These will be passed to the controller or callable, in order. This makes it possible to intercept a dispatch and inject, for example, the request or response objects in addition to the parameter values from the url.
  - It now has `createFromDataArray()`, which can be used to create a `Route` object from the same type of data set in the `Routing` file in the structure.
- `\Parable\Routing\Router` now has a `getRoutes()` method that returns all set routes. In case you, err, need that. 
- `dynamicReturnTypeMeta.json` has been added, removing the need for `/** @var \Class $var */` references in the code. This works with the dynamic return type plugin in PhpStorm. Removed the few existing references that were there.

__Backwards-incompatible Changes__
- `Bootstrap.php` has been removed. `\Parable\Framework\App` handles its own setup now. This makes it easier to implement App without much hassle.
- `\Parable\Console` no longer accepts options in the format `--option value`, but only in the following: `--option=value`. This is because if you had an option which didn't require a value, and was followed by an argument, the argument would be seen as the option's value instead.
- `\Parable\Console\App::setDefaultCommand()` now takes a command instance rather than the name, as the name would suggest. To set the default command by name, use `setDefaultCommandByName()` instead.
- `\Parable\Console\App::setOnlyUseDefaultCommand()` was added, and the boolean paramater was removed from the `setDefaultCommand/ByName()` function calls. Checked by calling `shouldOnlyUseDefaultCommand()`.
- `\Parable\Console\Command::addOption()` and `addArgument()` no longer take booleans for `required` or `valueRequired` but constants. See `\Parable\Console\Parameter` for the values. This adds the `OPTION_VALUE_PROHIBITED` possibility. Options can no longer be made required.
- `\Parable\Console\Parameter` has received several constants: `PARAMETER_OPTIONAL`, `PARAMETER_REQUIRED`, `OPTION_VALUE_OPTIONAL` and `OPTION_VALUE_REQUIRED`. This replaces the boolean functionality Parable had before. This makes it more readable.
- `\Parable\Console\Parameter::setOptions()` was renamed to `setCommandOptions()`, because the distinction is important.
- `\Parable\Console\Output::writeError/writeInfo/writeSuccess()` are now suffixed with `Block`, so `writeInfoBlock()`, etc.
- `\Parable\DI\Container::cleanName()` has been made protected. This shouldn't impact you, as you shouldn't've been using it in the first place.
- `\Parable\Framework\Dispatcher` no longer passes the Route to actions or callables as the first parameter. All parameters passed will be from the url params, in the same order as defined.
- `\Parable\GetSet\InputStream::extractAndSetData()` has been made protected. See above for why you should totally be fine.
- The interface `\Parable\Log\Writer` has been renamed and moved to `\Parable\Log\Writer\WriterInterface` for clarity and consistency.
- `\Parable\Log\Writer\Terminal` has been renamed to `\Parable\Log\Writer\Console`, because I don't know what was wrong with me when I chose 'terminal'.
- `\Parable\Orm\Model::guessValueType()` has been removed. Everything's a string now. Parable shouldn't change types for you. Honestly, when I added a method that started with `guess`, I should've doubted whether it was the right choice in the first place. Oh well.
- `\Parable\Routing\Route` has lost the ability to use typed params. Too much code for too little gain. If you need typed parameters, I suggest you figure something out for yourself.
- `\Parable\Routing\Route` no longer supports `template` for the template path, but the more correctly named `templatePath` instead. Because of this, it now checks more strictly whether valid properties are set through the Routing array. `setDataFromArray()` attempts to call setters named like the properties. Any that are not available with a setter will throw an Exception. All properties are now also `protected`.
- `\Parable\Routing\Router` now also supports adding a completely set-up `Route` object directly (or in an array), without having to pass them as arrays, through `addRoute()` and `addRoutes()`. These methods already existed, but those are now renamed to `addRouteFromArray()` and `addRoutesFromArray()`.
- Two config keys were renamed: `parable.session.autoEnable` has become `parable.session.auto-enable` and `parable.app.homeDir` has become `parable.app.homedir`. The option for `init-structure` has also become `--homedir`.

__Bugfixes__
- `\Parable\Console\Output` had a bug where moving the cursors would mess with the functionality of `clearLine()`. Line length is no longer kept track of, but whether or not the line is clearable is a boolean value. Moving the cursor up/down or placing it disables line clearing, writing anything enables it again. When you clear the line, the line gets cleared using the terminal width.
- `\Parable\Console\Parameter` had a bug where providing an argument after an option with an `=` sign in it (so `script.php command option=value arg`) would see the argument as the option value and overwrite the actual value. Fixed by @dmvdbrugge in PR #31. Thanks!
- `\Parable\Console\Parameter` had a bug, where false-equivalent values passed to an option would be seen as the option being provided without a value, making the returned value `true`. Fixed by @dmvdbrugge in PR #37. Thanks!
- `\Parable\Filesystem\Path::getDir()` had a bug where if the filename you were trying to get a proper base-dirred path for already existed in the directory the code was run from, it would think it didn't need to and return just the provided path again.
- `\Parable\Framework\Config` had a bug where a class was referenced that doesn't exist until you've run `parable init-structure`. This has been replaced with a string value instead. Found by @dmvdbrugge. Thanks!
- `\Parable\Routing\Router` now sanitizes the Url before trying to match it, stripping html and special characters.

### 0.12.14

__Changes__
- `/bin/vendor/parable`, the command-line tool, now shows the Parable version.
- `\Parable\Rights\Rights` has an improved `combine()` method and gained `getRightsFromNames()` and `getNamesFromRights()` methods, so it's easier to work with.
- `\Parable\Framework\App` has lost its dependency on `\Parable\Filesystem\Path`, since it became obsolete after recent changes.
- `\Parable\ORM\Query` now has `addValues()`, so you can add an array of values instead of having to do them one-by-one.
- `\Parable\ORM\Repository` now has `createInstanceForModelName()`, which is now what `Toolkit::getRepository` calls as well. Toolkit's `getRepository` is sticking around, since it's more useful in views.
- Small fixes to increase code base quality. 

__Bugfixes__
- The `parable init-structure` command used a hard-coded vendor path, which could eventually cause problems.
- Fixed tests up so that all components have 100% code coverage without `Framework` touching all the things. Also improved and simplified some tests.

### 0.12.13

__Changes__
- To ease development a little, try `make server` on a `make`-enabled OS (or run the php built-in webserver yourself with `php-server.php` passed as router script) and it will run (by default) on `http://localhost:5678`.
- `\Parable\Console\Parameter` and `Command` now understand that even arguments deserve default values. Third (optional) parameter added to `addArgument` that will allow for a default value. 
- 5 whitespace issues fixed, based on feedback from StyleCI, using the PSR-2 preset. These were the only(!) style discrepancies found.

__Bugfixes__
- `Undefined offset` fixed in `\Parable\Console\Parameter::checkArguments()`. Requesting arguments that weren't provided no longer results in a notice.

### 0.12.12

__Bugfixes__
- Bug in `\Parable\\Routing\Route::isPartCountSame($url)`, where trailing slash vs no trailing slash would be considered not the same. Fixed in PR #25 by @jerry1970.

### 0.12.11

__Bugfixes__
- I can't believe nobody noticed this yet, but there was a bug in `\Parable\ORM\Query::buildOrderBy()`, where the key is actually escaped through `quote` rather than `quoteIdentifier`, leaving it as a quoted value rather than a key.
- Also `buildGroupBy()`, grmbl.

### 0.12.10

I'm cleaning up this file. All CHANGELOG entries older than the current 'major' pre-release version and the version before that have been moved to CHANGELOG-HISTORY.md.

__Changes__
- `\Parable\GetSet\Base` also got some love. Now, like in `\Parable\Framework\Config`, it's possible to get nested array values with the dot-notation. To get `$resource["one"]["two"]`, you can now use `$config->get("one.two");`. This of course works for all GetSet implementations, so also Post and Get. `$getSet->remove()` also supports this notation now.
- `\Parable\GetSet\Base` can do the same with set now. So to set (and create all currently non-existing) nested keys, you can do this `$config->set("one.two.three", "value");`.
- Since this copies the functionality already built into `\Parable\Framework\Config`, that class has now become an implementation of `\Parable\GetSet\Base`, making it all work exactly the same.

### 0.12.9

__Changes__
- `\Parable\GetSet\BaseInput` has been de-abstracted and renamed. It's now available as `\Parable\GetSet\InputStream`. This deprecates the `Put`, `Patch` and `Delete` GetSetters. This fixes #23.

### 0.12.8

__Bugfixes__
- `\Parable\Http\Url` now has separate base path and script name. So even if you place all but the public files outside of the server's document root, the index.php is still stripped to build the base url. This fixes #22. 

### 0.12.7

__Changes__
- `\Parable\Http\Output\Json` has better error checking in `prepare()`, doing its best to make sure the content ends up as json. If it can't it'll throw an exception. This fixes issue #18.
- `\Parable\Http\Request` has received some love:
  - `getHeader($key)` now also matches on different capitalisation, to make matching easier.
  - `getCurrentUrl()` was added, which builds the current url from `$_SERVER` data.
  - `getHttpHost()` was added, which does its best to return the most reliable value.
  - `getRequestUrl()` was added.
  - `getScriptName()` was added.
  - `getBody()` was added, returning the value of `php://input` passed to the request.
- `ORM\Database` has gained `setCharset()` and `getCharset()`, so it's now possible to specifically set it. This will also be picked up from the config (key `database.charset`). This fixes issue #19. Fixed by @jerry1970.
  
__Bugfixes__
- `\Parable\GetSet\BaseInput` now auto-detects whether data is json or form-data and attempts to load it both ways. PHP doesn't care whether it's `x-www-form-urlencoded` or passed in the body, so it's all about what the data is.
- `\Parable\Model\ORM` was using the literal property `id` to check for a primary key. Now uses `$this->getTableKey()` instead.
- `\Parable\Routing\Route` didn't `rtrim` the / off of the urls. This made matching `url` with `url/` impossible.

### 0.12.6

__Changes__
- `\Parable\Framework\Toolkit::redirectToRoute()` now also accepts a `$parameters` array, to build route Urls with params like `\Parable\Routing\Router::getRouteUrlByName()` does.

__Bugfixes__
- `Init\Example` (structure file) did not yet use or explain the use of `HOOK_x` constants on `\Parable\Framework\App`.

### 0.12.5

__Changes__
- `\Parable\ORM\Model` now has the possibility of `toArrayWithoutEmptyValues` and `exportToArrayWithoutEmptyValues`, and the default `toArray` and `exportToArray` now return empty values.
- `\Parable\ORM\Repository` now offers `buildAndSet()` and `buildOrSet()`. This fixes issue #10.
- All hook `->trigger(...)` calls now make use of constants so that hooking into them is somewhat more comfortable as well. This fixes issue #11.
- It's now possible to add `errorMode` to the database config so you can set the error mode. Use the values defined on `PDO` for this: `ERRMODE_SILENT`, `ERRMODE_WARNING` or `ERRMODE_EXCEPTION`. `ERRMODE_SILENT` is the default, so it's no change for you. Setting more punishing error modes should be a conscious choice. This fixes issue #12.
- Typo in `Init\Example` fixed, was still referencing `initLocations`, whereas that's `inits` now. This fixes #14.
- `\Parable\Http\Request` now no longer sets Method in its `__construct` method, but checks the `$_SERVER` values every time `getMethod` is called.
- `\Parable\Http\Response::setHttpCode()` now throws an Exception when an invalid code is set.

__Bugfixes__
- You can now set a different `homeDir` than `public`, for those who need a different location due to their hosting setup. This makes it possible to keep all app-related code outside of the end-user-accessible dirs. This fixes #13.
- `\Parable\Console` now supports `--option=value` style options, as well as arguments. Simple `$command->addArgument("name", $required = bool)` and you can use them. Arguments are *in-order*, but can be either preceeded or succeeded by options. This fixes #15.
- `\Parable\Http\Response` now returns using the same HTTP protocol it was requested with, since it asks `\Parable\Http\Request` what it should be. This fixes issue #16.  

### 0.12.4

__Bugfixes__
- `\Parable\Framework\Toolkit::redirectToRoute()` did not use `getUrl` to build the url on top of the baseUrl.

### 0.12.2 - 0.12.3

__Bugfixes__
- Left-over but un-DI-ed `$toolkit` property on `\Parable\Framework\View` was preventing use of toolkit in views.
- Removed `composer.lock` since it's useless to end users.

### 0.12.1

__Bugfixes__
- Hook related to dispatching was being triggered twice. Fixed now.

### 0.12.0

__Note: Breaks backwards compatibility. See list below for details how to upgrade from 0.11.x__

__Changes__
- New `Config` array layout. See the example `Config` for the changes!
- `Routing`, `Init` and additional `Config` files now load the same as `Commands` already did. Only `Config\App` is an expected part of Parable, and from there you can just pass in arrays with class names (see new example in structure) and Parable will get the values from them in the order you provide them in. This removes the need for `sortOrder` in Configs, makes `Routing` files easier to split up for readability, and for Inits it really doesn't change much. No more iterating over files! But it does mean you need to add your inits to your Config class and any child Config classes as well.
- `Config` files now need to implement `\Parable\Framework\Interfaces\Config`, no more extends.
- `Routing` files now need to implement `\Parable\Framework\Interfaces\Routing`, no more extends.
- Replaced `strpos` method of deciding whether a method was public or not with `Reflection` logic. Affects `\Parable\ORM\Model::toArray()` and `\Parable\ORM\Model::reset()`.
- No longer add structure folder to the autoload path in `tests/bootstrap.php`, since we no longer use the `_struct` files in testing.
- `\Parable\Framework\App\loadInits()` has been moved up in the run procedure, allowing more triggers: `parable_load_inits_after`, `parable_load_routes_before/after`, `parable_init_database_before/after`, `parable_dispatch_before/after` 

[Check CHANGELOG-HISTORY.md](CHANGELOG-HISTORY.md) for older Changelogs.
