<?php

namespace Parable\Console;

class Output
{
    /** @var array */
    protected $tags = [
        /* foreground colors */
        'default'      => "\e[0m",
        'black'        => "\e[0;30m",
        'red'          => "\e[0;31m",
        'green'        => "\e[0;32m",
        'yellow'       => "\e[0;33m",
        'blue'         => "\e[0;34m",
        'purple'       => "\e[0;35m",
        'cyan'         => "\e[0;36m",
        'white'        => "\e[0;37m",

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
        'error'        => "\e[0;37m\e[41m",
        'success'      => "\e[0;30m\e[42m",
        'info'         => "\e[0;30m\e[43m",
    ];

    /** @var bool */
    protected $clearLineEnabled = false;

    /**
     * Write a string to the console.
     *
     * @param string $string
     *
     * @return $this
     */
    public function write($string)
    {
        $string = $this->parseTags($string);

        $this->enableClearLine();

        echo $string;
        return $this;
    }

    /**
     * Return the terminal width. If not an interactive shell, return default 80;
     *
     * @return int
     */
    public function getTerminalWidth()
    {
        // If running on windows or not an interactive shell, just pretend it's 80
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' || !posix_isatty(0)) {
            // @codeCoverageIgnoreStart
            return 80;
            // @codeCoverageIgnoreEnd
        }
        return (int)shell_exec("tput cols");
    }

    /**
     * Write a line or array of lines to the console. This will always end in a newline.
     *
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
     * Write a newline (or multiple) to the console.
     *
     * @param int $count
     *
     * @return $this
     */
    public function newline($count = 1)
    {
        $this->disableClearLine();

        echo str_repeat(PHP_EOL, $count);
        return $this;
    }

    /**
     * Move the cursor forward by $characters places and reset the lineLength.
     *
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
     * Move the cursor backward by $characters places and reset the lineLength.
     *
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
     * Move the cursor up by $characters places and reset the lineLength.
     *
     * @param int $characters
     *
     * @return $this
     */
    public function cursorUp($characters = 1)
    {
        $this->write("\e[{$characters}A");
        $this->disableClearLine();
        return $this;
    }

    /**
     * Move the cursor down by $characters places and reset the lineLength.
     *
     * @param int $characters
     *
     * @return $this
     */
    public function cursorDown($characters = 1)
    {
        $this->write("\e[{$characters}B");
        $this->disableClearLine();
        return $this;
    }

    /**
     * Place the cursor on $line and $column and reset the lineLength.
     *
     * @param int $line
     * @param int $column
     *
     * @return $this
     */
    public function cursorPlace($line = 0, $column = 0)
    {
        $this->write("\e[{$line};{$column}H");
        $this->disableClearLine();
        return $this;
    }

    /**
     * Reset the cursor position to the start of the current line you're on.
     *
     * @return $this
     */
    public function cursorReset()
    {
        $this->write("\r");
        return $this;
    }

    /**
     * Clear the screen.
     *
     * @return $this
     */
    public function cls()
    {
        $this->disableClearLine();

        $this->write("\ec");
        return $this;
    }

    /**
     * Enables clearing the line. This is dependent on staying on the same line.
     *
     * @return $this
     */
    public function enableClearLine()
    {
        $this->clearLineEnabled = true;
        return $this;
    }

    /**
     * Disables clearing the line. Moving the cursor up or down or clearing the screen will do this.
     *
     * @return $this
     */
    public function disableClearLine()
    {
        $this->clearLineEnabled = false;
        return $this;
    }

    /**
     * Return whether or not line clearing is currently enabled.
     *
     * @return bool
     */
    public function isClearLineEnabled()
    {
        return $this->clearLineEnabled;
    }

    /**
     * Clear the line, based on the current lineLength.
     *
     * @return $this
     */
    public function clearLine()
    {
        if (!$this->isClearLineEnabled()) {
            return $this;
        }

        $this->cursorReset();
        $this->write(str_repeat(" ", $this->getTerminalWidth()));
        $this->cursorReset();

        $this->disableClearLine();

        return $this;
    }

    /**
     * Write an error block to the console.
     *
     * @param string $string
     *
     * @return $this
     */
    public function writeErrorBlock($string)
    {
        $this->writeBlock($string, 'error');
        return $this;
    }

    /**
     * Write an info block to the console.
     *
     * @param string $string
     *
     * @return $this
     */
    public function writeInfoBlock($string)
    {
        $this->writeBlock($string, 'info');
        return $this;
    }

    /**
     * Write a success block to the console.
     *
     * @param string $string
     *
     * @return $this
     */
    public function writeSuccessBlock($string)
    {
        $this->writeBlock($string, 'success');
        return $this;
    }

    /**
     * Write a block of text to the console, using a tag (info by default).
     *
     * @param string $string
     * @param string $tag
     *
     * @return $this
     */
    public function writeBlock($string, $tag = 'info')
    {
        $this->writeBlockWithTags($string, [$tag]);
        return $this;
    }

    /**
     * Write a block of text to the console, applying all tags appropriately.
     *
     * @param string   $string
     * @param string[] $tags
     *
     * @return $this
     */
    public function writeBlockWithTags($string, array $tags = [])
    {
        $strlen = mb_strlen($string);

        $tagsOpen  = "";
        $tagsClose = "";
        if (count($tags) > 0) {
            foreach ($tags as $tag) {
                $tagsOpen  .= "<{$tag}>";
                $tagsClose .= "</{$tag}>";
            }
        }

        $this->writeln([
            "",
            " {$tagsOpen}┌" . str_repeat("─", $strlen + 2) . "┐{$tagsClose}",
            " {$tagsOpen}│ {$string} │{$tagsClose}",
            " {$tagsOpen}└" . str_repeat("─", $strlen + 2) . "┘{$tagsClose}",
            "",
        ]);
        return $this;
    }

    /**
     * Parse tags in a string to turn them into bash escape codes.
     *
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
