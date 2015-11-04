<?php
/**
 * @package     Fluid
 * @subpackage  Session
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

namespace Devvoh\Fluid\App;

class Session {
    use \Devvoh\Fluid\Traits\GetClassName;
    use \Devvoh\Fluid\Traits\GetSetValues;

    public function start($name = 'fluid') {
        if ($name) {
            session_name($name);
        }
        session_start();
    }

    public function destroy($name = 'fluid') {
        if ($name) {
            session_name($name);
        }
        session_destroy();
    }

}