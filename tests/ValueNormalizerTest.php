<?php

namespace ZingleCom\ArrayModel\Tests;

use PHPUnit\Framework\TestCase;
use ZingleCom\ArrayModel\Cast;
use ZingleCom\ArrayModel\ModelCollection;
use ZingleCom\ArrayModel\Tests\Model\TestEnum;
use ZingleCom\ArrayModel\Tests\Model\TestModel;
use ZingleCom\ArrayModel\ValueNormalizer;

/**
 * Class ValueNormalizerTest
 */
class ValueNormalizerTest extends TestCase
{
    /**
     * Test bool normalization
     */
    public function testNormalizeBool(): void
    {
        $normalizer = new ValueNormalizer();

        $cast = new Cast(Cast::BOOL);
        $this->assertFalse($normalizer->normalize('false', $cast));
        $this->assertFalse($normalizer->normalize(0, $cast));
        $this->assertFalse($normalizer->normalize('0', $cast));
        $this->assertTrue($normalizer->normalize('true', $cast));
        $this->assertTrue($normalizer->normalize(true, $cast));
        $this->assertTrue($normalizer->normalize(1, $cast));
    }

    /**
     * Normalize int cast
     */
    public function testNormalizeInt(): void
    {
        $normalizer = new ValueNormalizer();

        $cast = new Cast(Cast::INT);
        $this->assertTrue(1 === $normalizer->normalize('1', $cast));
    }

    /**
     * Normalize string
     */
    public function testNormalizeString(): void
    {
        $normalizer = new ValueNormalizer();

        $cast = new Cast(Cast::STRING);
        $this->assertTrue('1' === $normalizer->normalize(1, $cast));
    }

    /**
     * Test float normalization
     */
    public function testNormalizeFloat(): void
    {
        $normalizer = new ValueNormalizer();

        $cast = new Cast(Cast::FLOAT);
        $this->assertTrue(0.1 === $normalizer->normalize('0.1', $cast));
    }

    /**
     * Test enum normalization
     */
    public function testNormalizeEnum(): void
    {
        $normalizer = new ValueNormalizer();

        $cast = new Cast(Cast::ENUM, [TestEnum::class]);
        /** @var TestEnum $value */
        $value = $normalizer->normalize(TestEnum::TEST_1, $cast);
        $this->assertInstanceOf(TestEnum::class, $value);
        $this->assertTrue($value->is(TestEnum::TEST_1));
    }

    /**
     * Test normalize datetime
     */
    public function testNormalizationDateTime(): void
    {
        $normalizer = new ValueNormalizer();

        $cast = new Cast(Cast::DATETIME, ['Y-m-d']);
        /** @var \DateTime $value */
        $value = $normalizer->normalize('2011-01-01', $cast);
        $this->assertInstanceOf(\DateTime::class, $value);
    }

    /**
     * Test normalize model
     */
    public function testNormalizeModel(): void
    {
        $normalizer = new ValueNormalizer();

        $cast = new Cast(Cast::MODEL, [TestModel::class]);
        /** @var TestModel $value */
        $value = $normalizer->normalize(['key' => $key = '123'], $cast);
        $this->assertInstanceOf(TestModel::class, $value);
        $this->assertEquals($key, $value->getKey());
    }

    /**
     * Test normalize collection
     */
    public function testNormalizeCollection(): void
    {
        $normalizer = new ValueNormalizer();

        $cast = new Cast(Cast::COLLECTION, [TestModel::class]);
        /** @var ModelCollection $value */
        $value = $normalizer->normalize([['key' => $key = '123']], $cast);
        $this->assertInstanceOf(ModelCollection::class, $value);
        $this->assertEquals(1, $value->size());
        $this->assertInstanceOf(TestModel::class, $value->get(0));
    }
}
