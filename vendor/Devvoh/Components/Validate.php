<?php
/**
 * @package     Devvoh Components
 * @license     Validate
 * @author      Robin de Graaf <hello@devvoh.com>
 * @copyright   2015-2016, Robin de Graaf, devvoh webdevelopment
 */

namespace Devvoh\Components;

class Validate {

    /** @var array */
    protected $customTypes = [];

    /**
     * If needed, custom validation types can be added, where closures are given that return true or false (and if not,
     * will be cast to a boolean value anyway).
     *
     * @param string   $type
     * @param callable $closure
     *
     * @return $this
     */
    public function addCustomType($type = null, callable $closure) {
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
     * Run validation on the $data given, using $validators, returning an array with every validation field set to a
     * boolean value.
     *
     * @param string $data
     * @param array  $validators
     * @param bool   $returnBool
     *
     * @return array
     */
    public function run($data, array $validators, $returnBool = true) {
        // We're only going to loop through the values specifically requiring validation. Our results array will
        // not contain the values that we're not validating.
        $results = [];
        foreach ($validators as $key => $typeString) {

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
                $param = null;

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
        }

        // Return the results array
        return $results;
    }

    /**
     * Return a boolean value when validation based on $validators succeeds or fails.
     *
     * @param string $data
     * @param array  $validators
     *
     * @return bool
     */
    public function runBool($data, array $validators) {
        foreach ($this->run($data, $validators) as $result) {
            if (!$result) {
                return false;
            }
        }
        return true;
    }

}
