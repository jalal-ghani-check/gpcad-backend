<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class BaseModel extends Model
{
    public static function getTableColumns($table)
    {
        return Schema::getColumnListing($table);
    }

    public static function getTableName()
    {
        return with(new static)->getTable();
    }
}
