<?php
namespace App\Enums;

enum VoucherType: string
{
    case INBOUND = 'inbound';
    case OUTBOUND = 'outbound';
}