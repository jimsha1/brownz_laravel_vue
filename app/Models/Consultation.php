<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Consultation extends Model
{
    protected $guarded = [];

    public function hair_analyses()
    {
        return $this->hasMany(HairAnalysis::class, 'consultation_id');
    }

    public function hair_histories()
    {
        return $this->hasMany(HairHistory::class, 'consultation_id');
    }

    public function hair_client_desires()
    {
        return $this->hasMany(HairClientDesires::class, 'consultation_id');
    }

    public function skin_analyses()
    {
        return $this->hasMany(SkinAnalysis::class, 'consultation_id');
    }

    public function skin_lifestyles()
    {
        return $this->hasMany(SkinLifestyle::class, 'consultation_id');
    }

    public function skin_histories()
    {
        return $this->hasMany(SkinHistory::class, 'consultation_id');
    }

    public function skin_conclusions()
    {
        return $this->hasMany(SkinConclusion::class, 'consultation_id');
    }
}
