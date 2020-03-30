<?php
/** .-------------------------------------------------------------------
 * |      Site: www.zhouzy365.com
 * |      Date: 2019/9/10 下午3:13
 * |    Author: zzy <348858954@qq.com>
 * '-------------------------------------------------------------------*/
declare(strict_types=1);

namespace Zzy\Logistics;

use Zzy\Logistics\Exceptions\InvalidArgumentException;
use Zzy\Logistics\Exceptions\Exception;
use Zzy\Logistics\Channel\Channel;

class Factory
{
    private $defaultChannel = 'kuaiDiBird';

    protected $channels = [];

    /**
     * 获取默认查询类名称.
     *
     * @return string
     *
     * @throws \Zzy\Logistics\Exceptions\Exception
     */
    public function getDefault(): string
    {
        if (empty($this->defaultChannel)) {
            throw new Exception('No default query class name configured.');
        }

        return $this->defaultChannel;
    }

    /**
     * 设置默认查询类名称.
     *
     * @param $name
     */
    public function setDefault($name)
    {
        $this->defaultChannel = $name;
    }

    /**
     * 数组元素存储查询对象
     *
     * @param string $name
     *
     * @return mixed
     *
     * @throws \Zzy\Logistics\Exceptions\InvalidArgumentException
     */
    public function createChannel(string $name = '')
    {
        $name = $name ?: $this->defaultChannel;
        if (!isset($this->channels[$name])) {
            $className = $this->formatClassName($name);
            if (!class_exists($className)) {
                throw new InvalidArgumentException(sprintf('Class "%s" not exists.', $className));
            }
            $instance = new $className();
            if (!($instance instanceof Channel)) {
                throw new InvalidArgumentException(sprintf('Class "%s" not inherited from %s.', $name, Channel::class));
            }
            $this->channels[$name] = $instance;
        }

        return $this->channels[$name];
    }

    /**
     * 格式化类的名称.
     *
     * @param string $name
     *
     * @return string
     */
    protected function formatClassName(string $name): string
    {
        if (class_exists($name)) {
            return $name;
        }
        $name = ucfirst(str_replace(['-', '_', ' '], '', $name));

        return __NAMESPACE__."\\Channel\\{$name}Channel";
    }
}
