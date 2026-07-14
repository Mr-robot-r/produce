<?php
namespace App\Exceptions;

use Exception;

class InsufficientStockException extends Exception
{
    public function __construct(string $message = "موجودی کافی برای این کالا وجود ندارد.")
    {
        parent::__construct($message, 422);
    }
}