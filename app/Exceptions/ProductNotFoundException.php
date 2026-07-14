<?php
namespace App\Exceptions;

use Exception;

class ProductNotFoundException extends Exception
{
    public function __construct(string $message = "محصول مورد نظر یافت نشد.")
    {
        parent::__construct($message, 404);
    }
}