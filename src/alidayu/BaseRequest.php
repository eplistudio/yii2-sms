<?php
namespace eplistudio\sms\alidayu;


use yii\base\InvalidConfigException;
use yii\base\Model;


abstract class BaseRequest extends Model implements SmsRequest
{
    const BASE_URL = 'https://dysmsapi.aliyuncs.com';

    const FORMAT_JSON = 'json';
    const FORMAT_XML = 'xml';

    protected $accessKeyId;
    protected $accessKeySecret;

    public $action;
    public $format;
    public $regionId;

    public function __construct($accessKeyId, $accessKeySecret, $config = [])
    {
        parent::__construct($config);
        if (empty($accessKeyId)) {
            throw new InvalidConfigException("Property 'accessKeyId' must be set.");
        }

        if (empty($accessKeySecret)) {
            throw new InvalidConfigException("Property 'accessKeySecret' must be set.");
        }
    }

    public function rules()
    {
        return [
            [['action'], 'required'],
            [['format', 'regionId'], 'string'],
            [['format'], 'in', 'range' => [self::FORMAT_JSON, self::FORMAT_XML]],
        ];
    }

    public function fields()
    {
        return [
            'AccessKeyId' => 'accessKeyId',
            'Action' => 'action',
            'Format' => 'format',
            'RegionId' => 'regionId',
            'SignatureMethod' => 'signatureMethod',
            'SignatureNonce' => 'signatureNonce',
            'SignatureVersion' => 'signatureVersion',
            'Timestamp' => 'timestamp',
            'Version' => 'version',
        ];
    }

    public function getAccessKeyId()
    {
        return $this->accessKeyId;
    }

    public function getSignatureMethod()
    {
        return 'HMAC-SHA1';
    }

    public function getSignatureNonce()
    {
        return \Yii::$app->security->generateRandomString();
    }

    public function getSignatureVersion()
    {
        return '1.0';
    }

    public function getTimestamp()
    {
        return date('c');
    }

    public final function getVersion()
    {
        return '2017-05-25';
    }

    public function generateSignature($key) {

        $signatureBody = self::httpMethod() . '&' . $this->encode('/') . '&' . urlencode($this->queryString());
        $signature = base64_encode(hash_hmac("sha1", $signatureBody, $key . "&", true));
        return $this->encode($signature);
    }

    protected function queryString()
    {
        $queryParameters = $this->toArray();
        ksort($queryParameters);
        $queryString = '';
        foreach ($queryParameters as $key => $value) {
            $queryString .= $this->encode($key) . '=' . $this->encode($value) . '&' ;
        }
        return $queryString;
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
        return preg_replace("/%7E/", "~", $res);
    }

    public function exec()
    {
        $queryString = $this->queryString();
        $signature = $this->generateSignature($this->accessKeySecret);
        $requestUrl = self::BASE_URL . '/?' . $queryString . 'Signature=' . $signature;
        $context = stream_context_create([
            "http" => [
                "method" => self::httpMethod(),
                "header" => ["x-sdk-client: php/2.0.0"],
            ]
        ]);

        return file_get_contents($requestUrl, false, $context);
    }

}