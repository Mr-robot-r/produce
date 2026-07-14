<?php
namespace App\Http\Controllers;

use App\Actions\Voucher\ConfirmVoucherAction;
use App\Actions\Voucher\CancelVoucherAction;
use App\Actions\BOM\CalculateBOMCostAction;
use App\Services\StockService;
use App\Http\Requests\ConfirmVoucherRequest;
use App\Exceptions\CustomException;
use Illuminate\Http\JsonResponse;

class VoucherController extends Controller
{
    public function confirm(ConfirmVoucherRequest $request, ConfirmVoucherAction $action): JsonResponse
    {
        try {
            $data = $action->execute($request->input('voucher_id'));
            return response()->json(['message' => 'حواله تأیید شد.', 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode() ?: 422);
        }
    }

    public function cancel(ConfirmVoucherRequest $request, CancelVoucherAction $action): JsonResponse
    {
        try {
            $action->execute($request->input('voucher_id'));
            return response()->json(['message' => 'حواله لغو شد.']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode() ?: 422);
        }
    }

    public function history(int $productId, int $warehouseId, StockService $stockService): JsonResponse
    {
        $history = $stockService->getHistory($productId, $warehouseId);
        return response()->json($history);
    }

    public function bomCost(int $productId, CalculateBOMCostAction $action): JsonResponse
    {
        try {
            $costData = $action->execute($productId);
            return response()->json($costData);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode() ?: 422);
        }
    }
}