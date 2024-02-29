<?php

namespace App\Helpers;

use App\Models\InventoryDetail;
use App\Models\InventoryOwner;
use App\Models\LoginHistory;
use Auth;
use Cache;
use Illuminate\Support\Facades\File;

class bHelper
{
    public static function checkRole($variable)
    {
        foreach ($variable as $key => $value) {
            if ($value == env('LDAP_ROLE')) {
                return true;
            }
        }

        return false;
    }
}
