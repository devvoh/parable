<?php

namespace Parable\Tests\TestClasses;

class FakePDOMySQL extends \Parable\ORM\Database\PDOMySQL
{
    // Overwrite the __construct so we can make a MySQL PDO instance without having to connect.
    public function __construct()
    {
    }
}
