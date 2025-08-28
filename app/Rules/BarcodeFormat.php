<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class BarcodeFormat implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value)) {
            return; // Allow empty values (nullable)
        }

        // Validate barcode format
        if (!$this->isValidBarcodeFormat($value)) {
            $fail('Format barcode tidak valid. Gunakan format EAN-13 (13 digit), UPC-A (12 digit), EAN-8 (8 digit), atau Code 128.');
        }
    }

    /**
     * Check if barcode format is valid
     */
    private function isValidBarcodeFormat(string $barcode): bool
    {
        // Remove any whitespace
        $barcode = trim($barcode);
        
        // Check for numeric barcodes (EAN-13, UPC-A, EAN-8)
        if (ctype_digit($barcode)) {
            $length = strlen($barcode);
            
            // EAN-13 (13 digits) - Most common international format
            if ($length === 13) {
                return $this->validateEAN13($barcode);
            }
            
            // UPC-A (12 digits) - North American format
            if ($length === 12) {
                return $this->validateUPCA($barcode);
            }
            
            // EAN-8 (8 digits) - Short format
            if ($length === 8) {
                return $this->validateEAN8($barcode);
            }
            
            // Jika numeric tapi bukan panjang yang valid, tolak
            return false;
        }
        
        // Code 128 (alphanumeric, variable length 1-48 characters)
        if ($this->isValidCode128($barcode)) {
            return true;
        }
        
        return false;
    }

    /**
     * Validate EAN-13 barcode with check digit
     */
    private function validateEAN13(string $barcode): bool
    {
        if (strlen($barcode) !== 13) {
            return false;
        }
        
        $checkDigit = (int) substr($barcode, 12, 1);
        $calculatedCheckDigit = $this->calculateEAN13CheckDigit(substr($barcode, 0, 12));
        
        return $checkDigit === $calculatedCheckDigit;
    }

    /**
     * Validate UPC-A barcode with check digit
     */
    private function validateUPCA(string $barcode): bool
    {
        if (strlen($barcode) !== 12) {
            return false;
        }
        
        $checkDigit = (int) substr($barcode, 11, 1);
        $calculatedCheckDigit = $this->calculateUPCACheckDigit(substr($barcode, 0, 11));
        
        return $checkDigit === $calculatedCheckDigit;
    }

    /**
     * Validate EAN-8 barcode with check digit
     */
    private function validateEAN8(string $barcode): bool
    {
        if (strlen($barcode) !== 8) {
            return false;
        }
        
        $checkDigit = (int) substr($barcode, 7, 1);
        $calculatedCheckDigit = $this->calculateEAN8CheckDigit(substr($barcode, 0, 7));
        
        return $checkDigit === $calculatedCheckDigit;
    }

    /**
     * Validate Code 128 format
     */
    private function isValidCode128(string $barcode): bool
    {
        // Code 128 untuk inventory: huruf, angka, spasi, dash, underscore
        $length = strlen($barcode);
        
        if ($length < 1 || $length > 48) {
            return false;
        }
        
        // Hanya izinkan alphanumeric, spasi, dash, underscore untuk inventory
        if (!preg_match('/^[A-Za-z0-9\s\-_]+$/', $barcode)) {
            return false;
        }
        
        return true;
    }

    /**
     * Calculate EAN-13 check digit
     */
    private function calculateEAN13CheckDigit(string $barcode): int
    {
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $digit = (int) $barcode[$i];
            $sum += ($i % 2 === 0) ? $digit : $digit * 3;
        }
        
        $remainder = $sum % 10;
        return $remainder === 0 ? 0 : 10 - $remainder;
    }

    /**
     * Calculate UPC-A check digit
     */
    private function calculateUPCACheckDigit(string $barcode): int
    {
        $sum = 0;
        for ($i = 0; $i < 11; $i++) {
            $digit = (int) $barcode[$i];
            $sum += ($i % 2 === 0) ? $digit * 3 : $digit;
        }
        
        $remainder = $sum % 10;
        return $remainder === 0 ? 0 : 10 - $remainder;
    }

    /**
     * Calculate EAN-8 check digit
     */
    private function calculateEAN8CheckDigit(string $barcode): int
    {
        $sum = 0;
        for ($i = 0; $i < 7; $i++) {
            $digit = (int) $barcode[$i];
            $sum += ($i % 2 === 0) ? $digit * 3 : $digit;
        }
        
        $remainder = $sum % 10;
        return $remainder === 0 ? 0 : 10 - $remainder;
    }
}
