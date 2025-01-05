<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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

    public function getProductsActive()
    {
        // Ambil semua produk dari database
        $products = Product::where('status', 1)->get();

        // Mengembalikan response JSON
        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        // Validasi input status
        $request->validate([
            'status' => 'required|in:0,1', // 0 untuk Non Aktif, 1 untuk Aktif
        ]);

        // Cari produk berdasarkan ID
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan'
            ], 404);
        }

        // Update status produk
        $product->status = $request->status;
        $product->save(); // Simpan perubahan

        // Kembalikan response sukses
        return response()->json([
            'success' => true,
            'message' => 'Status produk berhasil diupdate',
            'data' => $product
        ], 200);
    }

    public function destroy($id)
    {
        try {
            // Temukan produk berdasarkan ID
            $product = Product::findOrFail($id);

            // Hapus produk
            $product->delete();

            // Kembalikan respon berhasil
            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil dihapus.',
            ], 200);
        } catch (ModelNotFoundException $e) {
            // Jika produk tidak ditemukan
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan.',
            ], 404);
        } catch (\Exception $e) {
            // Jika terjadi kesalahan lain
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus produk.',
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'nama' => 'required|string|max:255',
                'stok' => 'required|integer|min:0',
                'harga_jual' => 'required|numeric|min:0',
                'image' => 'nullable|string', // Base64 string for image
            ]);

            $product = Product::findOrFail($id);

            $product->nama = $validatedData['nama'];
            $product->stok = $validatedData['stok'];
            $product->harga_jual = $validatedData['harga_jual'];

            if (!empty($validatedData['image'])) {
                // Optionally, handle image saving here
                $imageData = $request->image;
                $image = str_replace('data:image/jpeg;base64,', '', $imageData); // Hapus header base64 jika ada
                $image = str_replace(' ', '+', $image); // Pastikan spasi diubah menjadi +

                // Generate nama gambar unik
                $imageName = time() . '.jpg';

                // Menyimpan gambar di storage
                Storage::disk('public')->put('gambar_produk/' . $imageName, base64_decode($image));
                $product->gambar = 'gambar_produk/' . $imageName;
            }

            $product->save();

            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully.',
                'data' => $product,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function addStock(Request $request, $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan',
            ], 404);
        }

        $request->validate([
            'stok' => 'required|integer|min:1',
        ]);

        // Update stok produk
        $product->stok += $request->stok;
        $product->save();

        return response()->json([
            'success' => true,
            'message' => 'Stok produk berhasil diperbarui.',
            'data' => $product,
        ]);
    }
}
