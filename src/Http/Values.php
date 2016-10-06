<?php
/**
 * @package     Parable Http
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 */

namespace Parable\Http;

class Values
{
    /** @var \Parable\Http\Values\Get */
    public $get;

    /** @var \Parable\Http\Values\Post */
    public $post;

    /** @var \Parable\Http\Values\Cookie */
    public $cookie;

    /** @var \Parable\Http\Values\Session */
    public $session;

    /** @var \Parable\Http\Values\Internal */
    public $internal;

    /**
     * @param Values\Get      $get
     * @param Values\Post     $post
     * @param Values\Cookie   $cookie
     * @param Values\Session  $session
     * @param Values\Internal $internal
     */
    public function __construct(
        \Parable\Http\Values\Get      $get,
        \Parable\Http\Values\Post     $post,
        \Parable\Http\Values\Cookie   $cookie,
        \Parable\Http\Values\Session  $session,
        \Parable\Http\Values\Internal $internal
    ) {
        $this->get      = $get;
        $this->post     = $post;
        $this->cookie   = $cookie;
        $this->session  = $session;
        $this->internal = $internal;
    }
}
