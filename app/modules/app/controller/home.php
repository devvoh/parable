<?php
/**
 * @package     Fluid
 * @subpackage  home controller
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

use \Devvoh\Fluid\App as App;

class home extends \Devvoh\Fluid\Controller {

    public function index() {
        App::getDock()->into('test_dock', function(&$payload) {
            $message[] = 'This is how a closure/view dock combination works. Once it\'s triggered,';
            $message[] = 'it will perform the actions in the closure given to ->into, and then the view';
            $message[] = 'file will be opened. ->into can be called from anywhere, while ->trigger should';
            $message[] = 'only be called from view. For purely functional events, use Hooks instead.<br /><br />';
            $message[] = '<b>NOTE:</b> Since self/$this refers to the dock, App can only be reached through full name';
            $message[] = '\Devvoh\Fluid\App, since the scope can\'t be part of App without moving Dock into Fluid.';
            App::getParam()->set('message', implode(' ', $message));
        }, App::getViewDir('dock/test'));
    }

    public function test() {
        echo 'test@home';
    }

    public function viewUser() {
        echo 'View user: ' . App::getParam()->get('id');
        var_dump(App::getParam()->getAll());
    }

    public function viewUserProfile() {
        echo 'Profile of user: ' . App::getParam()->get('name');
        var_dump(App::getParam()->getAll());
    }

}