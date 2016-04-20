<?php
/**
 * @package     Devvoh Parable
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 */

namespace Devvoh\Parable;

class Cli {
    use \Devvoh\Parable\AppTraitStatic;

    /**
     * @var array
     */
    protected static $cliModules = [];

    /**
     * @var array
     */
    protected static $commands = [];

    /**
     * @var array
     */
    protected static $parameters = [];

    /**
     * Set up everything for our cli app
     *
     * @param $argv
     */
    public static function boot($argv) {
        self::initApp();

        self::$app->getCli()->write('Parable CLI - ' . self::$app->getVersion());
        self::$app->getCli()->write(str_repeat('-', 70));
        self::loadModules();
        self::populateCommands();
        self::populateParameters($argv);
    }

    /**
     * Run the app
     */
    public static function run() {
        if (count(self::$parameters) == 0) {
            self::showCommands();
        }
        foreach (self::$parameters as $key => $parameter) {
            if (count($parameter) > 1) {
                // A command
                self::handleCommand($key, $parameter);
            }
        }
    }

    /**
     * Load all the modules
     */
    protected static function loadModules() {
        $modules = self::$app->getModules();
        foreach ($modules as $module) {
            $cliClass = '\\' . $module['name'] . '\Cli\Index';
            if (class_exists($cliClass)) {
                self::$cliModules[$module['name']] = [
                    'name' => $module['name'],
                    'path' => $module['path'],
                    'instance' => new $cliClass(),
                ];
            }
        }
    }

    /**
     * Populate the commands based on the modules loaded
     */
    protected static function populateCommands() {
        foreach (self::$cliModules as $module) {
            $methods = get_class_methods($module['instance']);
            foreach ($methods as $method) {
                $r = new \ReflectionMethod($module['instance'], $method);
                $params = $r->getParameters();
                $parameters = [];
                foreach ($params as $param) {
                    $parameters[] = $param->name;
                }
                self::$commands[$module['name']][$method] = [
                    'method' => $method,
                    'parameters' => $parameters,
                ];
            }
        }
    }

    /**
     * Populate the parameters based on the arguments given on the cli
     *
     * @param $argv
     */
    protected static function populateParameters($argv) {
        // Remove the first element, as it'll be the script called
        array_shift($argv);

        foreach($argv as $argument) {
            self::$parameters[] = explode(':', $argument);
        }
    }

    /**
     * Handle the command based on its index in the argument list and the command found
     *
     * @param $index
     * @param $command
     */
    protected static function handleCommand($index, $command) {
        $commandModule = $command[0];
        $commandAction = $command[1];

        // Sanity check
        if (!isset(self::$commands[$commandModule]) || !isset(self::$commands[$commandModule][$commandAction])) {
            self::endInvalidCommand($command);
        }

        $module = self::$cliModules[$commandModule];
        $commandData = self::$commands[$commandModule][$commandAction];
        $instance = $module['instance'];

        $parameters = [];
        $missingParameters = [];
        for ($i = 0; $i < count($commandData['parameters']); $i++) {
            if (!isset(self::$parameters[$index+1+$i][0])) {
                $missingParameters[] = $commandData['parameters'][$i];
                continue;
            }
            $parameter = self::$parameters[$index+1+$i][0];
            $parameters[] = $parameter;
        }
        if (count($missingParameters) > 0) {
            self::endMissingParameters($command, $missingParameters);
        }
        // Everything is okay, call it
        call_user_func_array([$instance, $commandAction], $parameters);
    }

    /**
     * Show all found commands in all found modules
     */
    public static function showCommands() {
        self::$app->getCli()->write('The following commands are available:');
        foreach (self::$commands as $module => $commands) {
            self::$app->getCli()->nl();
            foreach ($commands as $command) {
                $output = str_pad('    ' . $module . ':' . $command['method'], 50, ' ', STR_PAD_RIGHT);
                $output .= count($command['parameters']) . ' parameters';
                self::$app->getCli()->write($output);
            }
            self::$app->getCli()->nl();
        }
    }

    /**
     * End due to invalid $command
     *
     * @param $command
     */
    public static function endInvalidCommand($command) {
        self::$app->getCli()->addLine('Invalid command: ' .  $command[0] . ':' . $command[1])->end();
    }

    /**
     * End due to $missingParameters of $command
     *
     * @param $command
     * @param $missingParameters
     */
    public static function endMissingParameters($command, $missingParameters) {
        $missingParameters = implode(', ', $missingParameters);
        self::$app->getCli()->addLine('Cannot run command ' . $command[0] . ':' . $command[1]);
        self::$app->getCli()->addLine('Missing parameters: ' . $missingParameters);
        self::$app->getCli()->end();
    }

}