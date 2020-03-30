<?php
/** .-------------------------------------------------------------------
 * |      Site: www.zhouzy365.com
 * |      Date: 2019/9/10 下午3:13
 * |    Author: zzy <348858954@qq.com>
 * '-------------------------------------------------------------------*/

declare(strict_types=1);

namespace Zzy\Logistics\Channel;

class KuaiDi100Channel extends Channel
{
    /**
     * 构造函数.
     *
     * Kuaidi100Channel constructor.
     */
    public function __construct()
    {
        $this->url = 'http://poll.kuaidi100.com/poll/query.do';
    }

    /**
     * 调用快递100接口.
     *
     * @param string $code
     * @param string $company
     *
     * @return array
     *
     * @throws \Exception
     */
    public function request(string $code, array $config = [],string $company = ''): array
    {
        try {
            $companyCode = (new \Zzy\Logistics\SupportLogistics())->getCode($this->getClassName(), $code, $company);
            $postJson = \json_encode([
                'num' => $code,
                'com' => $companyCode,
            ]);
            if(!$config){
                $config = $this->getChannelConfig();
            }
            $params = [
                'customer' => $config['app_secret'],
                'sign' => \strtoupper(\md5($postJson.$config['app_key'].$config['app_secret'])),
                'param' => $postJson,
            ];
            $response = $this->post($this->url, $params);
            $this->toArray($response);
            $this->format();

            return $this->response;
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
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
            if (0 === $jsonToArray['state']) {
                $this->response = [
                    'status' => 1,
                    'message' => 'ok',
                    'error_code' => 0,
                    'data' => $jsonToArray['data'],
                    'logistics_company' => $jsonToArray['com'],
                ];
            } else {
                $this->response = [
                    'status' => 0,
                    'message' => $jsonToArray['message'],
                    'error_code' => $jsonToArray['state'],
                    'data' => [],
                    'logistics_company' => '',
                ];
            }
        }
    }

    /**
     * 统一物流信息.
     *
     * @return mixed|void
     */
    protected function format()
    {
        if (!empty($this->response['data'])) {
            $formatData = [];
            foreach ($this->response['data'] as $datum) {
                $formatData[] = ['time' => $datum['ftime'], 'description' => $datum['context']];
            }
            $this->response['data'] = $formatData;
        }
    }
}
