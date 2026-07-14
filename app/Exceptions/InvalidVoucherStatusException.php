<?php
namespace App\Exceptions;

use Exception;

class InvalidVoucherStatusException extends Exception
{
    public function __construct(string $message = "وضعیت حواله برای این عملیات معتبر نیست.")
    {
        parent::__construct($message, 422);
    }
}