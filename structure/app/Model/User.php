<?php

namespace Model;

class User extends \Parable\ORM\Model
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

    /** @var array */
    protected $exportable = ['username', 'email'];
}
