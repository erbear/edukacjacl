<?php
class Code extends Eloquent
{
	public function lecture()
    {
            return $this->hasMany('Term');            
    }

}