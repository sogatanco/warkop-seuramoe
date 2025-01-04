<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;

class ProductController extends Controller
{
    public function insert(Request $request)
    {
        // Validasi input
        $request->validate([
            'nama' => 'required|string|max:255',
            'stok' => 'required|integer',
            'harga_jual' => 'required|numeric',
            'harga_modal' => 'required|numeric',
            'image' => 'required|string', // Validasi base64 image
        ]);

        // Mendapatkan data gambar base64
        $imageData = $request->image;
        $image = str_replace('data:image/jpeg;base64,', '', $imageData); // Hapus header base64 jika ada
        $image = str_replace(' ', '+', $image); // Pastikan spasi diubah menjadi +

        // Generate nama gambar unik
        $imageName = time() . '.jpg';

        // Menyimpan gambar di storage
        Storage::disk('public')->put('gambar_produk/' . $imageName, base64_decode($image));

        // Membuat produk baru
        $product = new Product();
        $product->nama = $request->nama;
        $product->stok = $request->stok;
        $product->harga_jual = $request->harga_jual;
        $product->harga_modal = $request->harga_modal;
        $product->gambar = 'gambar_produk/' . $imageName; // Path gambar yang disimpan

        if ($product->save()) {
            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil ditambahkan',
                'data' => $product
            ]);
        }

    }

    public function getProducts()
    {
        // Ambil semua produk dari database
        $products = Product::all();

        // Mengembalikan response JSON
        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }
}
