<?php

namespace Botiroff\Gii\Classes;

use ReflectionClass;

class Data
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
        $dataHelper = new Data();

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
                    $expectedType = $propertyType->getName();
                    $actualType = $dataHelper->determineType($actualValue);

                    if ($actualType !== $expectedType && !($propertyType->allowsNull() && $actualType === 'NULL')) {
                        throw new DataException("Property '$propertyName' requires type '$expectedType' but got '$actualType'");
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
        if (is_numeric($value)) {
            return str_contains($value, '.') ? 'float' : 'int';
        } elseif (is_bool($value)) {
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
