<?php

class Field extends Eloquent
{
	public function terms()
    {
            return $this->belongsToMany('Term')->withTimestamps();            
    }

    public function users()
    {
            return $this->belongsToMany('User')->withTimestamps();            
    }
}