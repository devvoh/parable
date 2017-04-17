<?php

namespace Parable\Console\Command;

class Help extends \Parable\Console\Command
{
    /** @var string */
    protected $name = 'help';

    /** @var string */
    protected $description = 'Shows all commands available.';

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
        $output->writeln("<yellow>{$this->app->getName()} help</yellow>");
        $output->writeln('--------------------------------------------------');
        $output->writeln('Available commands:');
        $output->newline();

        $longestName = 0;
        foreach ($this->app->getCommands() as $command) {
            $strlen = strlen($command->getName());
            if ($strlen > $longestName) {
                $longestName = $strlen;
            }
        }

        foreach ($this->app->getCommands() as $command) {
            $name = $command->getName();
            $output->write(str_pad("    <green>{$name}</green>", $longestName + 22, ' ', STR_PAD_RIGHT));
            $output->write("{$command->getDescription()}");
            $output->newline();
        }
        $output->newline();
        return $this;
    }
}
