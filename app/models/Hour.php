<?php
class Hour extends Eloquent
{
	 public function lectures()
    {
            return $this->hasMany('Lecture');
    } 
}