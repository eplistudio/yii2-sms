<?php
namespace eplistudio\sms;


use yii\base\Component;

/**
 * Class Provider
 * @package eplistudio\sms
 *
 * @property bool $enabled Indicates whether this sms provider is enabled. Defaults to true. Note that the type
 * of this property differs in getter and setter. See [[getEnabled()]] and [[setEnabled()]] for details.
 */
abstract class Provider extends Component
{
    private $_enabled = true;

    abstract public function collect($messages);

    /**
     * Sets a value indicating whether this sms provider is enabled.
     * @param bool|callable $value a boolean value or a callable to obtain the value from.
     * The callable value is available since version 2.0.13.
     *
     * A callable may be used to determine whether the sms provider should be enabled in a dynamic way.
     * For example, to only enable a sms if the current user is registered in you can configure the provider
     * as follows:
     *
     * ```php
     * 'enabled' => function() {
     *     return !Yii::$app->user->isGuest;
     * }
     * ```
     */
    public function setEnabled($value)
    {
        $this->_enabled = $value;
    }

    /**
     * Check whether the sms provider is enabled.
     * @property bool Indicates whether this sms provider is enabled. Defaults to true.
     * @return bool A value indicating whether this sms provider is enabled.
     */
    public function getEnabled()
    {
        if (is_callable($this->_enabled)) {
            return call_user_func($this->_enabled, $this);
        }

        return $this->_enabled;
    }
}