# Fluid PHP Framework Changelog

### 0.3.4 - in progress

__Changes__
- Response->sendResponse() now exits and keeps track of whether it's already output the response. This is to prevent __destruct() from outputting the response again once the class unloads at the end of a successful render.

__Bugfixes__
- View templates' output now gets sent to the Response Component and appended to the response. Now onlyContent=true stops all echoes and other direct output from php files.

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
- Removed \Devvoh\Fluid\Controller as base since it'd be unwise to alter this as a developer building on top of Fluid. An update of Fluid would overwrite it or an annoying merging issue would arise. Replaced with user-alterable \Core\Controller\Base.
- Added a number of currentModule-related functions to App, most important of which is getModuleFromPath, which is used by Routes.php to determine the module to set on all the routes instead of having to manually set it.
- Changed the 'magic' references to App in index.phtml to self:: instead of $this for consistency's sake.Both still work, to allow the developer to decide which one they'd prefer.
- \Devvoh\Fluid\App\View's __call magic method has been updated to use call_user_func_array to properly pass all parameters as they should.
- App::start() has been renamed to App::boot() because it looks nicer. For some reason this is important to me.
- Speaking of pretty things, redesigned the default 'hello' page to be more modern and playful.
- Many less important for the end user improvements (reworked almost all comments, cody style fixes, etc.)

### 0.3.1

- Controllers such as Home\Test (example provided) now work properly, allowing nesting for more fine-grained control over what goes where.
- Removed require_once in App::executeRoute, since the new namespace setup allows the regular autoloader to pick up on controller classes.
- Copyright notices in doc blocks & LICENSE updated, since (of course) it's 2016 now!

### 0.3.0

- Modules now use their own namespaces. app/modules/App/Controller/Home.php would use \App\Controller and Home as class.These are autoloaded by the same PSR-4 compatible autoloader that loads the Components and Fluid assets from vendor/.
- enableDebug() has been moved into App.php, to de-clutter index.php even more.

### 0.2.8

- App::redirect($url) will now look if the url is an absolute one, and if not, runs it through App::getUrl($relativeUrl) to get a proper fluid-relevant url. This allows for safe use of App::redirect('/');
- Response content types can now be set by either the shorthand (js) or the full string (application/javascript).
- For those few App::methods() that take a parameter, which is always a maximum of 1, View now recognizes that and passes it as a single variable instead of the usual array.

### 0.2.7

- Repository now has an onlyCount flag, which can be set using Repository->setOnlyCount(bool). If this is set to true, all selects will return an integer corresponding to the number of rows found. All types of Repository calls can be made, getByConditions as well.
- Since controllers sometimes need to extend one another, modelTypes were added. These are, by default, [controller, model], but can be extended with the config key 'model_types_add' (comma-separated list). If you want to autoload test_model, it'll recognize 'model' in the classname and know it's a modelType we know. It'll look in [activeModule]/[modelType]/test.php, where the class is, of course, test_model. By extending this, you can make any type of class load automatically.

### 0.2.6

- Added Devvoh\Component\Validate and the required assets on Devvoh\Fluid\Entity
- On Entity->save(), id is now added to the entity for updates/saves later on or to add to other entities
- Quoting table names due to common terms conflicting with sqlite
