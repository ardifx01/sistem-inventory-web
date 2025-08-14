<?php

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;

class GlobalActivityLogObserver
{
    public function created(Model $model)
    {
        activity()
            ->causedBy(auth()->user()) // pelaku (user login)
            ->performedOn($model)      // objek yang diubah
            ->withProperties($model->getAttributes()) // data objek
            ->log('Menambahkan ' . class_basename($model));
    }

    public function updated(Model $model)
    {
        $before = collect($model->getOriginal())
            ->only(['name', 'email', 'username', 'role', 'status']) // hanya field ini
            ->toArray();

        $after = collect($model->getChanges())
            ->only(['name', 'email', 'username', 'role', 'status']) // hanya field ini
            ->toArray();

        activity()
            ->causedBy(auth()->user())
            ->performedOn($model)
            ->withProperties([
                'before' => $before,
                'after' => $after
            ])
            ->log('Mengubah ' . class_basename($model));
    }


    public function deleted(Model $model)
    {
        activity()
            ->causedBy(auth()->user())
            ->performedOn($model)
            ->withProperties($model->getAttributes())
            ->log('Menghapus ' . class_basename($model));
    }
}
