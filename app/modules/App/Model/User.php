<?php
/**
 * @package     Parable
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

namespace App\Model;

class User extends \Devvoh\Parable\Entity {

    /** @var string */
    protected $tableName  = 'users';

    /** @var string */
    protected $tableKey   = 'id';

    /** @var array */
    protected $exportable = ['id', 'email', 'created_at', 'updated_at'];

    /** @var int */
    public $id;

    /** @var string */
    public $email;

    /** @var string */
    public $password;

    /** @var string */
    public $created_at;

    /** @var string */
    public $updated_at;

}
