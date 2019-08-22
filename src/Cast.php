<?php

namespace ZingleCom\ArrayModel;

use ZingleCom\Enum\AbstractEnum;

/**
 * Class Cast
 */
final class Cast extends AbstractEnum
{
    const INT        = 'int';
    const BOOL       = 'bool';
    const STRING     = 'string';
    const FLOAT      = 'float';
    const ENUM       = 'enum';
    const DATETIME   = 'datetime';
    const COLLECTION = 'collection';
    const MODEL      = 'model';
    const NONE       = '__none__';

    /**
     * @var array
     */
    private $fieldOptions;


    /**
     * Cast constructor.
     * @param mixed $value
     * @param array $fieldOptions
     */
    public function __construct($value, array $fieldOptions = [])
    {
        $this->fieldOptions = $fieldOptions;

        parent::__construct($value);
    }

    /**
     * @return array
     */
    public function getFieldOptions(): array
    {
        return $this->fieldOptions;
    }
}
