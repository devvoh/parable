<?php
/**
 * @package     Devvoh Components
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 */

namespace Devvoh\Parable;

class Init {

    /** @var \Devvoh\Parable\Tool */
    protected $tool;

    /** @var array */
    protected $orderedModules   = [];

    /** @var array */
    protected $unorderedModules = [];

    /** @var bool */
    protected $hasRun           = false;

    /**
     * @param \Devvoh\Parable\Tool $tool
     */
    public function __construct(
        \Devvoh\Parable\Tool $tool
    ) {
        $this->tool = $tool;
    }

    /**
     * Where applicable, load all scripts in app/modules/[name]/Init/
     *
     * @return $this
     */
    public function run() {
        if (!$this->hasRun) {
            if (count($this->orderedModules + $this->unorderedModules) == 0) {
                $this->loadInits();
            }
            $this->runAllInits();
        }
        return $this;
    }

    /**
     * Loads all module inits
     *
     * @return $this
     */
    public function loadInits() {
        foreach ($this->tool->getModules() as $module) {
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
                $className = $module['name'] . '\\Init\\' . $className;
                // And instantiate it
                $class = \Devvoh\Components\DI::get($className);
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
     * @param array $inits
     * @return $this
     */
    protected function runInits(array $inits) {
        foreach ($inits as $init) {
            $init->run();
        }
        return $this;
    }

}