<?php
class Kind extends Eloquent
{
	  public function lectures()
    {
            return $this->belongsToMany('Lecture');
    }
}