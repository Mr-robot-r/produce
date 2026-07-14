<?php
namespace App\Exceptions;

use Exception;

class CyclicBOMException extends Exception
{
    public function __construct(string $message = "حلقه در ساختار BOM شناسایی شد.")
    {
        parent::__construct($message, 422);
    }
}