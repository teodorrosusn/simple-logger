<?php

namespace Myprojects\Logger\drivers;

use Myprojects\Logger\core\Driver;

class TextDriver extends Driver
{
    const FILE_EXTENSION = '.txt';

    /**
     * A driver that outputs log information into a TEXT file.
     *
     * @param array $data
     * @return boolean
     */
    public function run(array $data): bool
    {
        $filePath   = $this->getFilePath(self::FILE_EXTENSION);
        $outputData = print_r($data, true);

        $response = file_put_contents($filePath, $outputData, FILE_APPEND);

        if ($response === false) {
            return $response;
        }

        return true;
    }
}
