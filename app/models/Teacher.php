<?php
class Teacher extends Eloquent
{
	  public function lectures()
    {
            return $this->hasMany('Lecture');
    }
}