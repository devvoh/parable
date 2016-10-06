<?php
/**
 * @package     Parable Http
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 */

namespace Parable\Http\Values;

class Session extends \Parable\Http\Values\GetSet
{
    /** @var string */
    protected $resource = '_SESSION';

    /**
     * @return $this
     */
    public function start()
    {
        session_start();
        return $this;
    }

    /**
     * @param bool $deleteOldSession
     *
     * @return $this
     */
    public function regenerateId($deleteOldSession = false)
    {
        session_regenerate_id($deleteOldSession);
        return $this;
    }

    /**
     * @return $this
     */
    public function destroy()
    {
        session_destroy();
        return $this;
    }
}
