<?php

namespace lib;

class App
{
    /**
     * 通过反射运行类方法（自动注入依赖）
     * @param  object $instance 类
     * @param  string $method   方法
     * @return mixed
     */
    public static function runMethod($instance, $method)
    {
        if (!method_exists($instance, $method)) {
            return null;
        }

        $reflector = new \ReflectionMethod($instance, $method);
        $parameters = [];

        foreach ($reflector->getParameters() as $key => $parameter) {
            $class = $parameter->getClass();
            if ($class) {
                $parameters[] = new $class->name();
            }
        }

        return call_user_func_array([$instance, $method], $parameters);
    }

    /**
     * 实例化类（自动注入依赖）
     * @param  string $class 类的名称
     * @return object 返回实例化的类
     */
    public static function make($class)
    {
        $reflector = new \ReflectionClass($class);
        if (!$reflector->isInstantiable()) {
            throw new \Exception("Error Processing Request", 1);
        }

        $constructor = $reflector->getConstructor();
        $dependencies = $constructor->getParameters();
        $instances = [];
        foreach ($dependencies as $dependency) {
            $className = $dependency->getClass();
            $instances[] = is_null($className) ? $dependency->getDefaultValue() : new $className->name();
        }

        return $reflector->newInstanceArgs($instances);
    }

    /**
     * 自动运行类的方法（自动完成依赖）
     * @param  string $action 类@方法名称
     * @return mixed
     */
    public static function run($action)
    {
        list($class, $method) = explode('@', $action);
        return static::runMethod(static::make($class), $method);
    }
}
