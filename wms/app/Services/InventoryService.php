<?php
namespace App\Services;

use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Item;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    public function getOrCreateInventory(int $itemId, int $warehouseId, ?int $variantId = null): Inventory
    {
        return Inventory::firstOrCreate(
            ['item_id' => $itemId, 'warehouse_id' => $warehouseId, 'item_variant_id' => $variantId],
            ['quantity' => 0, 'reserved_quantity' => 0]
        );
    }

    public function increaseStock(int $itemId, int $warehouseId, float $qty, float $unitCost, string $type, string $refType, int $refId, ?int $variantId = null, ?string $notes = null): void
    {
        DB::transaction(function () use ($itemId, $warehouseId, $qty, $unitCost, $type, $refType, $refId, $variantId, $notes) {
            $inventory = Inventory::lockForUpdate()->firstOrCreate(
                ['item_id' => $itemId, 'warehouse_id' => $warehouseId, 'item_variant_id' => $variantId],
                ['quantity' => 0, 'reserved_quantity' => 0]
            );
            $before = $inventory->quantity;
            $inventory->increment('quantity', $qty);
            $this->logMovement($itemId, $warehouseId, $variantId, $type, $qty, $before, $before + $qty, $unitCost, $refType, $refId, $notes);
        });
    }

    public function decreaseStock(int $itemId, int $warehouseId, float $qty, float $unitCost, string $type, string $refType, int $refId, ?int $variantId = null, ?string $notes = null): void
    {
        DB::transaction(function () use ($itemId, $warehouseId, $qty, $unitCost, $type, $refType, $refId, $variantId, $notes) {
            $inventory = Inventory::lockForUpdate()->where(['item_id' => $itemId, 'warehouse_id' => $warehouseId, 'item_variant_id' => $variantId])->firstOrFail();
            if ($inventory->quantity < $qty) {
                throw new \Exception("Insufficient stock for item ID {$itemId}. Available: {$inventory->quantity}, Required: {$qty}");
            }
            $before = $inventory->quantity;
            $inventory->decrement('quantity', $qty);
            $this->logMovement($itemId, $warehouseId, $variantId, $type, -$qty, $before, $before - $qty, $unitCost, $refType, $refId, $notes);
        });
    }

    public function adjustStock(int $itemId, int $warehouseId, float $newQty, float $unitCost, string $refType, int $refId, ?int $variantId = null, ?string $notes = null): void
    {
        DB::transaction(function () use ($itemId, $warehouseId, $newQty, $unitCost, $refType, $refId, $variantId, $notes) {
            $inventory = Inventory::lockForUpdate()->firstOrCreate(
                ['item_id' => $itemId, 'warehouse_id' => $warehouseId, 'item_variant_id' => $variantId],
                ['quantity' => 0, 'reserved_quantity' => 0]
            );
            $before = $inventory->quantity;
            $diff   = $newQty - $before;
            $inventory->update(['quantity' => $newQty]);
            $this->logMovement($itemId, $warehouseId, $variantId, 'adjustment', $diff, $before, $newQty, $unitCost, $refType, $refId, $notes);
        });
    }

    public function getLowStockItems(int $warehouseId = null)
    {
        $query = Item::active()->with(['inventory','category','unit'])
            ->whereHas('inventory', function ($q) use ($warehouseId) {
                if ($warehouseId) $q->where('warehouse_id', $warehouseId);
                $q->whereRaw('inventory.quantity <= items.alert_quantity');
            });
        return $query->get();
    }

    public function getStockByWarehouse(int $warehouseId)
    {
        return Inventory::where('warehouse_id', $warehouseId)
            ->with(['item.category', 'item.unit', 'variant'])
            ->where('quantity', '>', 0)
            ->get();
    }

    private function logMovement(int $itemId, int $warehouseId, ?int $variantId, string $type, float $qty, float $before, float $after, float $unitCost, string $refType, int $refId, ?string $notes): void
    {
        InventoryMovement::create([
            'item_id'          => $itemId,
            'item_variant_id'  => $variantId,
            'warehouse_id'     => $warehouseId,
            'type'             => $type,
            'quantity'         => $qty,
            'before_quantity'  => $before,
            'after_quantity'   => $after,
            'unit_cost'        => $unitCost,
            'reference_type'   => $refType,
            'reference_id'     => $refId,
            'created_by'       => auth()->id(),
            'notes'            => $notes,
            'movement_date'    => now(),
        ]);
    }
}
