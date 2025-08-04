<?php

namespace App\Models;

use App\Enums\SigningMethod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Signature extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'document_id',
        'signature_image_path',
        'signing_method',
        'verification_hash',
        'signed_at',
        'ip_address',
        'signature_image',
    ];

    protected $casts = [
        'signed_at' => 'datetime',
        'signing_method' => SigningMethod::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

}
