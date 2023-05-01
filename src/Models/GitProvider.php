<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GitProvider extends Model
{
    use HasFactory;
    protected $table = 'git_providers';

    protected $guarded = [];
}
