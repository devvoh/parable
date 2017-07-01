<?php

namespace Parable\Tests\TestClasses;

class Model extends \Parable\ORM\Model
{
    /** @var string */
    protected $tableName = 'user';
    /** @var string */
    protected $tableKey  = 'id';

    /** @var string */
    public $username;
    /** @var string */
    public $password;
    /** @var string */
    public $email;
    /** @var string */
    public $created_at;
    /** @var string */
    public $updated_at;

    /** @var array */
    protected $exportable = ['username', 'email'];
}
