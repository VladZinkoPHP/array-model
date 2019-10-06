<?php

namespace ZingleCom\ArrayModel\Test;

use PHPUnit\Framework\TestCase;
use ZingleCom\ArrayModel\ModelCollection;
use ZingleCom\ArrayModel\Test\Model\TestModel;

/**
 * Class ModelCollectionTest
 */
class ModelCollectionTest extends TestCase
{
    /**
     * Test array serialization
     */
    public function testToArray(): void
    {
        $collection = new ModelCollection([new TestModel(['key' => 'k123'])]);
        $array = $collection->toArray();

        $this->assertIsArray($array);
        $this->assertNotEmpty($array);
        $this->assertIsArray($array[0]);
    }
}
