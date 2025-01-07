<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateTriggerReduceStock extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::unprepared('
            CREATE TRIGGER reduce_stock_after_insert
            AFTER INSERT ON transaction_items
            FOR EACH ROW
            BEGIN
                -- Periksa apakah stok mencukupi
                IF (SELECT stok FROM products WHERE id = NEW.product_id) >= NEW.jumlah THEN
                    -- Kurangi stok produk
                    UPDATE products
                    SET stok = stok - NEW.jumlah
                    WHERE id = NEW.product_id;
                ELSE
                    -- Jika stok tidak mencukupi, hentikan transaksi dengan error
                    SIGNAL SQLSTATE "45000"
                    SET MESSAGE_TEXT = "Stok tidak mencukupi untuk produk ini";
                END IF;
            END;
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS reduce_stock_after_insert');
    }
}
