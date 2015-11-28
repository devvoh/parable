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