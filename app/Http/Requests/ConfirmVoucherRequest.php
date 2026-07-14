<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConfirmVoucherRequest extends FormRequest
{
    public function authorize() { return true; }
    public function rules() {
        return [
            'voucher_id' => 'required|exists:vouchers,id'
        ];
    }
}