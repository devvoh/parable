<?php
/**
 * @package     Devvoh Parable
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 */

namespace Devvoh\Parable;

class Session extends \Devvoh\Components\GetSet {

    /**
     * Set the resource to session
     */
    public function __construct() {
        $this->setResource('session');
    }

    /**
     * Start the session
     *
     * @return $this
     */
    public function startSession() {
        session_start();
        return $this;
    }

    /**
     * Regenerate the session
     *
     * @param bool|false $deleteOldSession
     *
     * @return $this
     */
    public function regenerateSession($deleteOldSession = false) {
        session_regenerate_id($deleteOldSession);
        return $this;
    }

    /**
     * Destroy the session
     *
     * @return $this
     */
    public function destroySession() {
        session_destroy();
        return $this;
    }

}
