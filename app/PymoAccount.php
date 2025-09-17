<?php

namespace App;

use App\Utils\Util;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PymoAccount extends Model
{

    protected $guarded = ['id'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $table = 'pymo_accounts';
    public $timestamps = false;
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
