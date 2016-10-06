<?php
/**
 * @package     Parable
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 */

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
