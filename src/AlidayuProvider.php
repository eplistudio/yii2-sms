<?php
namespace eplistudio\sms;


use eplistudio\sms\alidayu\SendBatchSmsRequest;
use yii\base\InvalidArgumentException;

class AlidayuProvider extends Provider
{
    const DOMAIN = 'dysmsapi.aliyuncs.com';

    const VERSION = '2017-05-25';

    const BATCH_LIMITED = 1000;

    const ACTION_SEND_SMS = 'SendSms';

    const SIGNATURE_TYPE_HMAC_SHA1 = 'HMAC-SHA1';

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
    public $regionId = "cn-hangzhou";


    public function collect($messages)
    {
        foreach ($messages as $message) {
            foreach ($message[0] as $templateCode => $requests) {
                try {
                    $sendBatchSmsRequest = new SendBatchSmsRequest($this->accessKeyId, $this->accessKeySecret);
                    $sendBatchSmsRequest->regionId = $this->regionId;
                    $sendBatchSmsRequest->templateCode = $templateCode;
                    foreach ($requests as $request) {
                        if (!is_array($request['content'])) {
                            throw new InvalidArgumentException();
                        }

                        array_push($sendBatchSmsRequest->phoneNumbers, $request['to']);
                        array_push($sendBatchSmsRequest->templateParams, $request['content']);
                        array_push($sendBatchSmsRequest->signNames, $request['sign']);
                    }

                    \Yii::info($sendBatchSmsRequest->exec(), __METHOD__);
                } catch (\Exception $e) {
                    \Yii::error($e->getMessage(), __METHOD__);
                }
            }
        }
    }
}