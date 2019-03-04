<?php
namespace eplistudio\sms;


use Yii;
use yii\base\Component;

class Dispatcher extends Component
{
    /**
     * @var array|Provider[] the sms providers. Each array element represents a single [[Provider|sms provider]] instance
     * or the configuration for creating the log target instance.
     */
    public $providers = [];

    /**
     * {@inheritdoc}
     */
    public function __construct($config = [])
    {
        parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        foreach ($this->providers as $name => $provider) {
            if (!$provider instanceof Provider) {
                $this->providers[$name] = Yii::createObject($provider);
            }
        }
    }

    public function send($message, $category = 'application')
    {
        $time = microtime(true);
        $messages[] = [$message, $category, $time];
        $this->dispatch($messages);
    }

    public function query($id, $condition = [])
    {
        $provider = $this->providers[$id];
        if ($provider->enabled) {
            try {
                return $provider->query($condition);
            } catch (\Exception $e) {
                Yii::error($e->getMessage(), __METHOD__);
            }
        }

        return false;
    }

    /**
     * Dispatches the sms messages to [[providers]].
     * @param array $messages sms messages. This property is managed by [[exec()]] and [[flush()]].
     * Each log message is of the following structure:
     *
     * ```
     * [
     *   [0] => message (mixed, can be a string or some complex data, such as an exception object)
     *   [1] => category (string)
     *   [2] => timestamp (float, obtained by microtime(true))
     * ]
     * ```
     */
    public function dispatch($messages)
    {
        foreach ($this->providers as $provider) {
            if ($provider->enabled) {
                try {
                    $provider->collect($messages);
                } catch (\Exception $e) {
                    Yii::error($e->getMessage(), $messages[1]);
                }
            }
        }
    }
}