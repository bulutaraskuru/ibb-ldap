<?php

namespace BulutKuru\IbbLdap\Traits;

use Illuminate\Support\Facades\Schema;

trait ExtractsFieldsFromTable
{
    /**
     * Retrieve table fields and set them as fillable in the model.
     */
    protected function initializeExtractsFieldsFromTable()
    {
        if (Schema::hasTable($this->getTable())) {
            $columns = Schema::getColumnListing($this->getTable());
            // Filter out unwanted fields
            $columns = array_diff($columns, ['id', 'created_at', 'updated_at', 'deleted_at']);
            $this->fillable = array_merge($this->getFillable(), $columns);
        }
    }
}
