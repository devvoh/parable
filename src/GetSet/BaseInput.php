<?php

namespace Parable\GetSet;

abstract class BaseInput extends \Parable\GetSet\Base
{
    public function __construct()
    {
        parse_str(file_get_contents('php://input'), $data);
        $this->setAll($data);
    }
}
