<?php

namespace Myprojects\Logger\core;

use Myprojects\Logger\core\validation\Validator;

class ParametersValidator extends Validator
{
    /**
     * The actual validation rules set for the Logger parameters.
     *
     * @return void
     */
    public function rules()
    {
        $config         = new Config();
        $driverSettings = $config->getDriversSettings();

        $this->field('method')->isRequired();
        $this->field('message')->isRequired();
        $this->field('level')->isRequired()->isListedIn(['debug', 'info', 'warning', 'error']);
        $this->field('driver')->isRequired()->isListedIn(array_keys($driverSettings));
    }
}
