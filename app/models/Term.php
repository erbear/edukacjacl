<?php
class Term extends Eloquent
{
	public function lecture()
    {
            return $this->belongsTo('Lecture');
            
    }

    public function teacher()
    {
            return $this->belongsTo('Teacher');
    }

    public function hour()
    {
            return $this->belongsTo('Hour');
    }

    public function day()
    {
            return $this->belongsTo('Day');
    }

    public function place()
    {
            return $this->belongsTo('Place');
    }

    public function space()
    {
            return $this->belongsTo('Space');
    }

    public function Code()
    {
            return $this->belongsTo('Code');
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