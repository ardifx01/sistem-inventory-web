<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class RackLocationFormat implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value)) {
            return; // Allow empty/null values
        }

        // Check ZIP format first (ZIP boleh duplikat)
        if ($value === 'ZIP') {
            return;
        }

        // Regular format validation
        if (!preg_match('/^[PBL]\d{2}-\d{2}-\d{2}-\d{2}$/', $value)) {
            $fail('Format lokasi rak tidak valid. Gunakan format (P/B/L)XX-XX-XX-XX atau ZIP (ZIP dapat duplikat sebagai penampung default).');
            return;
        }

        // Extract components
        $type = substr($value, 0, 1);
        if (!in_array($type, ['P', 'B', 'L'])) {
            $fail('Tipe rak harus P (Piece), B (Bulky), atau L (Lower).');
        }
    }
}
