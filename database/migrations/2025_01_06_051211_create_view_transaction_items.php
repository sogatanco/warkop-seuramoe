<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateViewTransactionItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
            CREATE VIEW view_transaction_items AS
            SELECT 
                transaction_items.transaction_id AS id_transaksi,
                transaction_items.product_id AS id_produk,
                products.nama AS product_name,
                transaction_items.jumlah AS jumlah_produk,
                transaction_items.harga AS harga_per_item,
                (transaction_items.jumlah * transaction_items.harga) AS total_harga,
                transactions.created_at
            FROM transaction_items
            JOIN transactions ON transactions.id = transaction_items.transaction_id
            JOIN products ON transaction_items.product_id = products.id
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP VIEW IF EXISTS view_transaction_items");
    }
}
