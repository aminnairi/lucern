<?php

namespace App\Core;

use ReflectionClass;
use ReflectionMethod;

final class Dependency
{
    public static function fromClassName(string $className, array $initialDependencies = []): object
    {
        $reflectionClass = new ReflectionClass($className);

        $contructor = $reflectionClass->getConstructor();

        if ($contructor === null) {
            return $reflectionClass->newInstance();
        }

        $parameters = $contructor->getParameters();

        $dependencies = [];

        foreach ($parameters as $parameter) {
            $parameterType = $parameter->getType();

            if ($parameterType === null) {
                throw new Exception("Cannot resolve parameters of class $className");
            }

            if (array_key_exists($parameter->getType()->getName(), $initialDependencies)) {
                $dependencies[] = $initialDependencies[$parameter->getType()->getName()];
                continue;
            }

            $dependencies[] = self::fromClassName($parameter->getType()->getName());
        }

        return $reflectionClass->newInstanceArgs($dependencies);
    }

    public static function fromMethodName(object $object, string $methodName, array $initialDependencies = []): object
    {
        $reflectionMethod = new ReflectionMethod($object, $methodName);

        $parameters = $reflectionMethod->getParameters();

        $dependencies = [];

        foreach ($parameters as $parameter) {
            $parameterType = $parameter->getType();

            if ($parameterType === null) {
                throw new Exception("Cannot resolve parameters of method $methodName");
            }

            if (array_key_exists($parameter->getType()->getName(), $dependencies)) {
                $dependencies[] = $dependencies[$parameter->getType()->getName()];
                continue;
            }

            $dependencies[] = self::fromClassName($parameter->getType()->getName());
        }

        return $reflectionMethod->invokeArgs($object, $dependencies);
    }
}
