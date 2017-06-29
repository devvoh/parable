<?php

namespace Parable\Rights;

class Rights
{
    /** @var array */
    protected $rights = [];

    /**
     * Set up the default rights. Custom rights can be added at any point.
     */
    public function __construct()
    {
        $this->addRight('create');
        $this->addRight('read');
        $this->addRight('update');
        $this->addRight('delete');
    }

    /**
     * Add a right to the list. The correct value is calculated automatically.
     *
     * @param string $name
     *
     * @return $this
     */
    public function addRight($name)
    {
        $rights = $this->getRights();
        if (count($rights) == 0) {
            $value = 1;
        } else {
            $value = 2 * end($rights);
        }
        $this->rights[$name] = $value;
        return $this;
    }

    /**
     * Return all rights
     *
     * @return array
     */
    public function getRights()
    {
        return $this->rights;
    }

    /**
     * Get a specific right by name
     *
     * @param string $name
     *
     * @return int|false
     */
    public function getRight($name)
    {
        if (!isset($this->rights[$name])) {
            return false;
        }
        return $this->rights[$name];
    }

    /**
     * Check if binary number $provided has the right bit for right $name
     *
     * @param string $provided
     * @param string $name
     *
     * @return bool
     */
    public function check($provided, $name)
    {
        $provided = bindec($provided);
        return (bool)($provided & $this->getRight($name));
    }

    /**
     * Combine all right values in $rights into a keep-high combined result
     *
     * Takes an array of binary string values ([00011], [10011], ...])
     *
     * @param array $rights
     *
     * @return string
     */
    public function combine(array $rights)
    {
        $return = [];
        foreach ($rights as $right) {
            for ($i = 0; $i < strlen($right); $i++) {
                if ($right[$i] == '1') {
                    $return[$i] = '1';
                } elseif ($right[$i] !== 1 && !isset($return[$i])) {
                    $return[$i] = 0;
                }
            }
        }
        return implode($return);
    }
}
