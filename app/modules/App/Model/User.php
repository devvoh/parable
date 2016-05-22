<?php
/**
 * @package     Parable
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

namespace App\Model;

class User extends \Devvoh\Parable\Entity {

    protected $tableName    = 'users';
    protected $tableKey     = 'id';

    public $id;
    public $email;
    public $password;
    public $created_at;
    public $updated_at;

    protected $exportable = ['id', 'email', 'created_at', 'updated_at'];

}