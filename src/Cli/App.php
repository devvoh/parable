<?php
/**
 * @package     Parable Framework
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2016, Robin de Graaf, devvoh webdevelopment
 */

namespace Parable\Cli;

class App {

    /** @var \Parable\Filesystem\Path */
    protected $path;

    /** @var \Parable\Framework\Config */
    protected $config;

    /** @var \Parable\Events\Hook */
    protected $hook;

    /** @var \Parable\ORM\Database */
    protected $database;

    /** @var \Parable\Command\Cli */
    protected $cli;

    /** @var array */
    public $arguments;

    /**
     * @param \Parable\Filesystem\Path  $path
     * @param \Parable\Framework\Config $config
     * @param \Parable\Events\Hook      $hook
     * @param \Parable\ORM\Database     $database
     * @param \Parable\Command\Cli      $cli
     */
    public function __construct(
        \Parable\Filesystem\Path  $path,
        \Parable\Framework\Config $config,
        \Parable\Events\Hook      $hook,
        \Parable\ORM\Database     $database,
        \Parable\Command\Cli      $cli
    ) {
        $this->path     = $path;
        $this->config   = $config;
        $this->hook     = $hook;
        $this->database = $database;
        $this->cli      = $cli;
    }

    /**
     * Do all the setup
     *
     * @return $this
     */
    public function run() {
        /* Set the basedir on paths */
        $this->path->setBasedir(BASEDIR);

        /* Load the passed options */
        return $this;
    }

    public function setArguments(array $arguments) {
        unset($arguments[0]);
        foreach ($arguments as $argument) {
            $this->arguments[] = $argument;
        }
        return $this;
    }

    public function parseArguments(array $arguments) {
        $this->setArguments($arguments);

        switch ($this->arguments[0]) {
            case 'init':
                $this->init();
                break;
        }
    }

    protected function init() {
        $this->cli
            ->writeLine('Welcome to Parable. We will now initialize your Parable install.')
            ->br()
            ->writeLine('After completing this process, you will have an environment based on')
            ->writeLine('Parable, complete with folder structure files to start with.')
            ->br()
            ->write('Press a key to continue')
            ->waitForKey();

        $this->cli->writeLine('Creating folder structure...');
        mkdir($this->path->getDir('app'));
        mkdir($this->path->getDir('app/Config'));
        mkdir($this->path->getDir('app/Controller'));
        mkdir($this->path->getDir('app/Model'));
        mkdir($this->path->getDir('app/View'));
        mkdir($this->path->getDir('app/View/Home'));
        mkdir($this->path->getDir('public'));

        $this->cli->writeLine('Copying files...');
        copy($this->path->getDir('vendor/devvoh/parable/structure/.htaccess'),                 $this->path->getDir('.htaccess'));
        copy($this->path->getDir('vendor/devvoh/parable/structure/public/.htaccess'),          $this->path->getDir('public/.htaccess'));
        copy($this->path->getDir('vendor/devvoh/parable/structure/public/index.php'),          $this->path->getDir('public/index.php'));
        copy($this->path->getDir('vendor/devvoh/parable/structure/app/Routes.php'),            $this->path->getDir('app/Routes.php'));
        copy($this->path->getDir('vendor/devvoh/parable/structure/app/Config/App.php'),        $this->path->getDir('app/Config/App.php'));
        copy($this->path->getDir('vendor/devvoh/parable/structure/app/Controller/Home.php'),   $this->path->getDir('app/Controller/Home.php'));
        copy($this->path->getDir('vendor/devvoh/parable/structure/app/Model/User.php'),        $this->path->getDir('app/Model/User.php'));
        copy($this->path->getDir('vendor/devvoh/parable/structure/app/View/Home/index.phtml'), $this->path->getDir('app/View/Home/index.phtml'));
    }

}
