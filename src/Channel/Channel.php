<?php
/** .-------------------------------------------------------------------
 * |      Site: www.zhouzy365.com
 * |      Date: 2019/9/10 下午3:13
 * |    Author: zzy <348858954@qq.com>
 * '-------------------------------------------------------------------*/

declare(strict_types=1);

namespace Zzy\Logistics\Channel;

use Zzy\Logistics\Config;
use Zzy\Logistics\Traits\HttpRequest;

abstract class Channel
{
    /*
     * HTTP 请求
     */
    use HttpRequest;

    /**
     * 渠道URL.
     *
     * @var string
     */
    protected $url;

    /**
     * 请求资源.
     *
     * @var array
     */
    protected $response;

    /**
     * 请求选项.
     *
     * @var array
     */
    protected $option = [];

    /**
     * 设置请求选项.
     *
     * @param array $option
     *
     * @return \Zzy\Logistics\Channel\Channel
     */
    public function setRequestOption(array $option): self
    {
        if (!empty($this->option)) {
            if (isset($option['header']) && isset($this->option['header'])) {
                $this->option['header'] = array_merge($this->option['header'], $option['header']);
            }
            if (isset($option['proxy'])) {
                $this->option['proxy'] = $option['proxy'];
            }
        } else {
            $this->option = $option;
        }

        return $this;
    }

    /**
     * 获取实例化的类名称.
     *
     * @return string
     */
    protected function getClassName(): string
    {
        $className = basename(str_replace('\\', '/', (get_class($this))));

        return preg_replace('/Channel/', '', $className);
    }

    /**
     * 获取配置.
     *
     * @return array
     */
    protected function getChannelConfig(): array
    {
        $key = $this->getClassName();
        $config = (new Config())->getConfig(strtolower($key));

        return $config;
    }

    /**
     * 调用查询接口.
     *
     * @param string $code
     * @param string $company
     *
     * @return array
     */
    abstract public function request(string $code, array $config = [], string $company = ''): array;

    /**
     * 转换为数组.
     *
     * @param string|array $response
     */
    abstract protected function toArray($response);

    /**
     * 格式物流信息.
     *
     * @return mixed
     */
    abstract protected function format();
}
