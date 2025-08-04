<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupInvitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'email',
        'token',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
