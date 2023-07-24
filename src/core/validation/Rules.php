<?php

namespace Myprojects\Logger\core\validation;

abstract class Rules
{
    const RULE_REQUIRED  = 'required';
    const RULE_LISTED_IN = 'listedIn';

    /** @var array */
    public $rules = [];

    /**
     * Specifies the validation field.
     *
     * @param string $field
     * 
     * @return Rules
     */
    public function field(string $field)
    {
        $this->rules[$field] = [];
        end($this->rules);

        return $this;
    }

    /**
     * Validates if the field exists
     *
     * @return Rules
     */
    public function isRequired()
    {
        $this->rules[key($this->rules)][self::RULE_REQUIRED] = true;

        return $this;
    }

    /**
     * Checks if the field is present in a list
     *
     * @param array $list
     * 
     * @return Rules
     */
    public function isListedIn(array $list)
    {
        $this->rules[key($this->rules)][self::RULE_LISTED_IN] = $list;

        return $this;
    }
}
