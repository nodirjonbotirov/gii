<?php

namespace Botiroff\Gii\Classes;

use ReflectionClass;

class CustomData
{

    /**
     * @param array $props
     * @return static
     * @throws DataException
     */
    public static function from(array $props): static
    {
        $childClass = new static();
        $reflection = new ReflectionClass($childClass);
        $dataHelper = new CustomData();

        foreach ($reflection->getProperties() as $property) {
            $propertyName = $property->getName();
            $propertyType = $property->getType();
            if (is_null($propertyType)) {
                $childClass->$propertyName = $props[$propertyName];
            } else {
                if (!$propertyType->allowsNull() && !array_key_exists($propertyName, $props)) {
                    throw new DataException("Property '$propertyName' in child class is not assigned and does not allow null");
                }

                if (array_key_exists($propertyName, $props)) {
                    $actualValue = $props[$propertyName];
                    $actualType = $dataHelper->determineType($actualValue);

                    if (!method_exists($propertyType, 'getTypes')) {
                        $expectedType = $propertyType->getName();

                        if ($actualType !== $expectedType && !($propertyType->allowsNull() && $actualType === 'NULL')) {
                            throw new DataException("Property '$propertyName' requires type '$expectedType' but got '$actualType'");
                        }
                    } else {
                        $allow = false;
                        $expectedPropertyTypes = '';
                        foreach ($propertyType->getTypes() as $type) {
                            $expectedPropertyTypes .= $type->getName() . ', ';
                            if ($actualType == $type->getName() || ($actualType == 'NULL' && $type->getName() == 'null')) {
                                $allow = true;
                            }
                        }
                        if (!$allow)
                            throw new DataException("Property '$propertyName' requires '$expectedPropertyTypes' type  but got '$actualType'");

                    }

                    $childClass->$propertyName = $actualValue;
                } elseif ($propertyType->allowsNull()) {
                    $childClass->$propertyName = null;
                }
            }
        }

        return $childClass;
    }


    private function determineType($value): string
    {
        if (is_bool($value)) {
            return 'bool';
        } elseif (is_array($value)) {
            return 'array';
        } elseif (is_object($value)) {
            return get_class($value);
        } elseif (is_resource($value)) {
            return 'resource';
        } else {
            return gettype($value);
        }
    }

    /**
     * @return array
     */
    public function all(): array
    {
        return get_object_vars($this);
    }
}
