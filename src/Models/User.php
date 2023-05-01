<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Conversation;
use Countable;
use Ramsey\Uuid\Type\Integer;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get all of the conversations for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class);
    }

    public function is_super_admin()
    {
        return $this->id === 1;
    }

    public function sharedConversations(){
        return $this->belongsToMany(Conversation::class,'conversations_users','user_id','conversation_id');
    }

    public function count_messages()
    {
     
        $total_messages = 0; 
     
        return $total_messages;
    }


    /**
     * Get all of the git_providers for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function gitProviders(): HasMany
    {
        return $this->hasMany(GitProvider::class);
    }
}
