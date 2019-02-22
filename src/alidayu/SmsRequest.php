<?php
/**
 * Created by PhpStorm.
 * User: HoYo
 * Date: 2019-02-22
 * Time: 11:35
 */

namespace eplistudio\sms\alidayu;


interface SmsRequest extends \eplistudio\sms\SmsRequest
{
    public static function httpMethod();
}