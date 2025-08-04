<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'admin_id',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function members()
    {
        return $this->hasMany(GroupMember::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function invitations()
    {
        return $this->hasMany(GroupInvitation::class);
    }
}
