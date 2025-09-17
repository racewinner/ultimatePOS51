<?php

namespace Modules\MercadoLibre\Model;

use Illuminate\Database\Eloquent\Model;

class MercadoLibreSyncLog extends Model
{
    protected $table = "mercadolibre_sync_logs";
    protected $guarded = ['id'];
}
