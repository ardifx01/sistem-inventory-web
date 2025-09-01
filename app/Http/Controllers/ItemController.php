<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use App\Rules\BarcodeFormat;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ItemsImport;


class ItemController extends Controller
{
    /**
     * Menampilkan daftar item dengan fitur filter dan pencarian.
     */
    public function index(Request $request)
    {
        $query = Item::with('category');

        // filter pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('dscription', 'like', "%{$search}%")
                    ->orWhere('itemCode', 'like', "%{$search}%")
                    ->orWhere('codeBars', 'like', "%{$search}%")
                    ->orWhere('rack_location', 'like', "%{$search}%");
            });
        }

        // filter kategori
        if ($request->has('categories') && !empty($request->categories)) {
            $categoryIds = is_array($request->categories)
                ? $request->categories
                : explode(',', $request->categories);

            $query->whereIn('category_id', $categoryIds);
        }

        // filter rak "ZIP only"
        if ($request->boolean('zip_only')) {
            $query->where(function ($q) {
                $q->where('rack_location', 'ZIP')
                    ->orWhereNull('rack_location');
            });
        }

        $items = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        // urutkan kategori → default selalu paling atas
        $allCategories = Category::all();
        $defaultCategory = $allCategories->firstWhere('is_default', true);
        $otherCategories = $allCategories->filter(fn($cat) => !$cat->is_default)->sortBy('name');

        $categories = collect();
        if ($defaultCategory) {
            $categories->push($defaultCategory);
        }
        $categories = $categories->merge($otherCategories);

        return view('items.index', compact('items', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('items.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'dscription'    => 'required|string|max:255',
            'itemCode'      => 'required|string|max:100|unique:items,itemCode',
            'codeBars'      => [
                'nullable',
                'string',
                'max:100',
                'unique:items,codeBars',
                new BarcodeFormat()
            ],
            'rack_location' => 'nullable|string|max:100',
            'category_id'   => 'required|exists:categories,id',
        ], [
            'codeBars.unique' => 'Barcode sudah digunakan oleh barang lain.',
            'itemCode.unique' => 'Kode barang sudah digunakan.',
        ]);

        if (empty($validatedData['rack_location'])) {
            $validatedData['rack_location'] = 'ZIP';
        }

        Item::create($validatedData);

        return redirect()->route('items.index')->with('success', 'Barang berhasil ditambahkan.');
    }

    public function edit(Item $item)
    {
        $categories = Category::all();
        return view('items.edit', compact('item', 'categories'));
    }

    public function update(Request $request, Item $item)
    {
        $validatedData = $request->validate([
            'dscription'    => 'required|string|max:255',
            'itemCode'      => [
                'required',
                'string',
                'max:100',
                Rule::unique('items', 'itemCode')->ignore($item->id),
            ],
            'codeBars'      => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('items', 'codeBars')->ignore($item->id),
                new BarcodeFormat()
            ],
            'rack_location' => 'nullable|string|max:100',
            'category_id'   => 'required|exists:categories,id',
        ], [
            'codeBars.unique' => 'Barcode sudah digunakan oleh barang lain.',
            'itemCode.unique' => 'Kode barang sudah digunakan.',
        ]);

        if (empty($validatedData['rack_location'])) {
            $validatedData['rack_location'] = 'ZIP';
        }

        $item->update($validatedData);

        return redirect()->route('items.index')->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroy(Item $item)
    {
        $item->delete();
        return redirect()->route('items.index')->with('success', 'Barang berhasil dihapus.');
    }

    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'ids'   => ['required', 'array'],
            'ids.*' => ['integer', 'exists:items,id'],
        ], [
            'ids.required' => 'Tidak ada item yang dipilih untuk dihapus.',
        ]);

        $ids = $validated['ids'];
        Item::whereIn('id', $ids)->delete();

        return redirect()->route('items.index')->with('success', count($ids) . ' item berhasil dihapus.');
    }

    public function deleteAll(Request $request)
    {
        // Kalau mau semua barang tanpa filter:
        Item::truncate();

        // Kalau mau hapus semua sesuai filter kategori pencarian:
        // Item::whereIn('category_id', $request->categories ?? [])->delete();

        return back()->with('success', 'Semua barang berhasil dihapus!');
    }

    public function import(Request $request)
    {
        // Validasi: harus ada file, tipe xls/xlsx/csv, dan bisa multiple
        $request->validate([
            'files.*' => 'required|mimes:xls,xlsx,csv',
        ]);

        try {
            // Cek apakah ada file
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    Excel::import(new \App\Imports\ItemsImport, $file);
                }
            }

            return redirect()->route('items.index')->with('success', 'File berhasil diimport!');
        } catch (\Exception $e) {
            return redirect()->route('items.index')->with('error', 'Gagal import file: ' . $e->getMessage());
        }
    }


}
