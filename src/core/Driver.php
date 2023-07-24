<?php

namespace Myprojects\Logger\core;

abstract class Driver
{
    /** @var */
    private string $outputDir = '';

    /**
     * The 'run' method that needs to be implemented for each and every driver.
     * 
     * @param array $data
     * @return boolean
     */
    abstract function run(array $data): bool;

    /**
     * Sets the outpit directory in which the LOG files generated.
     *
     * @param string $outputDir
     * @return void
     */
    public function setOutputDir(string $outputDir): void
    {
        $this->outputDir = $outputDir;
    }

    /**
     * Gets the previously set output directory.
     *
     * @return string
     */
    public function getOutputDir(): string
    {
        return $this->outputDir;
    }

    /**
     * Gets the full file path.
     * 
     * @param string $fileExtension
     */
    public function getFilePath(string $fileExtension)
    {
        $fileName = 'log_' . strtotime(date('Y-m-d')) . $fileExtension;
        $filePath = $this->getOutputDir() . $fileName;

        return $filePath;
    }
}
