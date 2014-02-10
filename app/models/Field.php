<?php

class Field extends Eloquent
{
	public function terms()
    {
            return $this->belongsToMany('Term')->withTimestapms();            
    }

    public function users()
    {
            return $this->belongsToMany('User')->withTimestamps();            
    }
}