<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageSubscribe extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "cpu",
        "ram",
        "disk",
        "hourly_rate",
        "monthly_rate",
        "yearly_rate",
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = ['created_at', 'updated_at'];

    public function userSubscribes() {
        return $this->hasMany(UserSubscribes::class, 'package_subscribe_id', 'id');
    }
}
