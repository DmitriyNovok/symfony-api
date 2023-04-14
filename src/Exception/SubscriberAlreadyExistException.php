<?php

namespace App\Exception;

class SubscriberAlreadyExistException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('Subscriber already exist!');
    }
}
