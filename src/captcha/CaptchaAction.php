<?php
namespace eplistudio\sms\captcha;

use eplistudio\sms\Alidayu;
use yii\base\InvalidConfigException;
use yii\rest\Action;
use yii\web\UnprocessableEntityHttpException;

class CaptchaAction extends Action
{
    public $scope;

    public $templateCode;

    public $verifyCodeLength = 4;

    public function init()
    {
        parent::init();
        if (!\Yii::$app->has('sms')) {
            throw new InvalidConfigException('The "sms" module must be set.');
        } else if (!$this->templateCode) {
            throw new InvalidConfigException('The "templateCode" property must be set.');
        }
    }

    public function run()
    {
        $mobile = \Yii::$app->request->getQueryParam(Captcha::MOBILE_VAR);
        if (!preg_match('/^1[34578]\d{9}$/', $mobile)) {
            throw new UnprocessableEntityHttpException();
        }

        $sms = \Yii::$app->get('sms');
        if ($sms instanceof Alidayu) {
            $code = Captcha::getVerifyCode($mobile, $this->scope, $this->verifyCodeLength);
            $isSend = Captcha::$fixedVerifyCode ?: $sms->send($mobile, $this->templateCode, ['code' => $code]);
            \Yii::debug($isSend, __METHOD__);
            return $isSend;
        } else {
            throw new InvalidConfigException('The "sms" module is invalid.');
        }
    }
}