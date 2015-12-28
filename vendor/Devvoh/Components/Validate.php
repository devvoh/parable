<?php
/**
 * @package     Devvoh
 * @subpackage  Components
 * @subpackage  Cli
 * @license     Validate
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015 Robin de Graaf, devvoh webdevelopment
 */

namespace Devvoh\Components;

class Validate {

    protected $customTypes = [];

    /**
     * If needed, custom validation types can be added, where closures are given that return true or false (and if not,
     * will be cast to a boolean value anyway).
     *
     * @param null $type
     * @param null $closure
     * @return $this|bool
     */
    public function addCustomType($type = null, $closure = null) {
        if (!$type || !is_callable($closure)) {
            return false;
        }
        $this->customTypes[$type] = $closure;
        return $this;
    }

    /**
     * Return the custom validation types currently set on Validate
     *
     * @return array
     */
    public function getCustomTypes() {
        return $this->customTypes;
    }

    /**
     * Run validation on the $data given, using the validators in $validator. If $returnBool is true, return only
     * an overall true/false value, otherwise return an array with every validation field set to a boolean value.
     *
     * @param null $data
     * @param null $validator
     * @param bool|true $returnBool
     * @return array|bool
     */
    public function run($data = null, $validator = null, $returnBool = true) {
        if (!$data || !$validator) {
            return false;
        }

        // We're only going to loop through the values specifically requiring validation. Our results array will
        // not contain the values that we're not validating.
        $results = [];
        foreach ($validator as $key => $typeString) {

            // If missing, don't need to do the type check
            if (!array_key_exists($key, $data)) {
                if (strpos($typeString, 'required')) {
                    $results[$key] = false;
                    continue;
                }
                continue;
            }

            $types = explode(':', $typeString);

            // Remove required from our validation types, since we've already done it
            $requiredKey = array_search('required', $types);
            if ($requiredKey !== false) {
                unset($types[$requiredKey]);
            }

            // And loop through the remaining types
            foreach ($types as $type) {
                // Some validation types require a parameter, which uses =
                if (strpos($type, '=') !== false) {
                    $parts = explode('=', $type);
                    $type = $parts[0];
                    $param = $parts[1];
                }

                // Set success before validation
                $results[$key] = true;

                // And switch on type
                switch ($type) {
                    case 'exactLength':
                        if (strlen($data[$key]) != $param) {
                            $results[$key] = false;
                            continue;
                        }
                        break;
                    case 'minLength':
                        if (strlen($data[$key]) < $param) {
                            $results[$key] = false;
                            continue;
                        }
                        break;
                    case 'maxLength':
                        if (strlen($data[$key]) > $param) {
                            $results[$key] = false;
                            continue;
                        }
                        break;
                    case 'int':
                        if (!is_int($data[$key])) {
                            $results[$key] = false;
                            continue;
                        }
                        break;
                    case 'string':
                        if (!is_string($data[$key])) {
                            $results[$key] = false;
                            continue;
                        }
                        break;
                }
            }

            foreach ($this->getCustomTypes() as $type => $closure) {
                if (strpos($typeString, $type) !== false) {
                    echo $type . '<br>';
                    $return = $closure($data[$key]);
                    var_dump($return);
                }
            }
        }

        // If we're only supposed to return a boolean value, we're going to have to loop through our results and
        // return false if even one of them is false
        if ($returnBool) {
            foreach ($results as $result) {
                if (!$result) {
                    return false;
                }
            }
            return true;
        }

        // Return the results array
        return $results;
    }

}