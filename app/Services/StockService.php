<?php

namespace App\Services;

use App\Models\StockBalance;
use App\Models\StockBatch;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

/**
 * Centralised stock movement helper. All stock writes funnel through
 * `record()` so balances + movement ledger stay consistent regardless
 * of the upstream flow (purchases, sales, transfers, adjustments,
 * waste, returns).
 */
class StockService
{
    public function record(array $args): StockMovement
    {
        return DB::transaction(function () use ($args) {
            $companyId = $args['company_id'];
            $branchId = $args['branch_id'] ?? null;
            $warehouseId = $args['warehouse_id'] ?? null;
            $ingredientId = $args['ingredient_id'];
            $batchId = $args['stock_batch_id'] ?? null;
            $qtyIn = (float) ($args['quantity_in'] ?? 0);
            $qtyOut = (float) ($args['quantity_out'] ?? 0);

            $balance = StockBalance::firstOrNew([
                'branch_id' => $branchId,
                'warehouse_id' => $warehouseId,
                'ingredient_id' => $ingredientId,
            ]);
            $balance->company_id = $companyId;
            $current = (float) ($balance->quantity_on_hand ?? 0);
            $balance->quantity_on_hand = $current + $qtyIn - $qtyOut;
            $balance->available_quantity = $balance->quantity_on_hand - (float) ($balance->reserved_quantity ?? 0);
            if ($qtyIn > 0 && ! empty($args['unit_cost'])) {
                // Weighted-average cost update.
                $oldQty = $current;
                $oldAvg = (float) ($balance->average_cost ?? 0);
                $newTotalQty = $oldQty + $qtyIn;
                if ($newTotalQty > 0) {
                    $balance->average_cost = (($oldQty * $oldAvg) + ($qtyIn * (float) $args['unit_cost'])) / $newTotalQty;
                }
            }
            $balance->stock_value = $balance->quantity_on_hand * (float) ($balance->average_cost ?? 0);
            $balance->save();

            if ($batchId) {
                $batch = StockBatch::find($batchId);
                if ($batch) {
                    $batch->current_quantity = max(0, (float) $batch->current_quantity + $qtyIn - $qtyOut);
                    $batch->save();
                }
            }

            return StockMovement::create([
                'company_id' => $companyId,
                'branch_id' => $branchId,
                'warehouse_id' => $warehouseId,
                'ingredient_id' => $ingredientId,
                'stock_batch_id' => $batchId,
                'created_by' => $args['created_by'] ?? null,
                'movement_type' => $args['movement_type'],
                'reference_type' => $args['reference_type'] ?? null,
                'reference_id' => $args['reference_id'] ?? null,
                'reference_no' => $args['reference_no'] ?? null,
                'quantity_in' => $qtyIn,
                'quantity_out' => $qtyOut,
                'balance_after' => $balance->quantity_on_hand,
                'unit_cost' => $args['unit_cost'] ?? 0,
                'note' => $args['note'] ?? null,
            ]);
        });
    }
}
