<?php

class Campus extends Eloquent
{
    protected $table = 'campuses';

    public function programs(){
		return $this->hasMany('Program');
	}
}