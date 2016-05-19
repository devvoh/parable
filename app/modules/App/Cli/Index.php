<?php
/**
 * @package     Parable
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

namespace App\Cli;

use \Devvoh\Parable\App;

class Index {

    /**
     * @var array
     */
    protected $accessList = [
        'app/storage' => 0777,
        'var' => 0777,
    ];

    /**
     * setAccess will loop through $accessList and apply all given rights to the corresponding paths
     */
    public function setAccess() {
        App::Cli()->write('Setting access...');
        foreach ($this->accessList as $path => $rights) {
            $path = App::getDir($path);
            if (!file_exists($path)) {
                mkdir($path);
            }
            chmod($path, $rights);
            App::Cli()->write('... ' . $path . ' set to ' . base_convert($rights, 10, 8));
            $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
            foreach ($iterator as $item) {
                chmod($item, $rights);
            }
        }
        App::Cli()->write('Done!')->nl();
        return;
    }

    public function removeUser($id, $name) {
        App::Cli()->write(
            'Remove user with id ' . $id . ' and name ' . $name . '...'
        );
    }

}