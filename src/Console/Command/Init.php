<?php

namespace Parable\Console\Command;

class Init extends \Parable\Console\Command
{
    /** @var string */
    protected $name = 'init';

    /** @var string */
    protected $description = 'This command initializes a parable structure.';

    /** @var \Parable\Filesystem\Path */
    protected $path;

    public function __construct(
        \Parable\Filesystem\Path $path
    ) {
        $this->path = $path;
    }

    /**
     * @param \Parable\Console\Output $output
     * @param \Parable\Console\Input $input
     * @param \Parable\Console\Parameter $parameter
     *
     * @return $this
     */
    public function run(
        \Parable\Console\Output $output,
        \Parable\Console\Input $input,
        \Parable\Console\Parameter $parameter
    ) {
        $output->writeln([
            "Parable initialization script",
            "-----------------------------------",
            "This script will initialize Parable's structure.",
            "",
            "<red>WARNING</red>",
            "This will overwrite existing files without notice!",
            "",
        ]);

        for (;;) {
            $output->write("Do you want to continue? [y/N] ");
            if ($input->getYesNo(false)) {
                break;
            } else {
                $output->writeln(["", "<red>You chose not to continue.</red>", ""]);
                return $this;
            }
        }

        /** @var \Parable\Filesystem\Path $path */
        $output->newline();
        $output->write('Creating folder structure: ');

        $dirs = [
            'app',
            'app/Command',
            'app/Config',
            'app/Controller',
            'app/Init',
            'app/Model',
            'app/Routing',
            'app/View',
            'app/View/Home',
            'public',
        ];

        foreach ($dirs as $dir) {
            if (!file_exists($this->path->getDir($dir))) {
                mkdir($this->path->getDir($dir));
            }
            $output->write('.');
        }

        $output->writeln(" <green>OK</green>");

        $output->write('Copying files: ');
        copy(
            $this->path->getDir('vendor/devvoh/parable/structure/.htaccess'),
            $this->path->getDir('.htaccess')
        );
        $output->write('.');
        copy(
            $this->path->getDir('vendor/devvoh/parable/structure/public/.htaccess'),
            $this->path->getDir('public/.htaccess')
        );
        $output->write('.');
        copy(
            $this->path->getDir('vendor/devvoh/parable/structure/public/index.php'),
            $this->path->getDir('public/index.php')
        );
        $output->write('.');
        copy(
            $this->path->getDir('vendor/devvoh/parable/structure/app/Command/HelloWorld.php'),
            $this->path->getDir('app/Command/HelloWorld.php')
        );
        $output->write('.');
        copy(
            $this->path->getDir('vendor/devvoh/parable/structure/app/Config/App.php'),
            $this->path->getDir('app/Config/App.php')
        );
        $output->write('.');
        copy(
            $this->path->getDir('vendor/devvoh/parable/structure/app/Controller/Home.php'),
            $this->path->getDir('app/Controller/Home.php')
        );
        $output->write('.');
        copy(
            $this->path->getDir('vendor/devvoh/parable/structure/app/Init/Example.php'),
            $this->path->getDir('app/Init/Example.php')
        );
        $output->write('.');
        copy(
            $this->path->getDir('vendor/devvoh/parable/structure/app/Model/User.php'),
            $this->path->getDir('app/Model/User.php')
        );
        $output->write('.');
        copy(
            $this->path->getDir('vendor/devvoh/parable/structure/app/Routing/App.php'),
            $this->path->getDir('app/Routing/App.php')
        );
        $output->write('.');
        copy(
            $this->path->getDir('vendor/devvoh/parable/structure/app/View/Home/index.phtml'),
            $this->path->getDir('app/View/Home/index.phtml')
        );
        $output->write('.');
        copy(
            $this->path->getDir('vendor/devvoh/parable/structure/app/View/Home/test.phtml'),
            $this->path->getDir('app/View/Home/test.phtml')
        );
        $output->write('.');

        $output->writeln(" <green>OK</green>");

        $output->writeln(["", "<green>Completed!</green>", ""]);
        return $this;
    }
}
