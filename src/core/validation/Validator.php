<?php

namespace Myprojects\Logger\core\validation;

use Myprojects\Logger\exceptions\ValidationException;

abstract class Validator extends Rules
{
    /** @var array */
    private array $errors = [];

    /**
     * @param array $data
     */
    public function __construct($data)
    {
        $this->rules();
        $this->validate($data);
    }

    /**
     * Main validation logic that switches to the specified validation case. 
     *
     * @param array $data
     * 
     * @return void
     */
    private function validate($data)
    {        
        foreach ($this->rules as $fieldName => $fieldRules) {
            foreach ($fieldRules as $ruleName => $ruleValue) {
                if (is_array($data)) {
                    $data = (object) $data;
                }
                
                switch ($ruleName) {
                    case self::RULE_REQUIRED:
                        if (property_exists($data, $fieldName) === false || !$data->$fieldName) {
                            $this->addError($fieldName, self::RULE_REQUIRED);
                        }
                        break;
                    case self::RULE_LISTED_IN:
                        if (!isset($data->$fieldName) || !in_array($data->$fieldName, $ruleValue)) {
                            $this->addError($fieldName, self::RULE_LISTED_IN);
                        }
                        break;
                }
            }
        }

        if (!empty($this->errors)) {
            $validationException = new ValidationException();
            $validationException->setValidationMessage($this->errors);
            throw $validationException;
        }
    }

    /**
     * Collects validation error messages. 
     *
     * @param string $fieldName
     * @param string $rule
     * 
     * @return void
     */
    private function addError(string $fieldName, string $rule)
    {
        $this->errors[$fieldName][] = 'Validation error found on rule ' . $rule;
    }

    abstract function rules();
}
