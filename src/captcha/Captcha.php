<?php
namespace eplistudio\sms\captcha;


class Captcha 
{
    const MOBILE_VAR = 'mobile';
    const VERIFY_CODE_VAR = 'code';

    const VERIFY_CODE_MIN_LENGTH = 4;

    public static $fixedVerifyCode;

    public static $duration = 900;

    public static function getCacheKey($mobile, $scope)
    {
        return "__captcha/{$mobile}/{$scope}";
    }

    public static function getVerifyCode($mobile, $scope, $verifyCodeLength = null)
    {
        if (self::$fixedVerifyCode !== null) {
            return self::$fixedVerifyCode;
        }

        $cache = \Yii::$app->cache;
        $cacheKey = Captcha::getCacheKey($mobile, $scope);
        if (!$cache->exists($cacheKey) || $verifyCodeLength > 0) {
            $verifyCodeLength = ($verifyCodeLength < self::VERIFY_CODE_MIN_LENGTH) ? self::VERIFY_CODE_MIN_LENGTH : $verifyCodeLength;
            $cache->set($cacheKey, self::generateVerifyCode($verifyCodeLength), self::$duration);
        }

        return $cache->get($cacheKey);
    }

    public static function generateVerifyCode($verifyCodeLength)
    {
        $max = pow(10, $verifyCodeLength) - 1;
        mt_srand((int) bindec(\Yii::$app->security->generateRandomKey(8)));
        return sprintf("%0{$verifyCodeLength}d", mt_rand(0, $max));
    }

    public static function validateVerifyCode($mobile, $scope, $verifyCode)
    {
        $isValidate = true;
        if (self::getVerifyCode($mobile, $scope) !== $verifyCode) {
            $isValidate = false;
        }

        return $isValidate;
    }

    public static function destroyVerifyCode($cacheKey)
    {
        $cache = \Yii::$app->cache;
        return $cache->delete($cacheKey);
    }
}