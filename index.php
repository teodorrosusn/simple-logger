<?php

use Myprojects\Logger\exceptions\ValidationException;
use Myprojects\Logger\Logger;

require_once './vendor/autoload.php';

ini_set('xdebug.var_display_max_depth', 20);
ini_set('xdebug.var_display_max_children', 512);
ini_set('xdebug.var_display_max_data', 2048);

class Test
{
    public function run()
    {
        $logger = new Logger();

        //TEST FOR JSON DRIVER

        $result = $logger->log(__METHOD__)
            ->message('Message from LOG -1-')
            ->level('debug')
            ->driver('json')
            ->transaction('orderId', '2')
            ->meta([
                'orderNumber'   => '232',
                'orderDate'     => '2023-10-12',
                'orderQuantity' => '3'
            ])->exec();

        $logger->log(__METHOD__)
            ->message('Message from LOG -2-')
            ->level('info')
            ->driver('json')
            ->transaction('orderId', '1')
            ->meta([
                'orderNumber'   => '124',
                'orderDate'     => '2023-07-1',
                'orderQuantity' => '30'
            ])->exec();

        $logger->log(__METHOD__)
            ->message('Message from LOG -3-')
            ->level('error')
            ->driver('json')
            ->transaction('orderId', '2')
            ->meta([
                'orderNumber'   => '232',
                'orderDate'     => '2023-10-12',
                'orderQuantity' => '3'
            ])->exec();
    


        //TEST FOR TEXT DRIVER

        $result = $logger->log(__METHOD__)
            ->message('Message from LOG -1-')
            ->level('debug')
            ->driver('text')
            ->transaction('orderId', '2')
            ->meta([
                'orderNumber'   => '232',
                'orderDate'     => '2023-10-12',
                'orderQuantity' => '3'
            ])->exec();

        $logger->log(__METHOD__)
            ->message('Message from LOG -2-')
            ->level('info')
            ->driver('text')
            ->transaction('orderId', '1')
            ->meta([
                'orderNumber'   => '124',
                'orderDate'     => '2023-07-1',
                'orderQuantity' => '30'
            ])->exec();

        $logger->log(__METHOD__)
            ->message('Message from LOG -3-')
            ->level('error')
            ->driver('text')
            ->transaction('orderId', '2')
            ->meta([
                'orderNumber'   => '232',
                'orderDate'     => '2023-10-12',
                'orderQuantity' => '3'
            ])->exec();

        var_dump($logger->logList);
    }
} 


$test = new Test();

try {
    $result = $test->run();
} catch (ValidationException $exception) {
    var_dump($exception->getValidationMessage());
}

