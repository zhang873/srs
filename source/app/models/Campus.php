<?php

class Campus extends Eloquent
{
	public function programs(){
		return $this->hasMany('Program');
	}
}
