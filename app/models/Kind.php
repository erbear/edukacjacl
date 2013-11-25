<?php
class Kind extends Eloquent
{
	  public function lectures()
    {
            return $this->hasMany('Lecture');
    }
}