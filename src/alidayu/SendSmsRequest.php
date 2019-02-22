<?php
/**
 * Created by PhpStorm.
 * User: HoYo
 * Date: 2019-02-22
 * Time: 09:41
 */

namespace eplistudio\sms\alidayu;


class SendSmsRequest extends BaseRequest
{
    public static function httpMethod()
    {
        return 'GET';
    }
}