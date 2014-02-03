<?php
class Day extends Eloquent
{
	 public function terms()
    {
            return $this->hasMany('Term');
    }
}