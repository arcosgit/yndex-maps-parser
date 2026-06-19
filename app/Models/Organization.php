<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    protected $guarded = [];

    public function reviews()
    {
        return $this->hasMany(OrganizationReview::class, 'org_id', 'id');
    }
}
