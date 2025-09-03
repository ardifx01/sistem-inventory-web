<?php

namespace App\Observers;

use App\Models\Item;

class ItemObserver
{
    public function creating(Item $item)
    {
        // Set default rack_location ke ZIP jika kosong
        if (empty($item->rack_location)) {
            $item->rack_location = 'ZIP';
        }
        
        // Sync rack_location_unique
        $this->syncRackLocationUnique($item);
    }

    public function updating(Item $item)
    {
        // Set default rack_location ke ZIP jika kosong
        if (empty($item->rack_location)) {
            $item->rack_location = 'ZIP';
        }
        
        // Sync rack_location_unique jika rack_location berubah
        if ($item->isDirty('rack_location')) {
            $this->syncRackLocationUnique($item);
        }
    }

    public function created(Item $item)
    {
        activity()
            ->causedBy(auth()->user())
            ->performedOn($item)
            ->withProperties(['attributes' => $item->getAttributes()])
            ->log('Menambahkan barang');
    }

    public function updated(Item $item)
    {
        activity()
            ->causedBy(auth()->user())
            ->performedOn($item)
            ->withProperties([
                'before' => $item->getOriginal(),
                'after'  => $item->getChanges()
            ])
            ->log('Mengubah barang');
    }

    public function deleted(Item $item)
    {
        activity()
            ->causedBy(auth()->user())
            ->performedOn($item)
            ->log('Menghapus barang');
    }

    /**
     * Sinkronkan rack_location_unique berdasarkan rack_location
     */
    private function syncRackLocationUnique(Item $item): void
    {
        // Jika ZIP, set rack_location_unique ke null
        // Jika bukan ZIP, set rack_location_unique sama dengan rack_location
        $item->rack_location_unique = ($item->rack_location === 'ZIP') ? null : $item->rack_location;
    }
}
