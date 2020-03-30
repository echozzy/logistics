<?php

declare(strict_types=1);

namespace Zzy\Logistics;

use Zzy\Logistics\Exceptions\InvalidArgumentException;
use Zzy\Logistics\Exceptions\NoQueryAvailableException;

/**
 * 抓取物流信息.
 */
class Logistics
{
    /**
     * 渠道接口总数.
     *
     * @var int
     */
    const CHANNEL_NUMBER = 7;

    /**
     * 成功
     *
     * @var string
     */
    const SUCCESS = 'success';

    /**
     * 失败.
     *
     * @string
     */
    const FAILURE = 'failure';

    /**
     * 快递渠道工厂
     *
     * @var \Zzy\Logistics\Factory
     */
    protected $factory;

    /**
     * 构造函数.
     */
    public function __construct()
    {
        $this->factory = new Factory();
    }

    /**
     * 通过接口获取物流信息.
     *
     * @param string $code
     * @param array  $channels
     * @param string $company
     *
     * @return array
     *
     * @throws \Zzy\Logistics\Exceptions\InvalidArgumentException
     * @throws \Zzy\Logistics\Exceptions\NoQueryAvailableException
     */
    public function query(string $code, $channels = ['kuaidiBird'],array $config = [], string $company = ''): array
    {
        $results = [];
        if (empty($code)) {
            throw new InvalidArgumentException('code arguments cannot empty.');
        }
        if (!empty($channels) && is_string($channels)) {
            $channels = explode(',', $channels);
        }
        foreach ($channels as $key=>$channel) {
            try {
                $request = $this->factory->createChannel($channel)->request($code,$config[$key], $company);
                if (1 === $request['status']) {
                    $results[$channel] = [
                        'channel' => $channel,
                        'status' => self::SUCCESS,
                        'result' => $request,
                    ];
                } else {
                    $results[$channel] = [
                        'channel' => $channel,
                        'status' => self::FAILURE,
                        'exception' => $request['message'],
                    ];
                }
            } catch (\Exception $exception) {
                $results[$channel] = [
                    'channel' => $channel,
                    'status' => self::FAILURE,
                    'exception' => $exception->getMessage(),
                ];
            }
        }
        $collectionOfException = array_column($results, 'exception');
        if (self::CHANNEL_NUMBER === count($collectionOfException)) {
            throw new NoQueryAvailableException('sorry! no channel class available');
        }

        return $results;
    }

    /**
     * 通过代理IP获取物流信息.
     *
     * @param array  $proxy
     * @param string $code
     * @param array  $channels
     * @param string $company
     *
     * @return array
     *
     * @throws \Zzy\Logistics\Exceptions\InvalidArgumentException
     * @throws \Zzy\Logistics\Exceptions\NoQueryAvailableException
     */
    public function queryByProxy(array $proxy, string $code, $channels = ['kuaidiBird'],array $config = [], string $company = ''): array
    {
        $results = [];
        if (empty($code)) {
            throw new InvalidArgumentException('code arguments cannot empty.');
        }
        if (!empty($channels) && is_string($channels)) {
            $channels = explode(',', $channels);
        }
        foreach ($channels as $key=>$channel) {
            try {
                $request = $this->factory->createChannel($channel)->request($code,$config[$key], $company);
                if (1 === $request['status']) {
                    $results[$channel] = [
                        'channel' => $channel,
                        'status' => self::SUCCESS,
                        'result' => $request,
                    ];
                } else {
                    $results[$channel] = [
                        'channel' => $channel,
                        'status' => self::FAILURE,
                        'exception' => $request['message'],
                    ];
                }
            } catch (\Exception $exception) {
                $results[$channel] = [
                    'channel' => $channel,
                    'status' => self::FAILURE,
                    'exception' => $exception->getMessage(),
                ];
            }
        }
        $collectionOfException = array_column($results, 'exception');
        if (self::CHANNEL_NUMBER === count($collectionOfException)) {
            throw new NoQueryAvailableException('sorry! no channel class available');
        }

        return $results;
    }
}
