<?php
/**
 * @package     Parable
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 */

namespace Model;

class User extends \Parable\ORM\Model {

    protected $tableName = 'user';
    protected $tableKey = 'id';

    public $username;
    public $password;
    public $email;

    protected $exportable = ['username', 'email'];

}
