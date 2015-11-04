<?php
/**
 * @package     Fluid
 * @subpackage  Param
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

namespace Devvoh\Fluid\App;

class Param {
    use \Devvoh\Fluid\Traits\GetClassName;
    use \Devvoh\Fluid\Traits\GetSetId;
    use \Devvoh\Fluid\Traits\GetSetValues;
    
    protected $params = array();
}