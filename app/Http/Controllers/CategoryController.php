<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Exception;

class CategoryController extends Controller
{
    /**
     * Tampilkan semua kategori.
     */
    public function index()
    {
        $categories = Category::all();
        return view('categories.index', compact('categories'));
    }

    /**
     * Simpan kategori baru.
     */
// app/Http/Controllers/CategoryController.php

public function store(Request $request)
{
    try {
        $validated = $request->validate([
            'name'       => 'required|string|max:255|unique:categories,name',
            'is_default' => 'nullable|boolean',
        ]);

        if (!empty($validated['is_default'])) {
            if (Category::where('is_default', true)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya boleh ada satu kategori default.'
                ], 422);
            }
        }

        $validated['is_default'] = $validated['is_default'] ?? false;
        // Simpan kategori dan dapatkan objeknya
        $newCategory = Category::create($validated);

        // Setelah aksi berhasil, kembalikan objek kategori yang baru
        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil ditambahkan.',
            'id' => $newCategory->id,
            'name' => $newCategory->name,
        ], 201); // Gunakan 201 Created untuk respons berhasil
    } catch (ValidationException $e) {
        return response()->json(['success' => false, 'message' => 'Nama kategori sudah terdaftar.'], 422);
    } catch (Exception $e) {
        Log::error('Gagal menambah kategori: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menambahkan kategori.'], 500);
    }
}

    /**
     * Hitung jumlah item dalam kategori.
     */
    public function itemCount(Category $category)
    {
        $count = $category->items()->count();
        $isDefault = $category->is_default;

        return response()->json([
            'category_id' => $category->id,
            'item_count' => $count,
            'is_default' => $isDefault
        ]);
    }

    /**
     * Update kategori.
     */
    public function update(Request $request, Category $category)
    {
        try {
            if ($category->is_default) {
                return response()->json(['success' => false, 'message' => 'Kategori default tidak dapat diedit.'], 403);
            }

            $oldName = $category->name;

            $validated = $request->validate([
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('categories', 'name')->ignore($category->id),
                ],
            ]);

            $category->update($validated);
            
            // Setelah aksi berhasil, kembalikan daftar kategori terbaru untuk di-refresh oleh JS
            $categories = Category::all();
            return response()->json([
                'success' => true,
                'message' => "Kategori '{$oldName}' berhasil diubah menjadi '{$category->name}'.",
                'id'      => $category->id,
                'name'    => $category->name,
                'categories' => $categories
            ]);
        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Nama kategori sudah terdaftar.'], 422);
        } catch (Exception $e) {
            Log::error('Gagal mengupdate kategori: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat mengupdate kategori.'], 500);
        }
    }

    /**
     * Hapus kategori.
     */
    public function destroy(Request $request, Category $category)
    {
        try {
            if ($category->is_default) {
                return response()->json(['success' => false, 'message' => 'Kategori default tidak dapat dihapus.'], 403);
            }

            $action = $request->input('action');
            $itemCount = $category->items()->count();

            $message = '';

            // Lakukan aksi berdasarkan input dari frontend
            if ($itemCount > 0) {
                $defaultCategory = Category::where('is_default', true)->first();
                if ($action === 'move_items') {
                    if (!$defaultCategory) {
                        return response()->json(['success' => false, 'message' => 'Tidak ada kategori default untuk memindahkan barang.'], 409);
                    }
                    $category->items()->update(['category_id' => $defaultCategory->id]);
                    $message = "Kategori **{$category->name}** berhasil dihapus dan **{$itemCount}** barang dipindahkan ke **{$defaultCategory->name}**.";
                } elseif ($action === 'delete_items') {
                    $category->items()->delete();
                    $message = "Kategori **{$category->name}** berhasil dihapus dan **{$itemCount}** barang di dalamnya juga terhapus.";
                }
            } else {
                // Konfirmasi penghapusan untuk kategori tanpa item
                if ($action === 'delete_only') {
                    $message = "Kategori **{$category->name}** berhasil dihapus.";
                } else {
                    return response()->json(['success' => false, 'message' => 'Aksi penghapusan tidak valid.'], 422);
                }
            }

            $category->delete();
            
            // Setelah aksi berhasil, kembalikan daftar kategori terbaru untuk di-refresh oleh JS
            $categories = Category::all();
            return response()->json(['success' => true, 'message' => $message, 'categories' => $categories]);
        } catch (Exception $e) {
            Log::error('Gagal menghapus kategori: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal menghapus kategori.'], 500);
        }
    }
}
