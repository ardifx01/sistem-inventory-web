<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class RackLocationUniqueExceptZip implements ValidationRule
{
    private $ignoreId;

    public function __construct($ignoreId = null)
    {
        $this->ignoreId = $ignoreId;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value) || $value === 'ZIP') {
            return; // ZIP boleh duplikat dan nilai kosong akan diubah ke ZIP
        }

        // Cek apakah lokasi rak sudah digunakan menggunakan kolom rack_location_unique
        $query = DB::table('items')
            ->where('rack_location_unique', $value);

        if ($this->ignoreId) {
            $query->where('id', '!=', $this->ignoreId);
        }

        if ($query->exists()) {
            $fail('Lokasi rak sudah digunakan oleh barang lain.');
        }
    }
}
