<?php
/**
 * Created by PhpStorm.
 * User: HoYo
 * Date: 2017/12/14
 * Time: 下午10:10
 */

namespace eplistudio\sms;


interface SmsSenderInterface
{
    public function send($model);
}