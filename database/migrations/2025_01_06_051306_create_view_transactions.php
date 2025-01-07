<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateViewTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
            CREATE VIEW view_transactions AS
            SELECT 
                transactions.id AS id_transaction,
                SUM(transaction_items.jumlah) AS jumlah_item,
                users.name AS nama_kasir,
                SUM(transaction_items.jumlah * transaction_items.harga) AS total_transaksi,
                transactions.created_at
            FROM transactions
            JOIN transaction_items ON transactions.id = transaction_items.transaction_id
            JOIN users ON transactions.kasir = users.id
            GROUP BY transactions.id, users.name, transactions.created_at
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP VIEW IF EXISTS view_transactions");
    }
}
