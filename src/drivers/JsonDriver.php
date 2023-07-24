<?php

namespace Myprojects\Logger\drivers;

use Myprojects\Logger\core\Driver;

class JsonDriver extends Driver
{
    const FILE_EXTENSION = '.json';

    /**
     * A driver that outputs log information into a JSON file.
     *
     * @param array $data
     * @return boolean
     */
    public function run(array $data): bool
    {
        $filePath = $this->getFilePath(self::FILE_EXTENSION);

        if (file_exists($filePath)) {
            $currentFileContent = file_get_contents($filePath);
            $tempData           = json_decode($currentFileContent, true);
        }
         
        $tempData[] = $data;
        $outputData = json_encode($tempData, JSON_PRETTY_PRINT);
        $response   = file_put_contents($filePath, $outputData);

        if ($response === false) {
            return $response;
        }

        return true;
    }
}
