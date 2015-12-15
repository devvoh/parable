<?php
/**
 * @package     Fluid
 * @subpackage  test model entity
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

use \Devvoh\Fluid\App as App;

class test_entity extends \Devvoh\Fluid\Entity {

    protected $id;
    protected $name;

    protected $validator = [
        'id' => 'int',
        'name' => 'alphanum:minchar6:maxchar24:unique',
    ];

}