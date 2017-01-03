<?php

namespace Parable\Http\Values;

class Internal extends \Parable\Http\Values\GetSet
{
    /** @var string */
    protected $resource = 'internal';
    
    /** @var bool */
    protected $useLocalResource = true;
}
