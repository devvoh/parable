<?php

namespace Parable\GetSet;

class Session extends \Parable\GetSet\Base
{
    /** @var string */
    protected $resource = '_SESSION';

    /**
     * @return $this
     * @throws \Parable\GetSet\Exception
     */
    public function start()
    {
        if (headers_sent()) {
            throw new \Parable\GetSet\Exception("Headers already sent, can't start the session.");
        }

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
