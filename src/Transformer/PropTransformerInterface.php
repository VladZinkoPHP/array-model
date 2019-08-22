<?php


namespace ZingleCom\ArrayModel\Transformer;

use phootwork\collection\Map;

/**
 * Interface PropTransformerInterface
 */
interface PropTransformerInterface
{
    /**
     * Transforms attributes for normalizing the data on the model
     *
     * @param array $attributes
     *
     * @return Map
     */
    public function transform(array $attributes): Map;

    /**
     * Get property from getter name
     *
     * @param string $getter
     *
     * @return string
     */
    public function getPropertyFromGetter(string $getter): string;

    /**
     * Get property from isser name
     *
     * @param string $isser
     *
     * @return string
     */
    public function getPropertyFromIsser(string $isser): string;
}
