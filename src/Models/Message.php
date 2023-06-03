<?php

namespace App\Models;

use App\Models\Conversation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Message extends Model
{
    use HasFactory;
    protected $fillable = ['body','status', 'conversation_id'];
/**
 * Get the conversation that owns the Message
 *
 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
 */
public function conversation(): BelongsTo
{
    return $this->belongsTo(Conversation::class);
}


/**
 * Get the issues opened with this message
 * 
 * @return Illuminate\Database\Eloquent\Relations\HasMany
 */
public function issues(): HasMany
{
    return $this->hasMany(Issue::class);
}
}
