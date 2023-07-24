<?php

namespace Myprojects\Logger\core;

class Config
{
    /** @var array */
    private array $config;

    public function __construct()
    {
        $this->loadConfig();
    }

    /**
     * Loads config data from:
     * 1. The main directory location
     * 2. Loads the data from a specified array param
     *
     * @param array $config
     * @return void
     */
    public function loadConfig(array $config = [])
    {
        if (empty($config)) {
            $fileContent = file_get_contents(__DIR__ . '/../config.json');
            $config      = json_decode($fileContent, true);
        }

        $this->config = $config;
    }

    /**
     * Gets loaded config.
     *
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * Returns transasction settings from the config
     *
     * @return array
     */
    public function getTransactionSettings(): array
    {
        return $this->config['transaction'];
    }

    /**
     * Returns translator settings from the config
     *
     * @return array
     */
    public function getTranslatorSettings(): array
    {
        return $this->config['translator'];
    }

    /**
     * Returns driver settings from the config
     *
     * @return array
     */
    public function getDriversSettings(): array
    {
        return $this->config['drivers'];
    }
}
