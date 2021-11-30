<?php

namespace jinxing\framework\lib;

class Container
{
    /**
     * 用于提供实例的回调函数，真正的容器还会装其他实例等内容
     * @var array
     */
    protected $bindings = [];

    /**
     *
     * 绑定接口和生成相应实例的回调函数
     *
     * @param         $abstract
     * @param         $concrete
     * @param boolean $shared [description]
     * @return void [type]             [description]
     */
    public function bind($abstract, $concrete, $shared = false)
    {
        if (!$concrete instanceof \Closure) {
            $concrete = $this->getClosure($abstract, $concrete);
        }

        $this->bindings[$abstract] = compact('concrete', 'shared');
    }

    protected function getClosure($abstract, $concrete)
    {
        return function ($object) use ($abstract, $concrete) {
            $method = ($abstract == $concrete) ? 'build' : 'make';
            return $object->$method($concrete);
        };
    }

    /**
     * @throws \ReflectionException
     */
    public function make($abstract)
    {
        $concrete = $this->getConcrete($abstract);
        if ($this->isBuildable($concrete, $abstract)) {
            $object = $this->build($concrete);
        } else {
            $object = $this->make($concrete);
        }

        return $object;
    }

    public function isBuildable($concrete, $abstract)
    {
        return $concrete === $abstract || $concrete instanceof \Closure;
    }

    protected function getConcrete($abstract)
    {
        if (!isset($this->bindings[$abstract])) {
            return $abstract;
        }

        return $this->bindings[$abstract]['concrete'];
    }

    /**
     * @throws \ReflectionException
     */
    public function build($concrete)
    {
        if ($concrete instanceof \Closure) {
            return $concrete($this);
        }

        $reflector = new \ReflectionClass($concrete);
        if (!$reflector->isInstantiable()) {
            echo "Target [$concrete] is not instantiable";
        }

        $constructor = $reflector->getConstructor();
        if (is_null($constructor)) {
            return new $concrete;
        }

        $dependencies = $constructor->getParameters();
        $instances = $this->getDependencies($dependencies);
        return $reflector->newInstanceArgs($instances);
    }

    /**
     * @throws \ReflectionException
     */
    protected function getDependencies($parameters)
    {
        $dependencies = [];
        foreach ($parameters as $parameter) {
            $dependency = $parameter->getClass();
            if (is_null($dependency)) {
                $dependencies[] = null;
            } else {
                $dependencies[] = $this->resolveClass($parameter);
            }
        }

        return $dependencies;
    }

    /**
     * @throws \ReflectionException
     */
    protected function resolveClass(\ReflectionParameter $parameter)
    {
        return $this->make($parameter->getClass()->name);
    }
}