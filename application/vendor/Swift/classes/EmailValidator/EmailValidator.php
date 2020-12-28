<?php

/*
 * this is not complete.
 * originally swift uses https://github.com/egulias/EmailValidator
 * but it has lots of dependencies
 *
 * */


namespace Egulias\EmailValidator;


class EmailValidator
{
    public function __construct()
    {
    }

    public function isValid()
    {
        return true;
    }

    public function hasWarnings()
    {
        return false;
    }

    public function getWarnings()
    {
        return array();
    }

    public function getError()
    {
        return "";
    }
}
