<?php

namespace ZingleCom\ArrayModel\Transformer;

use phootwork\collection\Map;

/**
 * Class PassThruTransformer
 */
class PassThruTransformer implements PropTransformerInterface
{
    /**
     * @param array $attributes
     *
     * @return Map
     */
    public function transform(array $attributes): Map
    {
        return new Map($attributes);
    }

    /**
     * @param string $getter
     *
     * @return string
     */
    public function getPropertyFromGetter(string $getter): string
    {
        return substr($getter, 3);
    }

    /**
     * @param string $isser
     *
     * @return string
     */
    public function getPropertyFromIsser(string $isser): string
    {
        return substr($isser, 2);
    }
}
