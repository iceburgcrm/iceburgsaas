<?php

namespace App\Models\Iceburg;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubpanelField extends Model
{
    use HasFactory;

    public $table = 'ice_subpanel_fields';

    public function field()
    {
        return $this->hasOne(Field::class, 'id', 'field_id')->with('module');
    }
}
