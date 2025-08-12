<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $query = Item::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('item_code', 'like', "%{$search}%")
                ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        // Filter kategori
        if ($request->has('category_id') && !empty($request->category_id)) {
            $query->where('category_id', $request->category_id);
}

        $items = $query->latest()->paginate(10);
        $categories = Category::all();

        return view('items.index', compact('items', 'categories'));
    }


    public function create()
    {
        $categories = Category::all();
        return view('items.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'item_code'   => 'required|string|max:255|unique:items,item_code',
            'barcode'     => 'nullable|string|max:255|unique:items,barcode',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'rack_location' => 'nullable|string|max:255',
        ], [
            'item_code.unique' => 'Kode item sudah terdaftar.',
            'barcode.unique'   => 'Barcode sudah terdaftar.'
        ]);

        // Kalau rack_location kosong → isi ZIP
        if (empty($validated['rack_location'])) {
            $validated['rack_location'] = 'ZIP';
        }

        Item::create($validated);

        return redirect()->route('items.index')->with('success', 'Barang berhasil ditambahkan.');
    }

    public function show(Item $item)
    {
        $item->load('category');
        return response()->json($item); // untuk pop up detail via AJAX
    }

    public function edit(Item $item)
    {
        $categories = Category::all();
        return view('items.edit', compact('item', 'categories'));
    }

    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'item_code'   => 'required|string|max:255|unique:items,item_code,' . $item->id,
            'barcode'     => 'nullable|string|max:255|unique:items,barcode,' . $item->id,
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'rack_location' => 'nullable|string|max:255',
        ], [
            'item_code.unique' => 'Kode item sudah terdaftar.',
            'barcode.unique'   => 'Barcode sudah terdaftar.'
        ]);

        if (empty($validated['rack_location'])) {
            $validated['rack_location'] = 'ZIP';
        }

        $item->update($validated);

        return redirect()->route('items.index')->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroy(Item $item)
    {
        $item->delete();
        return redirect()->route('items.index')->with('success', 'Barang berhasil dihapus.');
    }
}
