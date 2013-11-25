<?php
class Place extends Eloquent
{
	  public function lectures()
    {
            return $this->hasMany('Lecture');
    }
}