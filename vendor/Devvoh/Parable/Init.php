<?php
/**
 * @package     Devvoh Components
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 */

namespace Devvoh\Parable;

use \Devvoh\Parable\App;

class Init {

    protected $orderedModules    = [];
    protected $unorderedModules  = [];
    protected $hasRun           = false;

    /**
     * Where applicable, load all scripts in app/modules/[name]/Init/
     *
     * @return $this
     */
    public function run() {
        if ($this->hasRun) {
            return;
        }
        if (count($this->orderedModules + $this->unorderedModules) == 0) {
            $this->loadInits();
        }
        $this->runAllInits();
        return $this;
    }

    /**
     * Loads all module inits
     *
     * @return $this
     */
    public function loadInits() {
        foreach (App::getModules() as $module) {
            // Build init path for this module
            $initPath = $module['path'] . DS . 'Init';
            // If there's no init path, just go onto the next module
            if (!file_exists($initPath)) {
                continue;
            }
            // Generate an iterator for our files
            $dirIt = new \RecursiveDirectoryIterator($initPath, \RecursiveDirectoryIterator::SKIP_DOTS);
            foreach ($dirIt as $file) {
                // Skip non-php files
                if (strpos($file->getFileName(), '.php') === false) {
                    continue;
                }
                // Generate the class to instantiate
                $className = str_replace('.php', '', $file->getFileName());
                $className = '\\' . $module['name'] . '\\Init\\' . $className;
                // And instantiate it
                $class = new $className();
                // Get the order, if any
                if (!property_exists($class, 'order')) {
                    $this->unorderedModules[] = $class;
                } else {
                    $this->orderedModules[$class->order] = $class;
                }
            }
        }
        // And make sure the orderedModules are... y'know, sorted properly.
        if (count($this->orderedModules) > 0) {
            ksort($this->orderedModules);
        }
        return $this;
    }

    /**
     * Runs both ordered and unordered init lists
     *
     * @return $this
     */
    protected function runAllInits() {
        $this->runInits($this->orderedModules);
        $this->runInits($this->unorderedModules);
        $this->hasRun = true;
        return $this;
    }

    /**
     * Run passed inits
     *
     * @param $inits
     *
     * @return $this
     */
    protected function runInits($inits) {
        foreach ($inits as $init) {
            $init->run();
        }
        return $this;
    }

}