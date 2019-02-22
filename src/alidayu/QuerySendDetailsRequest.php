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
            [['currentPage', 'pageSize', 'phoneNumber', 'sendDate'], 'required'],
            [['bizId'], 'string'],
        ]);
    }
}