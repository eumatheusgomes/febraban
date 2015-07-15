<?php
namespace EuMatheusGomes\Febraban\Util;

trait GetterSetter
{
    public function __call($method, $args)
    {
        if (preg_match('/(set|get)([A-Z][a-zA-Z]*)/', $method, $matches) === 0) {
            throw new \BadMethodCallException("Invalid method name: {$method}");
        }

        $type = $matches[1];
        $attr = lcfirst($matches[2]);

        if (!property_exists($this, $attr)) {
            throw new \BadMethodCallException("Invalid attribute name: {$attr}");
        }

        if ($type == 'get') {
            return $this->{$attr};
        }

        if (!is_array($args) || count($args) == 0) {
            throw new \InvalidArgumentException("Missing method arguments: {$method}");
        }

        if (count($args) > 1) {
            throw new \InvalidArgumentException('Expected 1 argument. ' . count($args) . ' passed.');
        }

        $this->{$attr} = reset($args);
        return $this;
    }
}
