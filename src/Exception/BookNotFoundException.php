<?php

namespace App\Exception;

use Symfony\Component\Yaml\Exception\RuntimeException;

class BookNotFoundException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Book not found');
    }
}
