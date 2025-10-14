<?php

namespace App\Models\API;

use App\Models\User;
use App\Models\UserBaseData;

class ApiUser extends User
{
    protected $table = 'users';

    public function baseData()
    {
        return $this->hasOne(UserBaseData::class, 'user_id');
    }
}
