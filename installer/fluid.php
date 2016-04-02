#!/usr/bin/env php
<?php
/**
 * @package     Devvoh Fluid Installer
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 */

class Curl {

    /**
     * @var string
     */
    protected $userAgent = 'fluid/curl';

    /**
     * Return the user agent
     *
     * @return string
     */
    public function getUserAgent() {
        return $this->userAgent;
    }

    /**
     * Sets the user agent
     *
     * @param $userAgent
     *
     * @return $this
     */
    public function setUserAgent($userAgent) {
        $this->userAgent = $userAgent;
        return $this;
    }

    /**
     * Returns the result from loading url
     *
     * @param string $url
     *
     * @return string|false
     */
    public function getContent($url = null) {
        if (!$url) {
            return false;
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->getUserAgent());
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    /**
     * Downloads $url to $filename
     *
     * @param string $url
     * @param string $path
     * @param string $filename
     *
     * @return string|false
     */
    public function download($url = null, $path = null, $filename = null) {
        if (!$url || !$path || !$filename) {
            return false;
        }
        if (!is_dir($path) || !is_writable($path)) {
            return false;
        }
        $filename = $path . DIRECTORY_SEPARATOR . $filename;
        $fp = fopen($filename, 'w+');

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->getUserAgent());
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_exec($ch);
        curl_close($ch);

        fclose($fp);
        return $filename;
    }

}

class Cli {

    /**
     * @var array
     */
    protected $arguments            = [];

    /**
     * @var int
     */
    protected $lastProgressLength   = 0;

    /**
     * @var array
     */
    protected $lines                = [];

    /**
     * Write a line ending in a line break
     *
     * @param $message
     * @return \Devvoh\Components\Cli
     */
    public function write($message) {
        echo $message . PHP_EOL;
        return $this;
    }

    /**
     * print_r the $message ending in a line break
     *
     * @param $message
     * @return $this
     */
    public function dump($message) {
        print_r($message);
        echo PHP_EOL;
        return $this;
    }

    /**
     * Add a line to $this->lines array
     *
     * @param $message
     * @return $this
     */
    public function addLine($message) {
        $this->lines[] = $message;
        return $this;
    }

    /**
     * Output all lines from $this->lines
     *
     * @return $this
     */
    public function writeLines() {
        $output = implode($this->lines, PHP_EOL);
        $this->write($output);
        return $this;
    }

    /**
     * Output a new line
     *
     * @return $this
     */
    public function nl() {
        echo PHP_EOL;
        return $this;
    }

    /**
     * Ask a yes/no question with a $default option and keep asking until a valid answer has been given
     *
     * @param      $question
     * @param bool $default
     *
     * @return bool
     */
    public function yesNo($question, $default = true) {
        // output question and appropriate default value
        echo trim($question) . ($default ? ' [Y/n] ' : ' [y/N] ');
        // get user input from stdin
        $line = fgets(STDIN);
        // turn into lowercase and check specifically for yes and no, call ourselves again if neither
        $value = strtolower(trim($line));

        if (in_array($value, ['y', 'yes'])) {
            return true;
        } elseif (in_array($value, ['n', 'no'])) {
            return false;
        } elseif (empty($value)) {
            // but if it's empty, assume default
            return $default;
        }
        // If nothing has been returned so far, keep asking
        echo "Enter y/yes or n/no.\n";
        return $this->yesNo($question, $default);
    }

    /**
     * Show or update progress message, which will replace itself if called again
     *
     * @param $message
     *
     * @return $this
     */
    public function progress($message) {
        // If lastProgressLength is over 0, this isn't the first progress call
        if ($this->lastProgressLength > 0) {
            // Output 70 spaces and then go back 70 characters, clearing the line
            echo str_repeat(' ', 70) . "\e[70D";
            // Go back [x] characters with the following weird string
            echo "\e[" . $this->lastProgressLength . "D";
        }

        // Set lastProgressLength to new message length
        $this->lastProgressLength = strlen($message);

        echo $message;
        return $this;
    }

    /**
     * Resets the progress last length so progress will not try to go back to the start of the line on next call
     *
     * @return $this
     */
    public function resetProgress() {
        $this->lastProgressLength = 0;
        return $this;
    }

    /**
     * Clean exit of the program
     */
    public function end() {
        $this->writeLines();
        exit;
    }

    public function setArguments($argv) {
        // Remove [0], which is our own name, but keep the remaining keys intact
        unset($argv[0]);
        $this->arguments = $argv;
    }

    public function getArgument($index) {
        if (isset($this->arguments[$index])) {
            return $this->arguments[$index];
        }
        return null;
    }

}

$curl = new Curl();
$cli = new Cli();
$cli->setArguments($argv);

$curlBaseUrl = 'https://api.github.com/repos/devvoh/Fluid/';

switch ($cli->getArgument(1)) {
    case 'new':
        if (!$cli->getArgument(2)) {
            $cli->write('Second parameter required.');
            $cli->end();
        }
        $cli->write('Download & install into ./' . $cli->getArgument(2));
        if (!$cli->getArgument(3)) {
            $cli->write('No version given, getting latest release...');
            $result = json_decode($curl->getContent($curlBaseUrl . 'releases'));
            $latest = $result[0];

            $downloadName       = 'fluid-' . $latest->tag_name;
            $downloadFilename   = $downloadName . '.zip';
            $downloadDirectory  = getcwd() . DIRECTORY_SEPARATOR;

            $downloadPath       = $downloadDirectory . $downloadFilename;
            $extractToPath      = $downloadDirectory . uniqid('fluid_');

            $cli->write('Downloading release ' . $latest->tag_name . ' to ' . $downloadPath . '...');

            $curl->download($latest->zipball_url, $downloadDirectory, $downloadFilename);

            $cli->write('Unpacking ' . $downloadPath . '...');

            mkdir($extractToPath);
            $zipArchive = new ZipArchive();
            if ($zipArchive->open($downloadPath)) {
                $zipArchive->extractTo($extractToPath);
                $zipArchive->close();
            }
            unlink($downloadPath);

            $targetDir = getcwd() . '/' . $cli->getArgument(2);

            mkdir($targetDir);
            $recursiveDir = new RecursiveDirectoryIterator($extractToPath, RecursiveDirectoryIterator::SKIP_DOTS);
            $sourceDir = new RecursiveDirectoryIterator($recursiveDir->getPathname(), RecursiveDirectoryIterator::SKIP_DOTS);
            $sourceIterator = new RecursiveIteratorIterator($sourceDir, RecursiveIteratorIterator::SELF_FIRST, RecursiveIteratorIterator::CATCH_GET_CHILD);

            $dirsToBeDeleted = [];
            foreach ($sourceIterator as $item) {
                $targetFile = str_replace($recursiveDir->getPathname(), '', $item->getPathname()) . PHP_EOL;
                $targetFile = trim($targetDir . $targetFile);
                $sourceFile = trim($item->getPathname());

                // Exclude .gitignore & remove the original so we can remove the dirs later
                if ($item->getFilename() == '.gitignore') {
                    unlink($sourceFile);
                    continue;
                }

                if (is_dir($sourceFile)) {
                    mkdir($targetFile);
                    $dirsToBeDeleted[] = $sourceFile;
                } elseif (is_file($sourceFile)) {
                    rename($sourceFile, $targetFile);
                }
            }
            usort($dirsToBeDeleted, function($a, $b) {
                return strlen($b) - strlen($a);
            });
            foreach ($dirsToBeDeleted as $dirToBeDeleted) {
                rmdir($dirToBeDeleted);
            }
            rmdir($recursiveDir->getPathname());
            rmdir($extractToPath);
        }
        break;
    case 'help':
    default:
        $cli->write('help');
        break;
}