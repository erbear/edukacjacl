<?php
class Lecture extends Eloquent
{
    public function kind()
    {
        return $this->belongsTo('Kind');
    }

    public function terms()
    {
    	return $this->hasMany('Term');
    }

}