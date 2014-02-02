<?php
class Hour extends Eloquent
{
	 public function terms()
    {
            return $this->belongsToMany('Term');
    } 
}