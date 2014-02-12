<?php

class Field extends Eloquent
{
	public function terms()
    {
            return $this->belongsToMany('Term')->withPivot('semestr', 'year')->withTimestamps();            
    }

    public function users()
    {
            return $this->belongsToMany('User')->withTimestamps();            
    }
}