<?php
/**
 * Created by PhpStorm.
 * User: HoYo
 * Date: 2019-02-22
 * Time: 10:22
 */

namespace eplistudio\sms\alidayu;


use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;

class QuerySendDetailsResponse extends BaseResponse
{
    public $code;
    public $message;
    public $smsSendDetailDTOs;
    public $totalCount;

    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [['code', 'message', 'smsSendDetailDTOs'], 'safe'],
        ]);
    }

    public function load($data, $formName = null)
    {
        foreach ($data as $key => $value) {
            $data[ucfirst(Inflector::camelize($key))] = $value;
        }

        return parent::load($data, $formName);
    }

    public function getSendDetails()
    {
        return $this->smsSendDetailDTOs['SmsSendDetailDTO'];
    }
}