<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Crm extends Model
{
    use HasFactory;
    protected $casts = [
      'created_at' => 'date:Y-m-d h:i A',
        'update_at' => 'date:y-m-d h:i A'
    ];


    public function type(){
        return $this->hasOne(CrmType::class, "id", "type_id");
    }

    public function status(){
        return $this->hasOne(CrmStatus::class, "id", "status_id");
    }

    public static function deleteCRM($id){
        $crm=Crm::where('id', $id)->first();
        $status=0;
        if(auth()->user()->id == 1 ||  $crm->user_id == auth()->user()->id)
        {
            DB::statement("DROP DATABASE " . $crm->name);
            $status=Crm::where('id', $id)->delete();
        }

        return $status;
    }

}
