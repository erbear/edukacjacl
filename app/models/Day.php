<?php
class Day extends Eloquent
{
	 public function terms()
    {
            return $this->belongsToMany('Term');
    }
}