<?php

namespace Myprojects\Logger;

use Myprojects\Logger\core\Config;
use Myprojects\Logger\core\Core;
use Myprojects\Logger\core\Translator;

class Logger extends Core
{
    /** @var Config */
    public Config $config;
    /** @var Translator */
    private Translator $translator;
    /** @var array */
    private array $translatorSettings;
    /** @var array */
    private array $transactionSettings;

    public function __construct()
    {
        $this->config               = new Config();
        $this->translator           = new Translator();
        $this->translatorSettings   = $this->config->getTranslatorSettings();
        $this->transactionSettings  = $this->config->getTransactionSettings();

        $this->translator->setPath($this->translatorSettings['translationsPath']);
        $this->translator->setLang($this->translatorSettings['language']);
    
        parent::__construct($this->config->getDriversSettings());
    }

    /**
     * Starts the procedure by either doing one log at a time or by grouping them by transaction.
     *
     * @return void
     */
    public function exec()
    {
        $this->groupByTransaction = $this->transactionSettings['groupByTransaction'];
        $batchSize                = $this->transactionSettings['batchSize'];
        $transactionElement       = $this->parameters['transaction']['element'];
        $transactionValue         = $this->parameters['transaction']['value'];
        $driverKey                = $this->parameters['driver'];

        $this->setTimeStamp();

        if ($this->groupByTransaction === true) {
            $this->logList[$driverKey][$transactionElement][$transactionValue][] = $this->parameters;
            $this->logCount[$driverKey]++;

            if ($this->logCount[$driverKey] === $batchSize) {
                return $this->execute($driverKey);
                //RESET for the next batch.
                $this->logCount[$driverKey] = 0;
                $this->logList[$driverKey]  = [];
            }
        }  else {
            $this->logList[] = $this->parameters;
            
            return $this->execute($driverKey);
        }
    }

    /**
     * Specifies the actual class => method, in which the log was executed.
     *
     * @param string $method
     * 
     * @return Logger
     */
    public function log(string $method)
    {
        $this->parameters['method'] = $method;

        return $this;
    }

    /**
     * Adds a transaction element E.G: 
     * ['orderId' => '102']
     *
     * @param [type] $element
     * @param [type] $value
     * 
     * @return Logger
     */
    public function transaction($element, $value)
    {
        $this->parameters['transaction'] = [
            'element' => $element,
            'value'   => (string) $value
        ];

        return $this;
    }

    /**
     * Adds the actual log message which can be used in parallel with the built in translator.
     * 
     * First parameter takes the message which can be either a simple message string or a string that defines the actual translation string.
     * E.G: translationFile.translationKey
     *
     * Second parameter is just a simple array which contains the values that will replace the "%s" characters from the actual translation message.
     * 
     * @param string $message
     * @param array $values
     * 
     * @return Logger
     */
    public function message(string $message, array $values = [])
    {
        if ($this->translatorSettings['active'] === true) {
            $this->parameters['message'] = $this->translator->translate($message, $values);
        } else {
            $this->parameters['message'] = $message;
        }
    
        return $this;
    }

    /**
     * Adds an array of data parameters that are used to offer more details into the log.
     *
     * @param array $meta
     * @return Logger
     */
    public function meta(array $meta)
    {
        $this->parameters['meta'] = $meta;

        return $this;
    }

    /**
     * Sets the actual LOG debug level.
     * Log debug level valid list: debug, error, info, warning.
     *
     * @param string $level
     * @return Logger
     */
    public function level(string $level)
    {
        $this->parameters['level'] = $level;

        return $this;
    }

    /**
     * Sets the actual driver key that needs to be used in order to identify the desired driver selection.
     * Driver keys are defined in the configuration file. Wrongly defined keys will result in a validation error.
     *
     * @param string $driver
     * @return Logger
     */
    public function driver(string $driver)
    {
        $this->parameters['driver'] = $driver;

        return $this;
    }

    /**
     * Sets the actual timestamp of the log.
     *
     * @return Logger
     */
    private function setTimeStamp()
    {
        $this->parameters['timestamp'] = date("Y-m-d H:i:s");
    }
}
