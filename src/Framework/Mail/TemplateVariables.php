<?php

namespace Parable\Framework\Mail;

class TemplateVariables extends \Parable\GetSet\Base
{
    /** @var string */
    protected $resource = 'mail-template';
    
    /** @var bool */
    protected $useLocalResource = true;
}
