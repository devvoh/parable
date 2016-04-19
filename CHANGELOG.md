# Parable PHP Framework Changelog

### 0.5.0

__Note: This version breaks backwards compatibility!__

__Changes__
- In preparation of future changes, App::run has been split into App::boot (setup) and App::dispatch (actually do the routing). This is mostly in preparation of the future addition of a Dispatcher. Also means run/dispatch no longer has to check whether it's running as a cli process, as the Cli class won't call dispatch.
- \Devvoh\Parable\Dispatcher has been added, and actually executing the route has been pulled out of App. This is also in preparation of pre/postDispatch functionality.
- On Dispatcher, the execute method has been reworked significantly, for better readability and more efficient code. Now also supports using a view key on a controller route, which will be looked for before looking for an auto-generated view path.
- Added /app/modules/[module]/Run.php, which can implement preDispatch & postDispatch methods which will be run at appropriate times. Controller pre/postDispatch coming at a later date.
- \Devvoh\Components\Getset now has a method setMany($array), which will set all key/value pairs in the passed array and add them to the resource.

### 0.4.1

__Changes__
- Components/Database and Components/Query now have a property (and appropriate get/set methods) to set whether 'all' should be quoted.
- Closures should now return their value, after which their return value is added to the Response content.

__Bugfixes__
- Since NULL values are no longer automatically skipped (due to bug fix in 0.3.3), query was trying to set the id even on inserts. sqlite didn't care, but mysql does. TableKey is now always skipped in inserts.
- MySQL mode now works.

### 0.4.0

__Changes__
- Renamed Fluid to Parable. The reason is that the Flow PHP framework has a component called Fluid, which is their template parser. As such, it's become clear that this little project required a new name. As this renames all classes and even some directories, it breaks backwards compatibility. Given that parable is still pre-release software, semantic versioning isn't completely in play yet and it merely raises the version to 0.4.0.

### 0.3.5

__Changes__
- Small one, but removed composer.json again. No use for Parable, which will need an installer like symfony & laravel. For now just releases on github are enough. But I do know how to do it now, so that's something.

### 0.3.4

__Changes__
- Response->sendResponse() now exits and keeps track of whether it's already output the response. This is to prevent it being called double. As soon as sendResponse() has been called, the application stops.
- Response no longer has a __destruct method. If you die/exit, it dies/exits without attempting to output the buffer.
- Moved App logic from index.php into App::run(), which used to be boot() but now also does the routing. Later on, split this into multiple functions.
- App::executeRoute now looks for the view parameter on a route definition regardless of closure or not. This allows overruling the auto-generated template or simply using a specific template if the auto-generated template doesn't exist.
- Added Parable.php cli interpreter & \Devvoh\Parable\Cli class to handle the logic behind Parable.php, see app/modules/App/Cli/Index.php for current implementation (subject to change).
- Added some more information to the index view file.
- Added composer.json (experimental)

__Bugfixes__
- View templates' output now gets sent to the Response Component and appended to the response. Now onlyContent=true stops all echoes and other direct output from php files.
- App::createEntity() now returns an empty Entity object if the requested entity can't be found. This should prevent strange errors down the road (for example, due to App::createRepository() failing to call methods on the entity)
- Entity's id property is now public. It doesn't need to be protected and this prevents IDE mismatches when directly interacting with the property.
- Boot & autoloader now no longer work on heavily customized path logic. Now use a properly set BASEDIR constant.

### 0.3.3

__Changes__
- Added a basic Mailer Component.
- Added App::getCurrentUrl() to App class. Super useful for forms.
- App::CreateRepository() now calls App::createEntity(), which looks in all modules instead of just the current one.
- Config Component now has getBool() function for true/false settings.
- Repository class now also supports orderBy & limit/offset.
- Query Component improved.
- Simplified the doc blocks.
- Improved the Response object somewhat, but it still needs a lot of work.

__Bugfixes__
- Fixed small bugs in Entity class.
- Query Component NULL bug fixed (empty string was being inserted/updated instead of NULL value)
- Bug in SessionMessage Component fixed where upon init and the session key not existing, an undefined index notice would appear.

### 0.3.2

- The Autoloader has been moved into an Autoloader component to be expanded upon as needed. This also makes it possible to add multiple, differently configured Autoloaders when needed.
- All references to ob_* output buffering functions have been moved into the Response object and are now called from there. This makes it clearer when output buffering is used and what type.
- Default output buffering has been changed from using ob_gzhandler to regular, since ob_gzhandler would cause flicker before first byte sometimes. I need to look into this before enabling it again.
- app\modules\Base has been renamed to app\modules\Core since it functions as a 'core' for completely standard functionality such as errors.
- Removed \Devvoh\Parable\Controller as base since it'd be unwise to alter this as a developer building on top of Parable. An update of Parable would overwrite it or an annoying merging issue would arise. Replaced with user-alterable \Core\Controller\Base.
- Added a number of currentModule-related functions to App, most important of which is getModuleFromPath, which is used by Routes.php to determine the module to set on all the routes instead of having to manually set it.
- Changed the 'magic' references to App in index.phtml to self:: instead of $this for consistency's sake.Both still work, to allow the developer to decide which one they'd prefer.
- \Devvoh\Parable\App\View's _call magic method has been updated to use call_user_func_array to properly pass all parameters as they should.
- App::start() has been renamed to App::boot() because it looks nicer. For some reason this is important to me.
- Speaking of pretty things, redesigned the default 'hello' page to be more modern and playful.
- Many less important for the end user improvements (reworked almost all comments, cody style fixes, etc.)

### 0.3.1

- Controllers such as Home\Test (example provided) now work properly, allowing nesting for more fine-grained control over what goes where.
- Removed require_once in App::executeRoute, since the new namespace setup allows the regular autoloader to pick up on controller classes.
- Copyright notices in doc blocks & LICENSE updated, since (of course) it's 2016 now!

### 0.3.0

- Modules now use their own namespaces. app/modules/App/Controller/Home.php would use \App\Controller and Home as class.These are autoloaded by the same PSR-4 compatible autoloader that loads the Components and Parable assets from vendor/.
- enableDebug() has been moved into App.php, to de-clutter index.php even more.

### 0.2.8

- App::redirect($url) will now look if the url is an absolute one, and if not, runs it through App::getUrl($relativeUrl) to get a proper Parable-relevant url. This allows for safe use of App::redirect('/');
- Response content types can now be set by either the shorthand (js) or the full string (application/javascript).
- For those few App::methods() that take a parameter, which is always a maximum of 1, View now recognizes that and passes it as a single variable instead of the usual array.

### 0.2.7

- Repository now has an onlyCount flag, which can be set using Repository->setOnlyCount(bool). If this is set to true, all selects will return an integer corresponding to the number of rows found. All types of Repository calls can be made, getByConditions as well.
- Since controllers sometimes need to extend one another, modelTypes were added. These are, by default, [controller, model], but can be extended with the config key 'model_types_add' (comma-separated list). If you want to autoload test_model, it'll recognize 'model' in the classname and know it's a modelType we know. It'll look in [activeModule]/[modelType]/test.php, where the class is, of course, test_model. By extending this, you can make any type of class load automatically.

### 0.2.6

- Added Devvoh\Component\Validate and the required assets on Devvoh\Parable\Entity
- On Entity->save(), id is now added to the entity for updates/saves later on or to add to other entities
- Quoting table names due to common terms conflicting with sqlite
