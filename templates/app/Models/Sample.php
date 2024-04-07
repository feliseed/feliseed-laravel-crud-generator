<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
%%USE_SOFTDELETES1%%

class Sample extends Model
{

    use HasFactory;
    protected $table = %%TABLE_NAME%%

    %%USE_SOFTDELETES2%%

    %%TIMESTAMPS%%
    protected $fillable = [
        %%COLUMNS%%
    ];
}
