<?php
/**
 * Created by PhpStorm.
 * User: HoYo
 * Date: 2019-02-22
 * Time: 09:41
 */

namespace eplistudio\sms\alidayu;


use yii\db\BaseActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * Class SendBatchSmsRequest
 * @package eplistudio\sms\alidayu
 * @property int count
 */
class SendBatchSmsRequest extends BaseRequest
{
    const MAX_AMOUNT = 100;

    /**
     * @var
     */
    public $phoneNumbers = [];

    /**
     * @var
     */
    public $signNames = [];

    /**
     * @var BaseActiveRecord|string
     */
    public $signNameModelClass;

    /**
     * @var string
     */
    public $signNameTargetAttribute = 'sign_name';

    /**
     * @var
     */
    public $templateCode;

    /**
     * @var string
     */
    public $templateCodePattern = '/^SMS_\d+$/';

    /**
     * @var BaseActiveRecord|string
     */
    public $templateCodeModelClass;

    /**
     * @var string
     */
    public $templateCodeTargetAttribute = 'template_code';

    /**
     * @var
     */
    public $smsUpExtendCodes;

    /**
     * @var
     */
    public $templateParams = [];

    public static function httpMethod()
    {
        return 'GET';
    }

    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [['phoneNumbers', 'signNames', 'templateCode'], 'required'],
            [['templateCode'], 'validateTemplateCode'],
            [['phoneNumbers', 'signNames', 'smsUpExtendCodes', 'templateParams'], 'validateQuantity'],
            [['phoneNumbers'], 'each', 'rule' => ['match', 'pattern' => '/^1\d{10}$/']], // TODO 仅支持国内手机
            [['smsUpExtendCodes'], 'each', 'rule' => ['string']],
            [['signNames'], 'each', 'rule' => ['validateSignName']],
        ]);
    }

    public function fields()
    {
        return ArrayHelper::merge(parent::fields(), [
            'PhoneNumberJson' => function () {
                return $this->phoneNumbers ? json_encode($this->phoneNumbers) : null;
            },
            'SignNameJson' => function () {
                return$this->signNames ?  json_encode($this->signNames) : null;
            },
            'TemplateCode' => 'templateCode',
            'SmsUpExtendCodeJson' => function () {
                return $this->smsUpExtendCodes ? json_encode($this->smsUpExtendCodes) : null;
            },
            'TemplateParamJson' => function () {
                return $this->templateParams ? json_encode($this->templateParams) : null;
            },
        ]);
    }

    public function getAction()
    {
        return 'SendBatchSms';
    }

    public function getCount()
    {
        return count($this->phoneNumbers);
    }

    public function validateTemplateCode($attribute)
    {
        $attributeValue = $this->$attribute;
        if (!preg_match($this->templateCodePattern, $attributeValue)) {
            $this->addError($attribute, \Yii::t(__NAMESPACE__, '{attribute} is illegal.'));
        } else if ($template = $this->templateCodeModelClass) {
            $isTemplateExists = $template::find()
                ->where([$this->templateCodeTargetAttribute => $this->$attribute])
                ->exists();

            if (!$isTemplateExists) {
                $this->addError($attribute, \Yii::t(__NAMESPACE__, '{attribute} "{value}" is not exists.'));
            }
        }
    }

    public function validateQuantity($attribute)
    {
        $attributeValue = $this->$attribute;
        if (!empty($attributeValue)) {
            if (($amount = count($attributeValue)) > self::MAX_AMOUNT) {
                $this->addError($attribute, \Yii::t(__NAMESPACE__, '{attribute} quantity is overflow.'));
            } else if ($amount != $this->count) {
                $this->addError($attribute, \Yii::t(__NAMESPACE__, '{attribute} quantity is not match phone quantity.'));
            }
        }
    }

    public function validateSignName($attribute)
    {
        $attributeValue = $this->$attribute;
        if (!strlen($attributeValue)) {
            $this->addError($attribute, \Yii::t(__NAMESPACE__, '{attribute} can not be blank.'));
        } else if ($signName = $this->signNameModelClass) {
            $isSignNameExists = $signName::find()
                ->where([$this->signNameTargetAttribute => $this->$attribute])
                ->exists();

            if (!$isSignNameExists) {
                $this->addError($attribute, \Yii::t(__NAMESPACE__, '{attribute} "{value}" is not exists.'));
            }
        }
    }
}