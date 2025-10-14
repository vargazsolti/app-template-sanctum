<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserBaseData extends Model
{
    protected $table = 'user_basedata';

    protected $fillable = [
        'user_id',
        'full_name',
        'mothers_name',
        'birth_date',
        'birth_place',
        'id_card_number',
        'social_security_number',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
