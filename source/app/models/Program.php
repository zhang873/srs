<?php

class Program extends Eloquent
{
	public function campus(){
		return $this->belongsTo('Campus');
	}
}