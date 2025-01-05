<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    /**
     * Simpan transaksi.
     */
    public function add(Request $request)
    {
        // Validasi data yang dikirim
        $validatedData = $request->validate([
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.harga_jual' => 'required|numeric',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        // Ambil ID user yang sedang login dari Auth
        $kasirId = Auth::id();

        // Simpan transaksi ke tabel `transactions`
        $transaction = Transaction::create([
            'kasir' => $kasirId,
        ]);

        // Simpan item transaksi ke tabel `transaction_items`
        foreach ($validatedData['products'] as $product) {
            TransactionItem::create([
                'transaction_id' => $transaction->id,
                'product_id' => $product['id'],
                'jumlah' => $product['quantity'],
                'harga' => $product['harga_jual'],
            ]);
        }

        // Response jika berhasil
        return response()->json([
            'message' => 'Transaksi berhasil disimpan.',
            'transaction_id' => $transaction->id,
        ], 201);
    }
}
