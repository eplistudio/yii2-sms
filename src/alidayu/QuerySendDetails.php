<?php
namespace eplistudio\sms\alidayu;

use yii\base\Model;

class QuerySendDetails extends Model
{
    public $phoneNumber;

    public $bizId;

    public $sendDate;

    public $pageSize;

    public $currentPage;

    public function rules()
    {
        return [
            [['phoneNumber', 'sendDate', 'pageSize', 'currentPage'], 'required'],
            [['bizId'], 'safe'],
            [['phoneNumber', 'bizId', 'sendDate'], 'string'],
            [['pageSize', 'currentPage'], 'integer'],
            [['pageSize'], 'integer', 'max' => 50],
            [['phoneNumber'], 'validatePhoneNumber'],
        ];
    }

    public function validatePhoneNumber($attribute, $params, $validator)
    {
        $phoneNumber = $this->$attribute;
        if (!preg_match("/\d{11}$/", $phoneNumber)) {
            $this->addError($attribute, 'Invalid phone number.');
        }
    }
}