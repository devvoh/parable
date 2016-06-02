<?php
/**
 * @package     Devvoh Components
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 */

namespace Devvoh\Components;

class Cli {

    /** @var array */
    protected $parameters         = [];

    /** @var int */
    protected $lastProgressLength = 0;

    /** @var array */
    protected $lines              = [];

    /** @var array */
    protected $foreColors = [
        "default"       => "39",
        "black"         => "30",
        "red"           => "31",
        "green"         => "32",
        "yellow"        => "33",
        "blue"          => "34",
        "magenta"       => "35",
        "cyan"          => "36",
        "light_gray"    => "37",
        "dark_gray"     => "90",
        "light_red"     => "91",
        "light_green"   => "92",
        "light_yellow"  => "93",
        "light_blue"    => "94",
        "light_magenta" => "95",
        "light_cyan"    => "96",
        "white"         => "97",
    ];

    /** @var array */
    protected $backColors = [
        "default"       => "49",
        "black"         => "40",
        "red"           => "41",
        "green"         => "42",
        "yellow"        => "43",
        "blue"          => "44",
        "magenta"       => "45",
        "cyan"          => "46",
        "light_gray"    => "47",
        "dark_gray"     => "100",
        "light_red"     => "101",
        "light_green"   => "102",
        "light_yellow"  => "103",
        "light_blue"    => "104",
        "light_magenta" => "105",
        "light_cyan"    => "106",
        "white"         => "107",
    ];

    public function cls() {
        echo "\033[2J";
    }

    /**
     * Write a line ending in a line break
     *
     * @param string $message
     * @param bool   $nl
     *
     * @return $this
     */
    public function out($message, $nl = true) {
        echo $message;
        if ($nl) {
            $this->nl();
        }
        return $this;
    }

    /**
     * Set a single color based on color code
     *
     * @param int $color
     *
     * @return $this
     */
    public function setColor($color) {
        echo "\e[{$color}m";
        return $this;
    }

    /**
     * Set both fore and background colors based on key => code from arrays $foreColors/$backColors
     *
     * @param string $fore
     * @param string $back
     *
     * @return $this
     */
    public function setColors($fore, $back) {
        $foreColor = null;
        $backColor = null;
        if (isset($this->foreColors[$fore])) {
            $foreColor = $this->foreColors[$fore];
        }
        if (isset($this->backColors[$back])) {
            $backColor = $this->backColors[$back];
        }
        if ($foreColor && $backColor) {
            $this->setColor($foreColor . ";" . $backColor);
        } elseif ($foreColor) {
            $this->setColor($foreColor);
        } elseif ($backColor) {
            $this->setColor($backColor);
        }
        return $this;
    }

    /**
     * print_r the $message ending in a line break
     *
     * @param mixed $data
     *
     * @return $this
     */
    public function dump($data) {
        print_r($data);
        $this->nl();
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
     * @param string $question
     * @param bool   $default
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
     * Return to the beginning of the line
     *
     * @return $this;
     */
    public function cr() {
        echo "\r\033[A";
        return $this;
    }

    /**
     * Clear the line and return to the beginning of the line
     *
     * @return $this;
     */
    public function cll() {
        $this->cr();
        echo "\033[K";
        return $this;
    }

    /**
     * Put the cursor on $line/$column
     *
     * @param $line
     * @param $column
     *
     * @return $this;
     */
    public function put($line, $column) {
        echo "\033[" . $line . ";" . $column . "H";
        return $this;
    }

    /**
     * Clean exit of the program
     */
    public function end() {
        exit;
    }

}
