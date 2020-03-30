<?php

/** .-------------------------------------------------------------------
 * |      Site: www.zhouzy365.com
 * |      Date: 2019/9/10 下午3:13
 * |    Author: zzy <348858954@qq.com>
 * '-------------------------------------------------------------------*/

namespace Wythe\Logistics\Tests;

use PHPUnit\Framework\TestCase;
use Wythe\Logistics\Channel\JiSuChannel;
use Wythe\Logistics\Factory;
use Wythe\Logistics\SupportLogistics;

class CommonMethodTest extends TestCase
{
    /**
     * 测试设置默认渠道接口.
     *
     * @throws \Wythe\Logistics\Exceptions\Exception
     */
    public function testSetFactoryDefault()
    {
        $factory = new Factory();
        $factory->setDefault('kuaiDi100');
        $this->assertSame('kuaiDi100', $factory->getDefault());
    }

    /**
     * 测试获取类名称.
     */
    public function testClassName()
    {
        $jiSu = new JiSuChannel();
        $closure = function () {
            return $this->getClassName();
        };
        $bindTo = $closure->bindTo($jiSu, $jiSu);
        $this->assertSame('JiSu', $bindTo());
    }

    /**
     * 测试获取快递公司编码
     *
     * @throws \Wythe\Logistics\Exceptions\HttpException
     */
    public function testSupportLogistics()
    {
        $supportLogistics = \Mockery::mock(SupportLogistics::class);
        $supportLogistics->shouldReceive('getCode')->andReturn('shunfeng');
        $this->assertSame('shunfeng', $supportLogistics->getCode('kuaiDi100', '12331231', '顺丰'));
    }
}
