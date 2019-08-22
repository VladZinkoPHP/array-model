<?php

namespace ZingleCom\ArrayModel\Transformer;

use phootwork\collection\Map;
use ZingleCom\ArrayModel\Util\Arr;

/**
 * Class CamelizeTransformer
 */
class CamelizeTransformer implements PropTransformerInterface
{
    /**
     * @param array $attributes
     *
     * @return Map
     */
    public function transform(array $attributes): Map
    {
        return new Map(Arr::camelCaseKeys($attributes));
    }

    /**
     * @param string $getter
     *
     * @return string
     */
    public function getPropertyFromGetter(string $getter): string
    {
        return lcfirst(substr($getter, 3));
    }

    /**
     * @param string $isser
     *
     * @return string
     */
    public function getPropertyFromIsser(string $isser): string
    {
        return lcfirst(substr($isser, 2));
    }
}
