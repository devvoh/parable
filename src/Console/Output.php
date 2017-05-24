<?php

namespace Parable\Console;

class Output
{
    /** @var array */
    protected $tags = [
        /* foreground colors */
        'default' => "\e[0m",
        'black'   => "\e[0;30m",
        'red'     => "\e[0;31m",
        'green'   => "\e[0;32m",
        'yellow'  => "\e[0;33m",
        'blue'    => "\e[0;34m",
        'purple'  => "\e[0;35m",
        'cyan'    => "\e[0;36m",
        'white'   => "\e[0;37m",

        /* background colors */
        'black_bg'     => "\e[40m",
        'red_bg'       => "\e[41m",
        'green_bg'     => "\e[42m",
        'yellow_bg'    => "\e[43m",
        'blue_bg'      => "\e[44m",
        'magenta_bg'   => "\e[45m",
        'cyan_bg'      => "\e[46m",
        'lightgray_bg' => "\e[47m",

        /* styles */
        'error'   => "\e[0;37m\e[41m",
        'success' => "\e[0;30m\e[42m",
        'info'    => "\e[0;30m\e[43m",
    ];

    /** @var int */
    protected $lineLength = 0;

    /**
     * @param string $string
     *
     * @return $this
     */
    public function write($string)
    {
        $string = $this->parseTags($string);

        $this->lineLength += strlen($string);

        echo $string;
        return $this;
    }

    /**
     * @param array|string $lines
     *
     * @return $this
     */
    public function writeln($lines)
    {
        if (!is_array($lines)) {
            $lines = [$lines];
        }

        foreach ($lines as $line) {
            $this->write($line);
            $this->newline();
        }
        return $this;
    }

    /**
     * @param int $count
     *
     * @return $this
     */
    public function newline($count = 1)
    {
        $this->lineLength = 0;

        echo str_repeat(PHP_EOL, $count);
        return $this;
    }

    /**
     * @param int $characters
     *
     * @return $this
     */
    public function cursorForward($characters = 1)
    {
        $this->write("\e[{$characters}C");
        return $this;
    }

    /**
     * @param int $characters
     *
     * @return $this
     */
    public function cursorBackward($characters = 1)
    {
        $this->write("\e[{$characters}D");
        return $this;
    }

    /**
     * @param int $characters
     *
     * @return $this
     */
    public function cursorUp($characters = 1)
    {
        $this->write("\e[{$characters}A");
        return $this;
    }

    /**
     * @param int $characters
     *
     * @return $this
     */
    public function cursorDown($characters = 1)
    {
        $this->write("\e[{$characters}B");
        return $this;
    }

    /**
     * @param int $line
     * @param int $column
     *
     * @return $this
     */
    public function cursorPlace($line = 0, $column = 0)
    {
        $this->write("\e[{$line};{$column}H");
        return $this;
    }

    /**
     * @return $this
     */
    public function cls()
    {
        $this->write("\ec");
        return $this;
    }

    /**
     * @return int
     */
    public function getLineLength()
    {
        return $this->lineLength;
    }

    /**
     * @return $this
     */
    public function clearLine()
    {
        // Move back the cursor and replace the existing text with spaces
        $spaces = str_repeat(" ", $this->lineLength);
        $this->write("\e[{$this->lineLength}D{$spaces}");
        // And move the cursor back again
        $this->write("\e[{$this->lineLength}D");

        $this->lineLength = 0;

        return $this;
    }

    /**
     * @param string $string
     *
     * @return $this
     */
    public function writeError($string)
    {
        $this->writeBlock($string, 'error');
        return $this;
    }

    /**
     * @param string $string
     *
     * @return $this
     */
    public function writeInfo($string)
    {
        $this->writeBlock($string, 'info');
        return $this;
    }

    /**
     * @param string $string
     *
     * @return $this
     */
    public function writeSuccess($string)
    {
        $this->writeBlock($string, 'success');
        return $this;
    }

    /**
     * @param string $string
     * @param string $tag
     *
     * @return $this
     */
    public function writeBlock($string, $tag = 'info')
    {
        $strlen = mb_strlen($string);

        $this->writeln([
            "",
            " <{$tag}>┌" . str_repeat("─", $strlen + 2) . "┐</{$tag}>",
            " <{$tag}>│ {$string} │</{$tag}>",
            " <{$tag}>└" . str_repeat("─", $strlen + 2) . "┘</{$tag}>",
            "",
        ]);
        return $this;
    }

    /**
     * @param string $string
     *
     * @return mixed
     */
    public function parseTags($string)
    {
        foreach ($this->tags as $tag => $code) {
            if (strpos($string, "<{$tag}>") !== false
                || strpos($string, "</{$tag}>") !== false
            ) {
                $string = str_replace("<{$tag}>", $code, $string);
                $string = str_replace("</{$tag}>", $this->tags['default'], $string);
            }
        }

        return $string . $this->tags['default'];
    }
}
