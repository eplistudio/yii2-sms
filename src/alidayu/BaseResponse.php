<?php
namespace eplistudio\sms\alidayu;


use eplistudio\sms\SmsResponse;
use yii\base\Model;


class BaseResponse extends Model implements SmsResponse
{
    const ERROR_BLACK_LIST = 'isv.BLACK_KEY_CONTROL_LIMIT';
    
    const ERROR_MINUTE_LIMIT_CONTROL = 'VALVE:M_MC';
    const ERROR_HOUR_LIMIT_CONTROL = 'VALVE:H_MC';
    const ERROR_DAY_LIMIT_CONTROL = 'VALVE:D_MC';
    const ERROR_BUSINESS_LIMIT_CONTROL = 'isv.BUSINESS_LIMIT_CONTROL';
    
    const ERROR_ACCOUNT_ABNORMAL = 'isv.ACCOUNT_ABNORMAL';
    const ERROR_ACCOUNT_NOT_EXISTS = 'isv.ACCOUNT_NOT_EXISTS';
    const ERROR_AMOUNT_NOT_ENOUGH = 'isv.AMOUNT_NOT_ENOUGH';
    const ERROR_OUT_OF_SERVICE = 'isv.OUT_OF_SERVICE';
    
    const ERROR_TEMPLATE_MISSING_PARAMETERS = 'isv.TEMPLATE_MISSING_PARAMETERS';
    const ERROR_TEMPLATE_PARAMETERS = 'isv.TEMPLATE_PARAMS_ILLEGAL';
    
    const ERROR_PRODUCT_UN_SUBSCRIPT = 'isv.PRODUCT_UN_SUBSCRIPT';
    const ERROR_PRODUCT_UNSUBSCRIBE = 'isv.PRODUCT_UNSUBSCRIBE';
    
    const ERROR_INVALID_SIGNATURE = 'isv.SMS_SIGNATURE_ILLEGAL';
    const ERROR_INVALID_TEMPLATE = 'isv.SMS_TEMPLATE_ILLEGAL';
    const ERROR_INVALID_MOBILE_NUMBER = 'isv.MOBILE_NUMBER_ILLEGAL';
    const ERROR_INVALID_MOBILE_COUNT = 'isv.MOBILE_COUNT_OVER_LIMIT';
    const ERROR_INVALID_PARAMETERS_LENGTH = 'isv.PARAM_LENGTH_LIMIT';
    const ERROR_INVALID_PARAMETERS = 'isv.INVALID_PARAMETERS';
    const ERROR_INVALID_KEYWORDS = 'FILTER';
    const ERROR_INVALID_JSON_PARAMETERS = 'isv.INVALID_JSON_PARAM';
    
    const ERROR_NOT_SUPPORT_URL = 'isv.PARAM_NOT_SUPPORT_URL';
    const ERROR_DENY = 'isv.RAM_PERMISSION_DENY';
    const ERROR_SYSTEM_ERROR = 'isv.SYSTEM_ERROR';

    public $requestId;

    public function rules()
    {
        return [
            [['requestId'], 'required'],
        ];
    }
    
    public static function errorMessages()
    {
        return [
            self::ERROR_BLACK_LIST => '请联系平台解除黑名单。',
            self::ERROR_MINUTE_LIMIT_CONTROL => '请减少每分钟发送数量。',
            self::ERROR_HOUR_LIMIT_CONTROL => '请减少每小时发送数量。',
            self::ERROR_DAY_LIMIT_CONTROL => '请减少每天发送数量。',
            self::ERROR_BUSINESS_LIMIT_CONTROL => '业务限流，请联系平台核查原因。',
            self::ERROR_ACCOUNT_ABNORMAL => '账户异常，请联系平台确认账号。',
            self::ERROR_ACCOUNT_NOT_EXISTS => '账户不存在，请确认账户信息配置是否正确。',
            self::ERROR_AMOUNT_NOT_ENOUGH => '账户余额不足，请对账户进行充值。',
            self::ERROR_OUT_OF_SERVICE => '业务停机，请联系平台核查原因。',
            self::ERROR_TEMPLATE_MISSING_PARAMETERS => '模板缺少变量，请确认模版参数。',
            self::ERROR_TEMPLATE_PARAMETERS => '模板变量里包含非法关键字，请修改模版。',
            self::ERROR_PRODUCT_UN_SUBSCRIPT => '未开通云通信产品，请确认账户信息配置是否正确，如配置无误请开通云通信产品。',
            self::ERROR_PRODUCT_UNSUBSCRIBE => '产品未开通，请确认账户信息配置是否正确，如配置无误请订购产品。',
            self::ERROR_INVALID_SIGNATURE => '请重新申请签名。',
            self::ERROR_INVALID_TEMPLATE => '请重新申请模版。',
            self::ERROR_INVALID_MOBILE_NUMBER => '请使用正确的手机号。',
            self::ERROR_INVALID_MOBILE_COUNT => '手机号码数量超过限制，请减少手机号码数量。',
            self::ERROR_INVALID_PARAMETERS_LENGTH => '参数超出长度限制，请修改参数长度。',
            self::ERROR_INVALID_PARAMETERS => '参数异常，请使用正确的参数。',
            self::ERROR_INVALID_KEYWORDS => '内容包含非法的关键字，请修改短信内容。',
            self::ERROR_INVALID_JSON_PARAMETERS => 'JSON参数不合法，只接受字符串值，请修改JSON参数。',
            self::ERROR_NOT_SUPPORT_URL => '不支持URL，请删除内容中的URL。',
            self::ERROR_DENY => 'RAM权限DENY，请联系平台核查原因',
            self::ERROR_SYSTEM_ERROR => '系统错误，请联系平台核查原因。',
        ];
    }
}