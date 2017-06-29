TODO
====

- `Session` should throw an exception on start() if headers already sent.
- `Output\Html` should work with strings, ints, floats and null values. Anything else, Exception.
- `Output\Json` should actually work with objects, arrays and json strings. Anything else, Exception.
- `Response::returnAllOutputBuffers` should set $content at first and then just return that when no outputbuffering
- `Response::redirect` should throw an Exception if headers have been sent
- `Logger` has a space too many in the `$writer` property
- `Logger::write` should not append a PHP_EOL. The individual writers need to take responsibility.
- `Logger::write` should not `trim()`. Nothing should. You should be able to prepend spaces.
- `Logger::write` has an enter too many after it.
- `Log\Writer\File::setLogFile` should `is_readable()` and throw an Exception if not possible.
- `Log\Writer\File::write` should say what log file couldn't be written to.
- `Log\Writer\StdOut::write` remove enter before return statement.
- `Log\Writer\StdOut::write` should be renamed to `Log\Writer\Console`.
- `Mail\Mailer::getAddresses` should return all addresses, and `getAddressesByType($type)` should do the type thing
- `Mail\Mailer::sendMail` should not implode the headers
- `ORM\Database::getInstance` should throw an Exception in the `default` case, since only Sqlite and MySQL are supported.
- `ORM\Database::createPDOSQLite` should build the DSN itself, passing only the location
- `ORM\Database::createPDOMySQL` should build the DSN itself, passing location, db, user, pass
- `ORM\Model` should put all the toArray functions together.
- `ORM\Query::setAction` `validString` should be called `acceptedValuesString`
- `ORM\Query::buildJoins` is the count really necessary? wouldn't `implode` already return an empty string? Or would it always return a space?
- `ORM\Query` needs to support `HAVING`
- `ORM\Query::__toString` says `// Without select values there's no update`, but wtf does that mean? Same for `delete`
- `ORM\Repository` has `an model`. Really, me? Pff.
- `Routing\Route::__construct` checks `$methods` but should set to `[]` instead of `null` if it's not set
- `Routing\Route::__construct` does `(!is_array($this->methods))` so the `null` value above triggers it. Add `empty()` instead
- `Routing\Route::checkParameterValue` at line 127 does not need to return `false` anymore 

And that seems to be it!

Then inspect the code and make sure PSR-2 is properly followed.