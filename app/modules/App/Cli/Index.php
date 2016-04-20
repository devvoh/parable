<?php
/**
 * @package     Parable
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

namespace App\Cli;

class Index {
    use \Devvoh\Parable\AppTrait;

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
        $this->app->getCli()->write('Setting access...');
        foreach ($this->accessList as $path => $rights) {
            $path = $this->app->getDir($path);
            if (!file_exists($path)) {
                mkdir($path);
            }
            chmod($path, $rights);
            $this->app->getCli()->write('... ' . $path . ' set to ' . base_convert($rights, 10, 8));
            $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
            foreach ($iterator as $item) {
                chmod($item, $rights);
            }
        }
        $this->app->getCli()->write('Done!')->nl();
        return;
    }

    public function removeUser($id, $name) {
        $this->app->getCli()->write('Remove user with id ' . $id . ' and name ' . $name . '...');
    }

}