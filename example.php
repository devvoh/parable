<?php
require_once('./src/Console/App.php');
require_once('./src/Console/Command.php');
require_once('./src/Console/Exception.php');
require_once('./src/Console/Output.php');
require_once('./src/Console/Input.php');
require_once('./src/Console/Parameter.php');
require_once('./src/Console/Parameter/Argument.php');
require_once('./src/Console/Parameter/Option.php');

$app = new \Parable\Console\App(
    new \Parable\Console\Output(),
    new \Parable\Console\Input(),
    new \Parable\Console\Parameter()
);
$command = new \Parable\Console\Command();

$app->setName('Test application');

$command->setName('command');
$command->setDescription('This command does a thing.');
$command->setCallable(function(
    \Parable\Console\Output $output,
    \Parable\Console\Input $input,
    \Parable\Console\Parameter $parameter
) {
    $output->writeln('we ran');

    var_dump($parameter->getCommandName());
});

$app->addCommand($command);

$app->setDefaultCommand($command->getName(), true);

$app->run();