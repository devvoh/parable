<?php
/**
 * @package     Fluid
 * @subpackage  home controller
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

namespace App\Controller\Home;

use \Devvoh\Fluid\App as App;

class Test extends \Devvoh\Fluid\Controller {

    public function index() {
        echo 'This is a nested controller, using Home as a namespace node and subfolder, for more fine-grained control.';
    }

}