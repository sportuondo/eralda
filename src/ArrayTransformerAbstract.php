<?php

namespace Sportuondo\Eralda;


abstract class ArrayTransformerAbstract extends TransformerAbstract
{
    protected $output = [];

    public function transformItem($item): array
    {
        if ($item === null) {
            throw new \InvalidArgumentException("The item to transform cannot be null");
        }

        // Process base mapping
        foreach ($this->keysMap as $key => $value) {
            $customPresenterMethodName = static::camel('present' . $key);
            if (method_exists($this, $customPresenterMethodName)) {
                $this->output[$value] = $this->{$customPresenterMethodName}($item);
            } else {
                $this->output[$value] = $item->{$key};
            }
        }

        // Process embeds
        foreach ($this->embeds as $key => $value) {
            $methodName = static::camel('embed' . $value);
            if (method_exists($this, $methodName)) {
                $this->output[$value] = $this->{$methodName}($item);
            } else {
                throw new \RuntimeException("No '{$methodName}()' method found on " . get_class($this) . " for the '{$value}' required embed");
            }
        }

        return $this->output;
    }

    /**
     * @param iterable $items
     * @return array
     */
    public function transformCollection(iterable $items): array
    {
        if (!is_iterable($items)) {
            throw new \InvalidArgumentException("The items parameter must contain an iterable value.");
        }

        $result = [];
        foreach ($items as $item) {
            $result[] = $this->transformItem($item);
        }

        return $result;
    }

    //region Helpers
    private static function camel($value): string
    {
        return lcfirst(static::studly($value));
    }

    private static function studly($value): string
    {
        $value = ucwords(str_replace(['-', '_'], ' ', $value));
        return str_replace(' ', '', $value);
    }
    //endregion

}