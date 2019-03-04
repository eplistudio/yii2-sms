<?php
namespace eplistudio\sms\alidayu;


use eplistudio\sms\SmsRequest;
use yii\base\InvalidArgumentException;
use yii\base\InvalidConfigException;
use yii\base\Model;


abstract class BaseRequest extends Model implements SmsRequest
{
    const BASE_URL = 'https://dysmsapi.aliyuncs.com';

    const FORMAT_JSON = 'JSON';
    const FORMAT_XML = 'XML';

    protected $accessKeyId;
    protected $accessKeySecret;

    public $format = self::FORMAT_JSON;
    public $regionId;

    private $_signatureNonce;

    public function __construct($accessKeyId, $accessKeySecret, $config = [])
    {
        parent::__construct($config);
        if (empty($accessKeyId)) {
            throw new InvalidConfigException("Property 'accessKeyId' must be set.");
        }
        $this->accessKeyId = $accessKeyId;

        if (empty($accessKeySecret)) {
            throw new InvalidConfigException("Property 'accessKeySecret' must be set.");
        }
        $this->accessKeySecret = $accessKeySecret;
    }

    public static function httpMethod()
    {
        return 'GET';
    }

    public function rules()
    {
        return [
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

    abstract public function getAction();

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
        if ($this->_signatureNonce) {
            return $this->_signatureNonce;
        }
        $this->_signatureNonce = \Yii::$app->security->generateRandomString();
        return $this->_signatureNonce;
    }

    public function getSignatureVersion()
    {
        return '1.0';
    }

    public function getTimestamp()
    {
        return gmdate("Y-m-d\TH:i:s\Z");
    }

    public final function getVersion()
    {
        return '2017-05-25';
    }

    public function generateSignature($key) {
        $signatureBody = self::httpMethod() . '&' . $this->encode('/') . '&' . urlencode(substr($this->queryString(), 0, -1));
        $signature = base64_encode(hash_hmac("sha1", $signatureBody, $key . "&", true));
        return $this->encode($signature);
    }

    protected function queryString()
    {
        if (!$this->validate()) {
            throw new InvalidArgumentException();
        }

        $queryParameters = $this->toArray();
        $queryParameters = array_filter($queryParameters, 'strlen');
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
        if (function_exists("curl_init")) {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $requestUrl);
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
                "method" => self::httpMethod(),
                "header" => ["x-sdk-client: php/2.0.0"],
            ]
        ]);

        return file_get_contents($requestUrl, false, $context);
    }

}