<?php
/**
 * Created by PhpStorm.
 * User: HoYo
 * Date: 2019-02-22
 * Time: 09:37
 */

namespace eplistudio\sms;


interface SmsRequest
{
    public function generateSignature($key);
    public function exec();
}