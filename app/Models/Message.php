<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'message',
        'attachment',
        'attachment_type',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
    
    public function getAttachmentUrlAttribute()
    {
        if ($this->attachment) {
            return asset('storage/' . $this->attachment);
        }
        return null;
    }
    
    public function isImage()
    {
        return strpos($this->attachment_type, 'image') !== false;
    }
    
    public function isPdf()
    {
        return $this->attachment_type === 'application/pdf';
    }
}