<?php

namespace ZingleCom\ArrayModel;

use phootwork\collection\Map;
use ZingleCom\ArrayModel\Exception\MissingRequiredOptionException;
use ZingleCom\Enum\AbstractEnum;

/**
 * Class ValueNormalizer
 */
class ValueNormalizer
{
    const FALSE_STRING = 'false';


    /**
     * @param mixed $value
     * @param Cast  $cast
     *
     * @return bool|int|string
     */
    public function normalize($value, Cast $cast)
    {
        $options = $cast->getFieldOptions();

        $map = new Map([
            Cast::BOOL => 'castBool',
            Cast::INT => 'castInt',
            Cast::STRING => 'castString',
            Cast::FLOAT => 'castFloat',
            Cast::ENUM => 'castEnum',
            Cast::DATETIME => 'castDateTime',
            Cast::COLLECTION => 'castCollection',
            Cast::MODEL => 'castModel',
        ]);

        if (!$map->has($cast->getValue())) {
            return $value;
        }

        return call_user_func([$this, $map->get($cast->getValue())], $value, $options);
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    protected function castBool($value): bool
    {
        return self::FALSE_STRING === strtolower($value) ?
            false : boolval($value);
    }

    /**
     * @param mixed $value
     *
     * @return int
     */
    protected function castInt($value): int
    {
        return intval($value);
    }

    /**
     * @param mixed $value
     *
     * @return string
     */
    protected function castString($value): string
    {
        return strval($value);
    }

    /**
     * @param mixed $value
     *
     * @return float
     */
    protected function castFloat($value): float
    {
        return floatval($value);
    }

    /**
     * @param mixed $value
     * @param array $options
     *
     * @return AbstractEnum
     *
     * @throws MissingRequiredOptionException
     */
    protected function castEnum($value, array $options): AbstractEnum
    {
        if (empty($options)) {
            throw new MissingRequiredOptionException();
        }

        $class = reset($options);

        return new $class($value);
    }

    /**
     * @param string $value
     * @param array  $options
     *
     * @return \DateTime|null
     *
     * @throws \Exception
     */
    protected function castDateTime(?string $value, array $options): ?\DateTime
    {
        if (!$value) {
            return null;
        }

        $format = reset($options) ?: \DateTime::RFC3339_EXTENDED;

        $date = \DateTime::createFromFormat($format, $value);
        if (false === $date && $this->isRfc3339Extended($value)) {
            $date = new \DateTime($value);
        }

        if (is_bool($date)) {
            $date = null;
        }

        return $date;
    }

    /**
     * @param array $value
     * @param array $options
     *
     * @return ModelCollection
     *
     * @throws \ReflectionException
     * @throws \Exception
     */
    protected function castCollection(?array $value, array $options): ModelCollection
    {
        // If the value is an collection, we'll assume it's already casted.
        if ($value instanceof ModelCollection) {
            return $value;
        }

        if (empty($value)) {
            $value = [];
        }

        $class = array_shift($options);
        $reflClass = new \ReflectionClass($class);
        if (!$reflClass->isSubclassOf(AbstractModel::class)) {
            throw new \Exception(sprintf(
                'Collection casts only work with instances of %s',
                AbstractModel::class
            ));
        }

        return new ModelCollection(array_map(function ($item) use ($class) {
            return $this->castModel($item, [$class]);
        }, $value));
    }

    /**
     * @param mixed $value
     * @param array $options
     *
     * @return AbstractModel|null
     *
     * @throws \ReflectionException
     * @throws \Exception
     */
    protected function castModel($value, array $options): ?AbstractModel
    {
        if (empty($value)) {
            return null;
        }

        $class = array_shift($options);
        $reflClass = new \ReflectionClass($class);
        if (!$reflClass->isSubclassOf(AbstractModel::class)) {
            throw new \Exception(sprintf(
                'Model casts only work with instances of %s',
                AbstractModel::class
            ));
        }

        // If the value is already casted return that.
        if (is_object($value) && $reflClass->isInstance($value)) {
            return $value;
        }

        return new $class($value);
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    private function isRfc3339Extended(string $value): bool
    {
        return preg_match('/^(\d{4})-(\d{2})-(\d{2})T\d{2}:\d{2}(?::\d{2})?(?:\.\d+)?(?:Z|(?:(?:\+|-)\d{2}:\d{2}))$/', $value);
    }
}
