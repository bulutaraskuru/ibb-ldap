<?php

namespace BulutKuru\IbbLdap\Traits;

use Illuminate\Support\Facades\Schema;

trait ExtractsFieldsFromTable
{
    /**
     * Retrieve table fields and set them as fillable in the model.
     */
    public static function bootExtractsFieldsFromTable()
    {
        static::retrieved(function ($model) {
            if (Schema::hasTable($model->getTable())) {
                $columns = Schema::getColumnListing($model->getTable());
                // Filter out unwanted fields
                $columns = array_diff($columns, ['id', 'created_at', 'updated_at', 'deleted_at']);
                $model->fillable = array_merge($model->getFillable(), $columns);
            }
        });
    }
}
