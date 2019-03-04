<?php
/**
 * Created by PhpStorm.
 * User: HoYo
 * Date: 2019-02-22
 * Time: 09:42
 */

namespace eplistudio\sms\alidayu;


use yii\helpers\ArrayHelper;

class QuerySendDetailsRequest extends BaseRequest
{
    public $currentPage;
    public $pageSize;
    public $phoneNumber;
    public $sendDate;
    public $bizId;

    public static function httpMethod()
    {
        return 'GET';
    }

    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [['phoneNumber'], 'required'],
            [['bizId'], 'string'],
            [['sendDate'], 'default', 'value' => date('Ymd')],
            [['currentPage'], 'default', 'value' => 1],
            [['pageSize'], 'default', 'value' => 20],
        ]);
    }

    public function fields()
    {
        return ArrayHelper::merge(parent::fields(), [
            'CurrentPage' => 'currentPage',
            'PageSize' => 'pageSize',
            'PhoneNumber' => 'phoneNumber',
            'SendDate' => 'sendDate',
        ]);
    }

    public function getAction()
    {
        return 'QuerySendDetails';
    }
}