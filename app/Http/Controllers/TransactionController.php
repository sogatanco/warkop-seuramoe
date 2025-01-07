<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\TransactionItemView;

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



    public function getTotalPerHari($month, $year)
    {
        // Validasi bulan dan tahun
        if (!checkdate($month, 1, $year)) {
            return response()->json(['error' => 'Tanggal tidak valid'], 400);
        }

        // Ambil total transaksi per hari untuk bulan dan tahun tertentu
        $data = DB::table('view_transactions')
            ->select(DB::raw('DAY(created_at) as day'), DB::raw('SUM(total_transaksi) as total_transaksi'), DB::raw('DATE(created_at) as date'))
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->groupBy(DB::raw('DAY(created_at), DATE(created_at)'))
            ->orderBy(DB::raw('DAY(created_at)'))
            ->get();

        // Persiapkan array untuk days dan total_transaksi
        $days = [];
        $totalTransaksi = [];

        // Tentukan jumlah hari dalam bulan yang dimaksud
        $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;

        // Loop untuk setiap hari dalam bulan tersebut
        for ($i = 1; $i <= $daysInMonth; $i++) {
            // Format tanggal dan nama hari
            $date = Carbon::createFromDate($year, $month, $i);
            $dayName = $date->format('l, j M'); // Format: Kamis, 1 Jan

            // Cek apakah ada transaksi untuk hari tersebut
            $transactionForDay = $data->firstWhere('day', $i);

            if ($transactionForDay) {
                // Jika ada transaksi, tambahkan ke array
                $days[] = $dayName;
                $totalTransaksi[] = (string) $transactionForDay->total_transaksi; // Pastikan total transaksi dalam bentuk string
            } else {
                // Jika tidak ada transaksi, tambahkan 0 ke array
                $days[] = $dayName;
                $totalTransaksi[] = '0'; // Tidak ada transaksi, set 0
            }
        }

        // Mengembalikan response JSON dengan 2 array terpisah
        return response()->json([
            'days' => $days,
            'total_transaksi' => $totalTransaksi
        ]);
    }

    public function getTransactionData()
    {
        // Total transaksi semua
        $totalTransaksiAll = DB::table('view_transactions')
            ->sum('total_transaksi');

        // Total transaksi minggu ini
        $totalTransaksiThisWeek = DB::table('view_transactions')
            ->whereBetween('created_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ])
            ->sum('total_transaksi');

        // Total transaksi hari ini
        $totalTransaksiToday = DB::table('view_transactions')
            ->whereDate('created_at', Carbon::today())
            ->sum('total_transaksi');

        // Jumlah transaksi hari ini
        $totalTransactionsTodayCount = DB::table('view_transactions')
            ->whereDate('created_at', Carbon::today())
            ->count();

        return response()->json([
            'total_transaksi_all' => $totalTransaksiAll,
            'total_transaksi_this_week' => $totalTransaksiThisWeek,
            'total_transaksi_today' => $totalTransaksiToday,
            'total_transactions_today_count' => $totalTransactionsTodayCount,
        ]);
    }

    public function listTransactionItem(Request $request)
    {
        // Ambil parameter tanggal dari query (default: hari ini)
        $date = $request->query('date', now()->toDateString());

        // Query ke view untuk mendapatkan data berdasarkan tanggal
        $items = TransactionItemView::whereDate('created_at', $date)->get();

        return response()->json([
            'success' => true,
            'data' => $items,
        ]);
    }

}
