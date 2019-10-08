<?php

namespace ZingleCom\ArrayModel\Test;

use PHPUnit\Framework\TestCase;
use ZingleCom\ArrayModel\AbstractModel;
use ZingleCom\ArrayModel\Transformer\PropTransformerInterface;

/**
 * Class AbstractModelTest
 */
class AbstractModelTest extends TestCase
{
    /**
     * Test set attributes calls transformer
     */
    public function testSetAttributes()
    {
        $model = new class extends AbstractModel {};
        $transformer = $this->createMock(PropTransformerInterface::class);
        $transformer
            ->expects($this->once())
            ->method('transform')
        ;

        $model->setAttributes([]);
    }
}
