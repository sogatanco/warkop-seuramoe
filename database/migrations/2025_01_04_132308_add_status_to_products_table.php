<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Menambahkan kolom `status` ke tabel `products`
        Schema::table('products', function (Blueprint $table) {
            $table->tinyInteger('status')->default(1)->after('gambar')->comment('1: Active, 0: Inactive');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Menghapus kolom `status` jika migration di-rollback
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}