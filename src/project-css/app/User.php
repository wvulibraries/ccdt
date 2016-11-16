<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Auth;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    // Function to check for the adminship
    public function isAdmin(){
      // Check if the user is logged in
      if(Auth::check()){
        // Check if the user is admin
        if(Auth::user()->isAdmin){
          return true;
        }
      }

      // Return false if not
      return false;
    }
}
