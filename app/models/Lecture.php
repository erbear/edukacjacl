<?php
class Lecture extends Eloquent
{
    public function kind()
    {
        return $this->hasOne('Kind');
    }

    public function terms()
    {
    	return $this->hasMany('Term');
    }

}