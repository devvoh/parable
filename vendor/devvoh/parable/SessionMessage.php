<?php
/**
 * @package     Devvoh Components
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 */

namespace Devvoh\Parable;

class SessionMessage {

    /** @var array */
    protected $messages = [];

    /** @var \Devvoh\Parable\Session */
    protected $session;

    /**
     * Initialize the session object and store all current messages on ourself
     *
     * @param \Devvoh\Parable\Session $session
     */
    public function __construct(
        \Devvoh\Parable\Session $session
    ) {
        $this->session = $session;
        if (is_array($this->session->get('messages'))) {
            $this->messages = $this->session->get('messages');
        }
    }

    /**
     * Get all messages or all messages of $type
     *
     * @param null|string $type
     *
     * @return array
     */
    public function get($type = null) {
        if (!$type) {
            return $this->messages;
        }

        if (isset($this->messages[$type])) {
            return $this->messages[$type];
        }

        return [];
    }

    /**
     * Get all messages or all messages of $type and then clear those messages
     *
     * @param null|string $type
     *
     * @return array
     */
    public function getClear($type = null) {
        $messages = $this->get($type);
        $this->clear($type);
        return $messages;
    }

    /**
     * Add a message to type notice by default, or to $type instead
     *
     * @param string $message
     * @param string $type
     *
     * @return $this
     */
    public function add($message, $type = 'info') {
        if (!isset($this->messages[$type]) || !is_array($this->messages[$type])) {
            $this->messages[$type] = [];
        }
        if ($message) {
            $this->messages[$type][] = $message;
        }
        $this->writeToSession();
        return $this;
    }

    /**
     * Clear all messages or all messages of $type
     *
     * @param null|string $type
     *
     * @return $this
     */
    public function clear($type = null) {
        if (!$type) {
            $this->messages = [];
        } elseif (isset($this->messages[$type])) {
            unset($this->messages[$type]);
        }

        $this->writeToSession();
        return $this;
    }

    /**
     * Count all messages or all messages of $type
     *
     * @param null|string $type
     *
     * @return int
     */
    public function count($type = null) {
        if ($type) {
            return count($this->get($type));
        }

        $count = 0;
        foreach ($this->get() as $type => $messages) {
            $count += count($messages);
        }
        return $count;
    }

    /**
     * Write messages stored on ourself to the session
     *
     * @return $this
     */
    protected function writeToSession() {
        $this->session->set('messages', $this->messages);
        return $this;
    }

}
