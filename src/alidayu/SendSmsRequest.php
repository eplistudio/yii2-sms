<?php
/**
 * Created by PhpStorm.
 * User: HoYo
 * Date: 2019-02-22
 * Time: 09:41
 */

namespace eplistudio\sms\alidayu;


use yii\helpers\ArrayHelper;

class SendSmsRequest extends BaseRequest
{
    public static function httpMethod()
    {
        return 'GET';
    }

    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [['currentPage', 'pageSize', 'phoneNumber', 'sendDate'], 'required'],
            [['bizId'], 'string'],
        ]);
    }

    public function getAction()
    {
        return 'SendSms';
    }
}