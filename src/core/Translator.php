<?php

namespace Myprojects\Logger\core;

use Myprojects\Logger\exceptions\FileNotFoundException;

class Translator
{
    const FILE_EXTENSION = '.json';

    /** @var string */
    private string $path = '';
    /** @var string */
    private string $lang = 'en';

    /**
     * Main translator that will take a mandatory $location parameter for the file, and
     * a $replaceable array which contains the values that needs to be replaced in 
     * the translation string obtained from the file.
     *
     * @param string $location
     * @param array $replaceable
     * @return void
     */
    public function translate(string $location, array $replaceable = [])
    {
        $location = explode('.', $location);
        
        if (count($location) <= 1) {
            return '';
        }

        $file = $location[0];
        $key  = $location[1];

        $filePath = $this->path . $this->lang . '\\' . $file . self::FILE_EXTENSION;

        if (file_exists($filePath)) {
            $fileContent     = file_get_contents($filePath);
            $parsableContent = json_decode($fileContent, true);

            if ($parsableContent[$key]) {
                if (empty($replaceable)) {
                    return $parsableContent[$key];
                }

                return vsprintf($parsableContent[$key], $replaceable);
            }

            return $key;
        }

        throw new FileNotFoundException('File not found.');
    }

    /**
     * Sets the translations path.
     *
     * @param string $path
     * @return void
     */
    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    /**
     * Gets the translations path.
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }
    
    /**
     * Sets the translator's language.
     *
     * @param string $lang
     * @return void
     */
    public function setLang(string $lang): void
    {
        $this->lang = $lang;
    }

    /**
     * Gets the translatior's language.
     *
     * @return string
     */
    public function getLang(): string
    {
        return $this->lang;
    }
}
