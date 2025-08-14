<?php

namespace App\Observers;

use App\Models\Item;

class ItemObserver
{
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
}
