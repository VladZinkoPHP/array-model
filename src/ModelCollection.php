<?php

namespace ZingleCom\ArrayModel;

use phootwork\collection\ArrayList;

/**
 * Class ModelCollection
 */
class ModelCollection extends ArrayList
{
    /**
     * @param mixed $element
     * @param null  $index
     *
     * @return ArrayList
     *
     * @throws \Exception
     */
    public function add($element, $index = null)
    {
        if (!$element instanceof AbstractModel) {
            throw new \Exception(sprintf('%s expects %s instances', static::class, AbstractArrayModel::class));
        }

        return parent::add($element, $index);
    }

    /**
     * @return array|ModelCollection
     */
    public function toArray()
    {
        return array_map(function (AbstractModel $model) {
            return $model->toArray();
        }, parent::toArray());
    }
}
