<?php

namespace App\Models\Iceburg;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModuleConvertable extends Model
{
    use HasFactory;
    public $table="ice_modules_convertables";

    public function module(){
        return $this->hasMany(Module::class, 'id', 'module_id');
    }
}
