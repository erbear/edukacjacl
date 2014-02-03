<?php
class Space extends Eloquent
{
	public function terms()
    {
            return $this->hasMany('Term');
            
    }
}