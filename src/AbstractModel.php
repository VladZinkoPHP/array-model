<?php

namespace ZingleCom\ArrayModel;

use phootwork\collection\Map;
use ZingleCom\ArrayModel\Exception\MissingRequiredOptionException;
use ZingleCom\ArrayModel\Transformer\CamelizeTransformer;
use ZingleCom\ArrayModel\Transformer\PropTransformerInterface;
use ZingleCom\ArrayModel\Util\Arr;
use ZingleCom\Enum\AbstractEnum;

/**
 * Class AbstractModel
 */
abstract class AbstractModel implements \JsonSerializable
{
    /**
     * @var ValueNormalizer
     */
    protected $normalizer;

    /**
     * @var Map
     */
    protected $attributes;

    /**
     * @var array
     */
    protected $casts = [];

    /**
     * @var string
     */
    protected $transformerClass = CamelizeTransformer::class;

    /**
     * @var Map
     */
    private $castedValues;

    /**
     * @var PropTransformerInterface
     */
    private $transformer;


    /**
     * ArrayModel constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->normalizer   = new ValueNormalizer();
        $this->castedValues = new Map();

        $transformerClass  = $this->transformerClass;
        $this->transformer = new $transformerClass();

        $this->setAttributes($attributes);
    }

    /**
     * @param string $json
     *
     * @return AbstractModel
     */
    public static function fromJson(string $json): self
    {
        return new static(json_decode($json, $assoc = true));
    }

    /**
     * @param PropTransformerInterface $transformer
     *
     * @return AbstractModel
     */
    public function setTransformer(PropTransformerInterface $transformer): self
    {
        $this->transformer = $transformer;

        return $this;
    }

    /**
     * @return Map
     */
    public function getAttributes(): Map
    {
        return $this->attributes;
    }

    /**
     * @param array $attributes
     *
     * @return AbstractModel
     */
    public function setAttributes(array $attributes): self
    {
        $this->attributes = $this->transformer->transform($attributes);

        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $array = [];
        $this->attributes->keys()->each(function ($attribute) use (&$array) {
            $value = $this->getAttributeValue($attribute);

            if ($value instanceof AbstractModel && $this !== $value) {
                $array[$attribute] = $value->toArray();
            } elseif ($value instanceof ModelCollection) {
                $array[$attribute] = $value->toArray();
            } elseif ($value instanceof AbstractEnum) {
                $array[$attribute] = sprintf('%s (%s)', get_class($value), $value->getValue());
            } else {
                $array[$attribute] = $value;
            }
        });

        return $array;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function __isset($name)
    {
        return $this->attributes->has($name);
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        return $this->getAttributeValue($name);
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return AbstractModel
     */
    public function __set($name, $value): self
    {
        $this->setAttribute($name, $value);

        return $this;
    }

    /**
     * @param string $name
     * @param mixed  $value
     */
    public function setAttribute(string $name, $value): void
    {
        $this->attributes->set($name, $value);
        $this->castedValues->remove($name);
    }

    /**
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (0 === strpos($name, 'get')) {
            return $this->getAttributeValue($this->getPropertyFromGetter($name));
        }

        if (0 === strpos($name, 'is')) {
            return $this->isPropertyTrue($this->getPropertyFromIsser($name));
        }

        if (0 === strpos($name, 'set')) {
            array_unshift($arguments, $this->getPropertyFromGetter($name));

            return call_user_func_array([$this, '__set'], $arguments);
        }

        return null;
    }

    /**
     * @param string $property
     *
     * @return bool
     */
    public function isPropertyTrue(string $property): bool
    {
        $value = (bool) $this->getAttributeValue($property);

        return true === $value;
    }

    /**
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getAttributeValue(string $name, $default = null)
    {
        $getter = sprintf('get%sValue', ucfirst($name));
        if (method_exists($this, $getter)) {
            return call_user_func([$this, $getter]);
        }

        try {
            if (!$this->castedValues->has($name)) {
                $this->castedValues->set($name, $this->getCastedValue($name, $this->attributes->get($name, $default)));
            }

            return $this->castedValues->get($name);
        } catch (MissingRequiredOptionException $e) {
            return $this->attributes->get($name);
        }
    }

    /**
     * @return array|mixed
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @param string $getter
     *
     * @return string
     */
    protected function getPropertyFromGetter(string $getter): string
    {
        return $this->transformer->getPropertyFromGetter($getter);
    }

    /**
     * @param string $isser
     *
     * @return string
     */
    protected function getPropertyFromIsser(string $isser): string
    {
        return $this->transformer->getPropertyFromIsser($isser);
    }

    /**
     * @param string $attribute
     * @param mixed  $value
     *
     * @return mixed
     */
    private function getCastedValue(string $attribute, $value)
    {
        return $this->getNormalizer()->normalize(
            $value,
            $this->getCast($attribute)
        );
    }

    /**
     * @param string $attribute
     *
     * @return Cast
     */
    private function getCast(string $attribute): Cast
    {
        $castDefinition = isset($this->casts[$attribute]) ?
            $this->casts[$attribute] : [Cast::NONE];

        if (is_string($castDefinition)) {
            list($castType, $optionsString) = array_pad(explode('|', $castDefinition), 2, null);
            $options = explode(',', $optionsString ?? '');
        } else {
            $castType = array_shift($castDefinition);
            $options  = $castDefinition;
        }

        return new Cast($castType, $options);
    }

    /**
     * @return ValueNormalizer
     */
    private function getNormalizer(): ValueNormalizer
    {
        return $this->normalizer;
    }
}
