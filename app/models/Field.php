<?php

class Field extends Eloquent
{
	public function terms()
    {
            return $this->hasMany('Term');            
    }

    public function users()
    {
            return $this->belongsToMany('User');            
    }