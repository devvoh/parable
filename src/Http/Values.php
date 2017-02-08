<?php

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

    public function __construct(
        \Parable\Http\Values\Get $get,
        \Parable\Http\Values\Post $post,
        \Parable\Http\Values\Cookie $cookie,
        \Parable\Http\Values\Session $session,
        \Parable\Http\Values\Internal $internal
    ) {
        $this->get      = $get;
        $this->post     = $post;
        $this->cookie   = $cookie;
        $this->session  = $session;
        $this->internal = $internal;
    }
}
