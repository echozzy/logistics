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
 * 极速数据物流查询.
 */
class JisuChannel extends Channel
{
    /**
     * JiSuChannel constructor.
     */
    public function __construct()
    {
        $this->url = 'https://api.jisuapi.com/express/query?appkey=';
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
            $params = ['type' => 'auto', 'number' => $code];
            if(!$config){
                $config = $this->getChannelConfig();
            }
            $this->url .= $config['app_key'];
            $response = $this->get($this->url, $params);
            $this->toArray($response);
            $this->format();

            return $this->response;
        } catch (HttpException $exception) {
            throw new HttpException($exception->getMessage());
        }
    }

    /**
     * 统一物流信息.
     */
    protected function format()
    {
        if (!empty($this->response['data'])) {
            $formatData = [];
            foreach ($this->response['data'] as $datum) {
                $formatData[] = ['time' => $datum['time'], 'description' => $datum['status']];
            }
            $this->response['data'] = $formatData;
        }
    }

    /**
     * 转为数组.
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
            if (0 === $jsonToArray['status']) {
                $this->response = [
                    'status' => 1,
                    'message' => 'ok',
                    'error_code' => 0,
                    'data' => $jsonToArray['result']['list'],
                    'logistics_company' => '',
                ];
            } else {
                $this->response = [
                    'status' => 0,
                    'message' => $jsonToArray['msg'],
                    'error_code' => $jsonToArray['status'],
                    'data' => [],
                    'logistics_company' => '',
                ];
            }
        }
    }
}
