# Parable PHP Framework

### TODO list
- If pre/postDispatch methods exist on controller, execute them at the appropriate moments.
- Add app/ModuleName/Run.php, which should/can have setup, preDispatch and postDispatch methods. These are called
  when appropriate. setup is run after run is called, and pre/postDispatch same as controllers.
- Add css/jss/script/style functionality, probably through a \Devvoh\Parable\App\View\Assets component. Allow scripts
  to be added and removed. These can be added to 'sets', so one could distinguish between header and footer, for ex.
- Router function match should be split into single-responsibility functionality as much as possible. REWRITE.
- Consider whether to use App::method or Parable::getApp()->method. The second is cleaner, but does require
  $app = \Devvoh\Parable\Parable::getApp();. App should be cleaned up significantly, offering lazy loaders and
  nothing more.
- Consider separating 'tool' functionality into \Devvoh\Components\Tools. It could be the only 'mandatory' component,
  and it would allow basedir/publicurl functionality to be used in Components.
- Improve all Components to make them more full-featured, check for errors better.
- Exceptions?
- Query needs to be split into more manageable parts.
- Reconsider differences with PSR-2. Is the stubbornness worth it?
- Verify PSR-4 compatibility.
- Look into phpunit testing
- Translation Component?
- Look into generating the folder structure for Parable (for the install script)

### DONE LIST
- \Devvoh\Parable\Dispatcher: move App's match & execute to it, rename execute to dispatch and clean it up.
- Made a logical mistake when calling App::run what it's called. Boot and run should be separated to allow, for ex.,
  route adding between setting it all up and running App. This would also allow preRun on Run.php.
