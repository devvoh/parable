<?php

namespace Parable\Console\Command;

class Help extends \Parable\Console\Command
{
    /** @var string */
    protected $name = 'help';

    /** @var string */
    protected $description = 'Shows all commands available.';

    public function run()
    {
        $this->output->newline();
        $this->output->writeln("<yellow>{$this->app->getName()}</yellow> " . str_repeat(" ", 36) . "command-line tool");
        $this->output->writeln(str_repeat("-", 69));
        $this->output->writeln('Help screen - available commands:');
        $this->output->newline();

        $longestName = 0;
        foreach ($this->app->getCommands() as $command) {
            $strlen = strlen($command->getName());
            if ($strlen > $longestName) {
                $longestName = $strlen;
            }
        }

        foreach ($this->app->getCommands() as $command) {
            $name = $command->getName();
            $this->output->write(str_pad("    <green>{$name}</green>", $longestName + 22, ' ', STR_PAD_RIGHT));
            $this->output->write("{$command->getDescription()}");
            $this->output->newline();
        }
        $this->output->newline();
        return $this;
    }
}
