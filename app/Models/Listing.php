<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Listing extends Model
{
    public function subjects() {
    	return $this->belongsToMany('App\Models\Subject');
    }

    public function terms() {
    	return $this->belongsToMany('App\Models\Term');
    }

    public function countries() {
    	return $this->belongsToMany('App\Models\Country');
    }
}
