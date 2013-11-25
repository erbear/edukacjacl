<?php
class Lecture extends Eloquent
{
    public function day()
    {
        return $this->belongsTo('Day');
    }

    public function hour()
    {
        return $this->belongsTo('Hour');
    }

    public function kind()
    {
        return $this->belongsTo('Kind');
    }

    public function place()
    {
        return $this->belongsTo('Place');
    }

    public function teacher()
    {
        return $this->belongsTo('Teacher');
    }


}