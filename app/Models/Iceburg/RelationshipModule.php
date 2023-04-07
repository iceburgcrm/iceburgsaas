<?php

namespace App\Models\Iceburg;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelationshipModule extends Model
{
    use HasFactory;
    public $table="ice_relationship_modules";
    protected $guarded = ['id'];

    public function relationship()
    {
        return $this->belongsTo(Relationship::class);
    }

    public function modulefields()
    {
        return $this->belongsTo(Module::class)->with('fields');
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }
}
