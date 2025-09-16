<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use PhpParser\Node\Expr\FuncCall;

class SellerRequest extends Model
{
    protected $fillable=['user_id','status','notes'];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
