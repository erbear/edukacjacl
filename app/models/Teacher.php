<?php
class Teacher extends Eloquent
{
	  public function terms()
    {
            return $this->hasMany('Term');
    }
}