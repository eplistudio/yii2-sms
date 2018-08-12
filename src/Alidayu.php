<?php
namespace eplistudio\sms;

use yii\base\Component;

class Alidayu extends Component
{
    const DOMAIN = 'dysmsapi.aliyuncs.com';

    const VERSION = '2017-05-25';

    const BATCH_LIMITED = 1000;

    const ACTION_SEND_SMS = 'SendSms';

    const SIGNATURE_TYPE_HMAC_SHA1 = 'HMAC-SHA1';

    /**
     * 短信签名
     * @var string
     */
    public $signName;

    /**
     * 阿里大于访问授权ID
     * @var string
     */
    public $accessKeyId;

    /**
     * 阿里大于访问授权密钥
     * @var string
     */
    public $accessKeySecret;

    /**
     * 阿里大于服务结点
     * @var string
     */
    public $regionId = "cn-shenzhen";

    /**
     * 生成签名并发起请求
     *
     * @param $domain string API接口所在域名
     * @param $params array API具体参数
     * @return bool|array 返回API接口调用结果，当发生错误时返回false
     */
    public function request($domain, $params)
    {
        $signature = $this->_generateSignature($params, self::SIGNATURE_TYPE_HMAC_SHA1);
        $sortedQueryString = $this->_sortedQueryString($params);
        $url = "http://{$domain}/?Signature={$signature}{$sortedQueryString}";

        try {
            $content = $this->fetchContent($url);
            return json_decode($content, true);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 替换URL字符
     * 
     * @param $str
     * @return null|string|string[]
     */
    protected function encode($str)
    {
        $res = urlencode($str);
        $res = preg_replace("/\+/", "%20", $res);
        $res = preg_replace("/\*/", "%2A", $res);
        $res = preg_replace("/%7E/", "~", $res);
        return $res;
    }

    /**
     * 获取请求返回信息
     * 
     * @param $url
     * @return bool|mixed|string
     */
    protected function fetchContent($url)
    {
        if (function_exists("curl_init")) {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_TIMEOUT, 5);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_HTTPHEADER, ["x-sdk-client" => "php/2.0.0"]);
            $return = curl_exec($curl);

            if ($return === false) {
                trigger_error("[CURL_" . curl_errno($curl) . "]: " . curl_error($curl), E_USER_ERROR);
            }
            curl_close($curl);

            return $return;
        }

        $context = stream_context_create([
            "http" => [
                "method" => "GET",
                "header" => ["x-sdk-client: php/2.0.0"],
            ]
        ]);

        return file_get_contents($url, false, $context);
    }

    /**
     * 发送短信
     * @param $phoneNumbers
     * @param $templateCode
     * @param null $templateParam
     * @param null $outId
     * @return bool|\stdClass
     */
    public function send($phoneNumbers, $templateCode, $templateParam = null, $outId = null)
    {
        $phoneNumbers = $this->_combinedPhoneNumbers($phoneNumbers);
        $dataPacket = [
            'PhoneNumbers' => $phoneNumbers,
            'SignName' => $this->signName,
            'TemplateCode' => $templateCode,
            'TemplateParam' => json_encode($templateParam),
            'OutId' => $outId,
        ];

        return $this->request(
            self::DOMAIN,
            array_merge($dataPacket, [
                "RegionId" => $this->regionId,
                "Action" => self::ACTION_SEND_SMS,
                "Version" => self::VERSION,
            ])
        );
    }

    /**
     * 生成签名
     * @param $params
     * @param $method
     * @param string $format
     * @param string $dateFormat
     * @return null|string|string[]
     */
    private function _generateSignature(&$params, $method, $format = "JSON", $dateFormat = "Y-m-d\TH:i:s\Z")
    {

        $params = array_merge([
            "SignatureMethod" => $method,
            "SignatureNonce" => uniqid(mt_rand(0, 0xffff), true),
            "SignatureVersion" => "1.0",
            "AccessKeyId" => $this->accessKeyId,
            "Timestamp" => gmdate($dateFormat),
            "Format" => $format,
        ], $params);

        $sortedQueryString = $this->_sortedQueryString($params);

        $stringToSign = "GET&%2F&" . $this->encode(substr($sortedQueryString, 1));

        $sign = base64_encode(hash_hmac("sha1", $stringToSign, $this->accessKeySecret . "&", true));

        return $this->encode($sign);
    }

    /**
     * 根据字典序排序参数并生成字符串
     * @param $params
     * @return string
     */
    private function _sortedQueryString($params)
    {
        ksort($params);
        $sortedQueryString = "";
        foreach ($params as $key => $value) {
            $sortedQueryString .= "&" . $this->encode($key) . "=" . $this->encode($value);
        }
        return $sortedQueryString;
    }

    /**
     * 处理手机号码
     * @param $phoneNumbers
     * @return array|bool|string
     */
    private function _combinedPhoneNumbers($phoneNumbers)
    {
        $count = 1;
        if (is_array($phoneNumbers)) {
            if (($count = count($phoneNumbers)) > self::BATCH_LIMITED) {
                return false;
            }

            foreach ($phoneNumbers as $index => $phoneNumber) {
                $phoneNumbers[$index] = intval($phoneNumber);
            }

            $phoneNumbers = implode(',', $phoneNumbers);
        }

        if (preg_match_all('/(13|14|15|17|18|19)[0-9]{9}(,)?/', $phoneNumbers) === $count) {
            return $phoneNumbers;
        }

        return false;
    }
}