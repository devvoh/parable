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
        $repo = new test_repository();
        $repo->getAll();
        die();
    }

}