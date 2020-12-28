<?php

namespace Egulias\EmailValidator\Validation;

class RFCValidation
{
    /**
     * @var EmailParser
     */
    private $parser;

    /**
     * @var array
     */
    private $warnings = [];

    /**
     * @var InvalidEmail
     */
    private $error;

    public function isValid()
    {
        return true;
    }

    public function getError()
    {
        return $this->error;
    }

    public function getWarnings()
    {
        return $this->warnings;
    }
}
