<?php

use Myprojects\Logger\exceptions\ValidationException;
use Myprojects\Logger\Logger;
use PHPUnit\Framework\TestCase;

class LoggerTest extends TestCase
{
    private Logger $logger;

    public function testConfig()
    {
        $logger = $this->init(false, 3, false, 'en');

        $loadedConfig = $logger->config->getConfig();

        $config = [
            'transaction' => [
                'groupByTransaction' => false,
                'batchSize' => 3
            ],
            'drivers' => [
                'json' => [
                    'path' => "Myprojects\\Logger\\drivers\\JsonDriver",
                    'outputDir' => __DIR__ . "\\output"
                ],
                'text' => [
                    'path' => "Myprojects\\Logger\\drivers\\TextDriver",
                    'outputDir' => __DIR__ . "\\output"
                ]
            ],
            "translator" => [
                'active' => false,
                'translationsPath' => "\\src\\translations\\",
                'language' => "en"
            ]
        ];

        $this->assertEquals($config, $loadedConfig);
    }

    public function testLogSingleMessageJsonDriver()
    {
        $logger = $this->init(false, 3, false, 'en');
        
        $response = $logger->log(__METHOD__)
            ->message('Message from LOG -1-')
            ->level('debug')
            ->driver('json')
            ->transaction('orderId', '1')
            ->meta([
                'variationId' => '1',
                'variationNumber' => '242',
                'unitOfMeasurement' => 'Metter'
            ])->exec();

            $this->assertEquals(true, $response);
    }

    public function testLogSingleMessageTextDriver()
    {
        $logger = $this->init(false, 3, false, 'en');
        
        $response = $logger->log(__METHOD__)
            ->message('Message from LOG -2-')
            ->level('debug')
            ->driver('text')
            ->transaction('itemId', '1')
            ->meta([
                'variationId' => '1',
                'variationNumber' => '242',
                'unitOfMeasurement' => 'Metter'
            ])->exec();

            $this->assertEquals(true, $response);
    }

    public function testValidationForExistingDebugLevels()
    {
        $logger    = $this->init(false, 3, false, 'en');
        $exception = null;
        
        try {
            $response = $logger->log(__METHOD__)
                ->message('Message from LOG -2-')
                ->level('debug')
                ->driver('text')
                ->transaction('itemId', '1')
                ->meta([
                    'variationId' => '1',
                    'variationNumber' => '242',
                    'unitOfMeasurement' => 'Metter'
                ])->exec();

            $response = $logger->log(__METHOD__)
                ->message('Message from LOG -2-')
                ->level('error')
                ->driver('text')
                ->transaction('itemId', '1')
                ->meta([
                    'variationId' => '1',
                    'variationNumber' => '242',
                    'unitOfMeasurement' => 'Metter'
                ])->exec();

            $response = $logger->log(__METHOD__)
                ->message('Message from LOG -2-')
                ->level('info')
                ->driver('text')
                ->transaction('itemId', '1')
                ->meta([
                    'variationId' => '1',
                    'variationNumber' => '242',
                    'unitOfMeasurement' => 'Metter'
                ])->exec();

            $response = $logger->log(__METHOD__)
                ->message('Message from LOG -2-')
                ->level('warning')
                ->driver('text')
                ->transaction('itemId', '1')
                ->meta([
                    'variationId' => '1',
                    'variationNumber' => '242',
                    'unitOfMeasurement' => 'Metter'
                ])->exec();
        } catch (ValidationException $exception) {}

        $this->assertEquals(null, $exception);
    }

    public function testValidationForNonExistingDebugLevels()
    {
        $logger    = $this->init(false, 3, false, 'en');
        $exception = null;
        
        try {
            $response = $logger->log(__METHOD__)
                ->message('Message from LOG -2-')
                ->level('iDoNotExist')
                ->driver('text')
                ->transaction('itemId', '1')
                ->meta([
                    'variationId' => '1',
                    'variationNumber' => '242',
                    'unitOfMeasurement' => 'Metter'
                ])->exec();
        } catch (ValidationException $exception) {
            $exception = true;
        }

        $this->assertEquals(true, $exception);
    }

    public function testValidationForRequiredMessageField()
    {
        $logger    = $this->init(false, 3, false, 'en');
        $exception = null;
        
        try {
            $response = $logger->log(__METHOD__)
                ->level('info')
                ->driver('text')
                ->transaction('itemId', '1')
                ->meta([
                    'variationId' => '1',
                    'variationNumber' => '242',
                    'unitOfMeasurement' => 'Metter'
                ])->exec();
        } catch (ValidationException $exception) {
            $exception = true;
        }

        $this->assertEquals(true, $exception);
    }

    public function testValidationForRequiredLevelField()
    {
        $logger    = $this->init(false, 3, false, 'en');
        $exception = null;
        
        try {
            $response = $logger->log(__METHOD__)
                ->message('Message from LOG -2-')
                ->driver('text')
                ->transaction('itemId', '1')
                ->meta([
                    'variationId' => '1',
                    'variationNumber' => '242',
                    'unitOfMeasurement' => 'Metter'
                ])->exec();
        } catch (ValidationException $exception) {
            $exception = true;
        }

        $this->assertEquals(true, $exception);
    }

    public function testValidationForInvalidDriverField()
    {
        $logger    = $this->init(false, 3, false, 'en');
        $exception = null;
        
        try {
            $response = $logger->log(__METHOD__)
                ->message('Message from LOG -2-')
                ->level('info')
                ->driver('iDoNotExist')
                ->transaction('itemId', '1')
                ->meta([
                    'variationId' => '1',
                    'variationNumber' => '242',
                    'unitOfMeasurement' => 'Metter'
                ])->exec();
        } catch (ValidationException $exception) {
            $exception = true;
        }

        $this->assertEquals(true, $exception);
    }

    public function testLogGroupByTransactionJsonDriver()
    {
        $logger   = $this->init(true, 3, false, 'en');
               
        $response = $logger->log(__METHOD__)
                ->message('Message from LOG -2-')
                ->level('info')
                ->driver('text')
                ->transaction('itemId', '1')
                ->meta([
                    'variationId' => '1',
                    'variationNumber' => '242',
                    'unitOfMeasurement' => 'Metter'
                ])->exec();

        $this->assertEquals(true, $response);
    }

    private function init(bool $groupByTransaction, int $batchSize, bool $activeTranslator, string $translatorLang)
    {
        $logger = new Logger();

        $config = [
            'transaction' => [
                'groupByTransaction' => $groupByTransaction,
                'batchSize' => $batchSize
            ],
            'drivers' => [
                'json' => [
                    'path' => "Myprojects\\Logger\\drivers\\JsonDriver",
                    'outputDir' => __DIR__ . "\\output"
                ],
                'text' => [
                    'path' => "Myprojects\\Logger\\drivers\\TextDriver",
                    'outputDir' => __DIR__ . "\\output"
                ]
            ],
            "translator" => [
                'active' => $activeTranslator,
                'translationsPath' => "\\src\\translations\\",
                'language' => $translatorLang
            ]
        ];
        
        $logger->config->loadConfig($config);

        return $logger;
    }
}
