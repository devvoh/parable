<?php

namespace Parable\Framework\Mail;

class TemplateVariables extends \Parable\Http\Values\GetSet
{
    /** @var string */
    protected $resource = 'mail-template';
    
    /** @var bool */
    protected $useLocalResource = true;
}
