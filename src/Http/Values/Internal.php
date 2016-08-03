<?php
/**
 * @package     Parable Http
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 */

namespace Parable\Http\Values;

class Internal extends \Parable\Http\Values\GetSet {

    /** @var string */
    protected $resource = 'internal';
    
    /** @var bool */
    protected $useLocalResource = true;

}
