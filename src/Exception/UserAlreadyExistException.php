<?php

namespace App\Exception;

class UserAlreadyExistException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('User already exist!');
    }
}
