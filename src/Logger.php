<?php

namespace Myprojects\Logger;

use Myprojects\Logger\core\Config;
use Myprojects\Logger\core\Core;
use Myprojects\Logger\core\Translator;

class Logger extends Core
{
    public Config $config;
    private Translator $translator;
    private array $translatorSettings;
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

    public function log(string $method)
    {
        $this->parameters['method'] = $method;

        return $this;
    }

    public function transaction($element, $value)
    {
        $this->parameters['transaction'] = [
            'element' => $element,
            'value'   => (string) $value
        ];

        return $this;
    }

    public function message(string $message, array $values = [])
    {
        if ($this->translatorSettings['active'] === true) {
            $this->parameters['message'] = $this->translator->translate($message, $values);
        } else {
            $this->parameters['message'] = $message;
        }
    
        return $this;
    }

    public function meta(array $meta)
    {
        $this->parameters['meta'] = $meta;

        return $this;
    }

    public function level(string $level)
    {
        $this->parameters['level'] = $level;

        return $this;
    }

    public function driver(string $driver)
    {
        $this->parameters['driver'] = $driver;

        return $this;
    }

    private function setTimeStamp()
    {
        $this->parameters['timestamp'] = date("Y-m-d H:i:s");
    }
}
