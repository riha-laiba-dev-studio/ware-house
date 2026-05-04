<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->restrictOnDelete();
            $table->foreignId('item_variant_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('warehouse_id')->constrained()->restrictOnDelete();
            $table->string('type'); // purchase, sale, transfer_in, transfer_out, adjustment, return_in, return_out, opening
            $table->decimal('quantity', 15, 4);
            $table->decimal('before_quantity', 15, 4)->default(0);
            $table->decimal('after_quantity', 15, 4)->default(0);
            $table->decimal('unit_cost', 15, 2)->default(0);
            $table->string('reference_type')->nullable(); // morphable
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamp('movement_date');
            $table->timestamps();
            $table->index(['item_id', 'warehouse_id', 'type']);
            $table->index(['reference_type', 'reference_id']);
            $table->index('movement_date');
        });
    }
    public function down(): void { Schema::dropIfExists('inventory_movements'); }
};
