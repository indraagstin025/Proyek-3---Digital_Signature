<?php

namespace App\Models;

use App\Enums\DocumentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'file_path',
        'user_id',
        'group_id',
        'original_hash',
        'status',
    ];

   protected $casts = [
        'status' => DocumentStatus::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function signatures()
    {
        return $this->hasMany(Signature::class);
    }
}
