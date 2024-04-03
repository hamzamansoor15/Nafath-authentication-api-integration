<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nafath extends Model
{
    use HasFactory;
    public $table = 'nafath';

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    ];

    protected $hidden = [
        'created_at',
        "updated_at",
        "deleted_at"
    ];
}
