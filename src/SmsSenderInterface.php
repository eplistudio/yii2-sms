<?php
namespace eplistudio\sms;


interface SmsSenderInterface
{
    public function send($model);
}