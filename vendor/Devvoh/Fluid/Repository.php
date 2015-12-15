<?php
/**
 * @package     Devvoh
 * @subpackage  Fluid
 * @subpackage  Repository
 * @license     MIT
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 */

namespace Devvoh\Fluid;

use \Devvoh\Fluid\App;

class Repository {
    use \Devvoh\Components\Traits\GetClassName;
    use \Devvoh\Components\Traits\MagicGetSet;

    public function createQuery() {
        $query = App::createQuery();
        $query->setTableName($this->getTableName());
        $query->setPdoInstance(App::getDatabase()->getInstance());
        return $query;
    }

    public function getAll() {
        $query = $this->createQuery();
        $db = App::getDatabase()->getInstance();
        var_dump($db);
        echo '<hr />';
        $result = $db->query($query);
        echo 'QUERY:<br />';
        var_dump($result);
    }

    public function getById($id) {
        $query = $this->createQuery();
        $query->where($this->getTableId() . ' = ?', $id);

        $result = App::getDatabase()->query($query);
        var_dump($result);
    }
}