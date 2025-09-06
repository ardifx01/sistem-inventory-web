<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use App\Rules\BarcodeFormat;
use App\Rules\RackLocationFormat;
use App\Rules\RackLocationUniqueExceptZip;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ItemsImport;
use App\Exports\ItemsTemplateExport;


class ItemController extends Controller
{
    /**
     * Menampilkan daftar item dengan fitur filter dan pencarian.
     */
    public function index(Request $request)
    {
        $query = Item::with('category');

        // search dscription, itemCode, codeBars, category.name
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('dscription', 'like', '%' . $searchTerm . '%')
                    ->orWhere('itemCode', 'like', '%' . $searchTerm . '%')
                    ->orWhere('codeBars', 'like', '%' . $searchTerm . '%')
                    ->orWhereHas('category', function ($categoryQuery) use ($searchTerm) {
                        $categoryQuery->where('name', 'like', '%' . $searchTerm . '%');
                    });
            });
        }

        // filter kategori
        if ($request->filled('categories')) {
            $categoryIds = array_filter($request->input('categories'));
            if (!empty($categoryIds)) {
                $query->whereIn('category_id', $categoryIds);
            }
        }

        // filter rak "ZIP only"
        if ($request->boolean('zip_only')) {
            $query->where(function ($q) {
                $q->where('rack_location', 'ZIP')
                    ->orWhereNull('rack_location');
            });
        }

        // pagination dengan opsi per_page
        $perPage = $request->input('per_page', 25);
        $allowedPerPage = [25, 50, 100];
        if (!in_array((int)$perPage, $allowedPerPage)) {
            $perPage = 25;
        }

        $items = $query->orderBy('dscription', 'asc')
            ->paginate($perPage)
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

        // Jika AJAX request, return JSON
        if ($request->ajax()) {
            try {
                // Generate table body HTML
                $tableBodyHtml = '<tbody id="itemsTableBody" class="divide-y divide-gray-200 dark:divide-gray-700">';
                
                if ($items->isEmpty()) {
                    $tableBodyHtml .= '<tr><td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">Barang tidak ditemukan.</td></tr>';
                } else {
                    foreach ($items as $item) {
                        $userCanEdit = in_array(auth()->user()->role, ['admin', 'superadmin']);
                        $editButton = $userCanEdit ? '<a href="' . route('items.edit', $item->id) . '" class="px-3 py-0.5 rounded bg-yellow-500 text-white hover:bg-yellow-600 text-sm">Edit</a>' : '';
                        $deleteButton = $userCanEdit ? '<button type="button" class="px-3 py-0.5 rounded bg-red-600 text-white hover:bg-red-700 text-sm delete-item-btn" data-item-id="' . $item->id . '" data-item-name="' . htmlspecialchars($item->dscription) . '">Hapus</button>' : '';
                        $checkbox = $userCanEdit ? '<input type="checkbox" class="itemCheckbox w-4 h-4" value="' . $item->id . '">' : '';
                        $deleteForm = $userCanEdit ? '<form id="delete-form-' . $item->id . '" action="' . route('items.destroy', $item->id) . '" method="POST" class="inline-block">' . csrf_field() . method_field('DELETE') . $deleteButton . '</form>' : '';
                        $actionCell = $userCanEdit ? '<td class="px-4 py-3 text-center"><div class="flex justify-center gap-x-2">' . $editButton . $deleteForm . '</div></td>' : '';
                        $checkboxCell = $userCanEdit ? '<td class="px-4 py-3 text-center">' . $checkbox . '</td>' : '';
                        
                        $tableBodyHtml .= '<tr>' .
                            '<td class="px-4 py-3">' . htmlspecialchars($item->dscription) . '</td>' .
                            '<td class="px-4 py-3 text-center">' . htmlspecialchars($item->itemCode) . '</td>' .
                            '<td class="px-4 py-3 text-center">' . htmlspecialchars($item->codeBars ?? '-') . '</td>' .
                            '<td class="px-4 py-3 text-center">' . htmlspecialchars($item->category->name ?? '-') . '</td>' .
                            '<td class="px-4 py-3 text-center">' . htmlspecialchars($item->rack_location) . '</td>' .
                            $actionCell . $checkboxCell .
                            '</tr>';
                    }
                }
                
                $tableBodyHtml .= '</tbody>';
                
                // Generate pagination info
                $paginationInfo = sprintf(
                    'Menampilkan %d - %d dari %d total barang%s',
                    $items->firstItem() ?? 0,
                    $items->lastItem() ?? 0,
                    $items->total(),
                    (request('per_page') && request('per_page') != 25) ? ' (' . request('per_page') . ' item per halaman)' : ''
                );
                
                return response()->json([
                    'html' => $tableBodyHtml,
                    'pagination' => $items->withQueryString()->links()->render(),
                    'info' => '<div class="text-sm text-gray-600 dark:text-gray-400">' . $paginationInfo . '</div>',
                    'total' => $items->total(),
                    'current_page' => $items->currentPage(),
                    'per_page' => $items->perPage(),
                    'from' => $items->firstItem(),
                    'to' => $items->lastItem()
                ]);
            } catch (\Exception $e) {
                \Log::error('AJAX pagination error: ' . $e->getMessage());
                return response()->json(['error' => 'Server error'], 500);
            }
        }

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
            'rack_location' => [
                'nullable',
                'string',
                'max:100',
                new RackLocationUniqueExceptZip(),
                new RackLocationFormat()
            ],
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
            'rack_location' => [
                'nullable',
                'string',
                'max:100',
                new RackLocationUniqueExceptZip($item->id),
                new RackLocationFormat()
            ],
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
    $request->validate([
        'files.*' => 'required|mimes:xls,xlsx,csv',
    ]);

    try {
        $successTotal = 0;
        $failedTotal  = 0;
        $failedItems  = [];
        $errorDetails = []; // Store all error details

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $import = new \App\Imports\ItemsImport;
                \Maatwebsite\Excel\Facades\Excel::import($import, $file);

                $successTotal += $import->successCount;
                $failedTotal  += $import->failedCount;
                $failedItems  = array_merge($failedItems, $import->failedItems);
                $errorDetails = array_merge($errorDetails, $import->errorDetails ?? []);
            }
        }

        return redirect()->route('items.index')->with([
            'import_success' => $successTotal,
            'import_failed'  => $failedTotal,
            'failed_items'   => $failedItems,
            'error_details'  => $errorDetails,
        ]);
    } catch (\Exception $e) {
        return redirect()->route('items.index')->with('error', 'Gagal import file: ' . $e->getMessage());
    }
}


public function downloadTemplate()
{
    return Excel::download(new ItemsTemplateExport, 'template_import_items.xlsx');
}

public function downloadErrorReport(Request $request)
{
    $errorDetails = json_decode($request->input('error_details', '[]'), true);
    
    if (empty($errorDetails)) {
        return redirect()->back()->with('error', 'Tidak ada data error untuk didownload');
    }
    
    $filename = 'import_errors_' . date('Y-m-d_H-i-s') . '.xlsx';
    
    return Excel::download(new \App\Exports\ImportErrorReportExport($errorDetails), $filename);
}



}
