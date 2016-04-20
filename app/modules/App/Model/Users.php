<?php
/**
 * @package     Parable
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 * @license     MIT
 * @author      Robin de Graaf (hello@devvoh.com)
 */

/**
 * NOTE: example model
 * 
 * 1) tableName & tableKey are necessary
 * 2) All public properties can and will be saved upon calling ->save() on this model. 
 * 3) Protected/private properties are not saved.
 * 4) Validator isn't implemented yet, but this shows the general idea. Validation is done left to right, in order,
 *    and it would be wise to leave 'unique' as last (for performance), since it'll make a database connection to check.
 * 5) This is it. This is a working model. Note that the database column names should match these properties 1:1.
 * 6) In case you do want to use different property names, there is a very basic (no error-checking) toMappedArray
 *    method in \Devvoh\Parable\Entity, which will use $entity->getMapper() to get a from => to array with which to map
 *    the fields. You would use $entity->setUseMapper(true) to enable. This has NOT been tested yet, and is basically
 *    pseudo-code. Will be tested and implemented properly later.
 */

namespace App\Model;

class Users extends \Devvoh\Parable\Entity {

    protected $tableName    = 'users';
    protected $tableKey     = 'id';

    public $id;
    public $email;
    public $firstname;
    public $lastname;
    
    protected $validator = [
        'id'        => 'int',
        'email'     => 'email:unique',
        'firstname' => '',
        'lastname'  => '',
    ];

}