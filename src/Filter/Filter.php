<?php

namespace MNC\RestBundle\Filter;

class Filter implements FilterInterface
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var \Closure
     */
    private $callable;

    /**
     * Filter constructor.
     * @param          $name
     * @param callable $callable
     */
    public function __construct($name, callable $callable)
    {
        $this->name = $name;
        $this->callable = $callable;
    }

    /**
     * @param          $name
     * @param \Closure $callable
     * @return Filter
     */
    public static function createCustom($name, \Closure $callable)
    {
        return new self($name, $callable);
    }

    /**
     * @return string
     */
    public function getTarget()
    {
        $refl = new \ReflectionFunction($this->callable);
        /** @var \ReflectionParameter[] $params */
        $params = $refl->getParameters();
        $type = $params[0]->getType();
        return $type->getName();
    }

    /**
     * @return mixed
     */
    public function applyFilter()
    {
        return \call_user_func_array($this->callable, func_get_args());
    }
}