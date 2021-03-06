<?php
/** .-------------------------------------------------------------------
 * |      Site: www.zhouzy365.com
 * |      Date: 2019/9/10 下午3:13
 * |    Author: zzy <348858954@qq.com>
 * '-------------------------------------------------------------------*/

declare(strict_types=1);

namespace Zzy\Logistics\Channel;

use Zzy\Logistics\Exceptions\HttpException;

/**
 * 数据智汇 查询物流接口.
 */
class ShujuzhihuiChannel extends Channel
{
    /**
     * 错误信息.
     *
     * @var array
     */
    private $errorMessage = [
        2000 => '系统繁忙',
        1100 => '暂无相关数据',
        1001 => '缺失必要参数expressNo',
        10012 => '请输入appKey!',
        50010 => 'appKey输入错误!',
        50013 => 'API次数已用完,请续费后再调用',
        50110 => '接口维护',
        50111 => '接口停用',
    ];

    /**
     * ShuJuZhiHuiChannel constructor.
     */
    public function __construct()
    {
        $this->url = 'http://api.shujuzhihui.cn/api/sjzhApi/searchExpress';
    }

    /**
     * 请求
     *
     * @param string $code
     * @param string $company
     *
     * @return array
     *
     * @throws \Zzy\Logistics\Exceptions\HttpException
     */
    public function request(string $code, array $config = [], string $company = ''): array
    {
        try {
            if(!$config){
                $config = $this->getChannelConfig();
            }
            $params = ['appKey' => $config['app_key'], 'expressNo' => $code];
            $response = $this->post($this->url, $params);
            $this->toArray($response);
            $this->format();

            return $this->response;
        } catch (HttpException $exception) {
            throw new HttpException($exception->getMessage());
        }
    }

    /**
     * 转换为数组.
     *
     * @param array|string $response
     */
    protected function toArray($response)
    {
        $jsonToArray = \json_decode($response, true);
        if (empty($jsonToArray)) {
            $this->response = [
                'status' => 0,
                'message' => '请求发生不知名错误, 查询不到物流信息',
                'error_code' => 0,
                'data' => [],
                'logistics_company' => '',
            ];
        } else {
            if (0 === $jsonToArray['ERRORCODE']) {
                $this->response = [
                    'status' => 1,
                    'message' => 'ok',
                    'error_code' => 0,
                    'data' => $jsonToArray['RESULT']['context'],
                    'logistics_company' => $jsonToArray['RESULT']['com'],
                ];
            } else {
                $this->response = [
                    'status' => 0,
                    'message' => $this->errorMessage[$jsonToArray['ERRORCODE']],
                    'error_code' => $jsonToArray['ERRORCODE'],
                    'data' => [],
                    'logistics_company' => '',
                ];
            }
        }
    }

    /**
     * 格式化数组.
     */
    protected function format()
    {
        if (!empty($this->response['data'])) {
            $formatData = [];
            foreach ($this->response['data'] as $datum) {
                $formatData[] = ['time' => $datum['time'], 'description' => $datum['desc']];
            }
            $this->response['data'] = $formatData;
        }
    }
}
