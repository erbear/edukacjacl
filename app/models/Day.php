<?php
class Day extends Eloquent
{
	 public function lectures()
    {
            return $this->hasMany('Lecture');
    }
}