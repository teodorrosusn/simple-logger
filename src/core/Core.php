<?php

namespace Myprojects\Logger\core;

use Myprojects\Logger\exceptions\DriverNotFoundException;
use Myprojects\Logger\exceptions\IncompatibleDriverClass;

class Core
{
    /** @var boolean */
    public bool  $groupByTransaction = false;
    /** @var array */
    public array $logCount           = [];
    /** @var array */
    public array $parameters         = [];
    /** @var array */
    public array $logList            = [];
    /** @var array */
    public array $driverSettings;

    /**
     * @param array $driverSettings
     */
    public function __construct(array $driverSettings)
    {
        $this->driverSettings = $driverSettings;
        $this->initLogCount();
    }

    /**
     * The main logic that validates the data, loads the apropriate driver, and executes the logging procecdure.
     *
     * @param string $driverKey
     * 
     * @return void
     */
    public function execute($driverKey)
    {
        if ($this->groupByTransaction) {
            foreach ($this->logList[$driverKey] as $transactionGroups) {
                foreach ($transactionGroups as $transactionGroup) {
                    foreach ($transactionGroup as $parameters) {
                        new ParametersValidator($parameters);
                    }
                }
            }

            $result = $this->loadDriver($driverKey);
        } else {
            new ParametersValidator($this->parameters);
            $result = $this->loadDriver($this->parameters['driver']);
        }

        return $result;
    }

    /**
     * Loads and executes one specified driver from the config path.
     *
     * @param string $driverKey
     * 
     * @return void
     */
    private function loadDriver(string $driverKey)
    {
        $driverPath      = $this->driverSettings[$driverKey]['path'];
        $driverOutputDir = $this->driverSettings[$driverKey]['outputDir'];

        try {
            $driver = new $driverPath;   
        } catch (\Throwable $exception) {
            throw new DriverNotFoundException($exception->getMessage());
        }

        if (!$driver instanceof Driver) {
            throw new IncompatibleDriverClass();
        }

        $runData = $this->groupByTransaction === true ? $this->logList[$driverKey] : $this->parameters;
        $driver->setOutputDir($driverOutputDir);
        
        return $driver->run($runData);
    }

    /**
     * Holds the evidence of how many logs were generated in order to make a later comparison with the batchSize.
     *
     * @return void
     */
    private function initLogCount()
    {
        foreach (array_keys($this->driverSettings) as $driverKey) {
            $this->logCount[$driverKey] = 0;
        }
    }
}
