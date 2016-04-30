<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

	
	/**
	 *
	 */
	public function tags()
	{

		return $this->hasMany('App\ApiTag');	

	}

	
	/**
	 *
	 */
	public function invitations()
	{

		return $this->hasMany('App\CalendarInvitation');
		
	}


	
	/**
	 *
	 */
	public function isGlobalAdmin()
	{
		if ( $this->global_admin ) return true;

		return false;
		
	}

	
	/**
	 *
	 */
	public function isAccountAdmin()
	{

		return true;
		
	}



	public static function generteGUID()
	{

		if (function_exists('com_create_guid') === true) {

			return trim(com_create_guid(), '{}');

		}

		return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
	}


	
	/**
	 *
	 */
	public static function emailRegistered($email)
	{

		$r = self::where('email', $email)->first();

		if ($r) return true;

		return false;
		
	}
}
