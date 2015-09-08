<?php

class Program extends Eloquent
{
    protected $table = 'programs';
    public function campus(){
		return $this->belongsTo('Campus');
	}
}