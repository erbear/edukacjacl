<?php
class Place extends Eloquent
{
	  public function terms()
    {
            return $this->belongsToMany('Term');
    }
}