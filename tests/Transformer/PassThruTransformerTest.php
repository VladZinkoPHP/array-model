<?php

namespace ZingleCom\ArrayModel\Test\Transformer;

use PHPUnit\Framework\TestCase;
use ZingleCom\ArrayModel\Transformer\PassThruTransformer;

/**
 * Class PassThruTransformerTest
 */
class PassThruTransformerTest extends TestCase
{
    /**
     * Test transformer
     */
    public function testTransformer(): void
    {
        $transformer = new PassThruTransformer();
        $attributes = [
            'key1' => rand(),
            'key2' => rand(),
            'key3' => rand(),
        ];

        $transformed = $transformer->transform($attributes);
        foreach ($attributes as $k => $v) {
            $this->assertTrue($transformed->has($k));
            $this->assertEquals($v, $transformed->get($k));

            $getter = sprintf('get%s', $k);
            $this->assertEquals($k, $transformer->getPropertyFromGetter($getter));
            $isser = sprintf('is%s', $k);
            $this->assertEquals($k, $transformer->getPropertyFromIsser($isser));
        }
    }
}
