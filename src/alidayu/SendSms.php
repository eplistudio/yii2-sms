<?php
namespace eplistudio\sms\alidayu;

use yii\base\Model;

class SendSms extends Model
{
    public $phoneNumbers;

    public $signName;

    public $templateCode;

    public $templateParams;

    public $outId;

    public function rules()
    {
        return [
            [['phoneNumbers', 'signName', 'templateCode'], 'required'],
            [['templateParams', 'outId'], 'safe'],
            [['phoneNumbers', 'signName', 'templateCode', 'outId'], 'string'],
            [['phoneNumbers'], 'validatePhoneNumbers'],
        ];
    }

    public function validatePhoneNumbers($attribute, $params, $validator)
    {
        $phoneNumbers = $this->$attribute;
        if (!preg_match("/\d{11}$/", $phoneNumbers)) {
            $this->addError($attribute, 'Invalid phone numbers.');
        }
    }
}