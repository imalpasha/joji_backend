<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class MongoUser extends Eloquent {
	    
		protected $collection = 'crud';
		protected $fillable = ['username'];
		protected $dates = ['birthday'];
		
		//protected $collection = 'user2';
		//protected $collection = 'user2';
		//protected $collection = 'user2';

}
