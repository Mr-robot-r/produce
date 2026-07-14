<?php
namespace App\Enums;

enum VoucherStatus: string
{
    case DRAFT = 'draft';
    case CONFIRMED = 'confirmed';
    case CANCELED = 'canceled';
}