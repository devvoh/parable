<?php
/**
 * @package     Parable
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

namespace App\Model;

class Test extends \Devvoh\Parable\Entity {

    protected $user;

    public function __construct(
        \App\Model\User $user,
        \Devvoh\Components\Debug $debug
    ) {
        $this->user = $user;
        
        $debug->pd(['it' => 'worked!']);
    }

}