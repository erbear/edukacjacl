<?php
class Term extends Eloquent
{
	public function lecture()
    {
            return $this->belongsTo('Lecture');
            
    }

    public function teacher()
    {
            return $this->hasOne('Teacher');
    }

    public function hour()
    {
            return $this->hasone('Hour');
    }

    public function day()
    {
            return $this->hasOne('Day');
    }

    public function place()
    {
            return $this->hasOne('Place');
    }

    public function space()
    {
            return $this->hasOne('Space');
    }

    public function Code()
    {
            return $this->hasOne('Code');
    }
    
    public function fields()
    {
            return $this->belongsToMany('Field');
    }

    public function users()
    {
            return $this->belongsToMany('User');
    }
}