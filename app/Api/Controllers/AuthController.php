<?php

namespace Api\Controllers;

use App\User;
use App\UserProfile;
use App\ActiveUser;
use App\MongoUser;
use Dingo\Api\Facade\API;
use Illuminate\Http\Request;
use Api\Requests\UserRequest;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends BaseController
{
    public function me(Request $request)
    {
        return JWTAuth::parseToken()->authenticate();
    }

    public function authenticate(Request $request)
    {

	
		$username = [
			'username' => 'Hello2',	
        ];
	
		//insert query------
		/*$users = MongoUser::create($username);
		if($users){
			 return response()->json(array(
            'status' => $users,
        ));
		}*/
		
		//delete query-------
		/*$users = MongoUser::where('username','imalpasha')->delete();
		if($users){
			 return response()->json(array(
            'status' => $users,
        ));
		}*/
		
		//update query------
		$users = MongoUser::where('username','Hello')->update($username);
		if($users){
			 return response()->json(array(
            'status' => $users,
        ));
		}		
		
		//read query-------
		//$users = MongoUser::where('username','Hello')->first();
		/*if($users){
			 return response()->json(array(
            'status' => $users,
        ));
		}*/
		
	
        //$users = MongoUser::select()->get();
		
		
		//$users = MongoUser::join('fcm','user2.username','=','pattra51@yahoo.com')->select('username')->get();
		//$users = MongoUser::where('username', '=', 'pattra51@yahoo.com')->select('username')->first();

		
        //return response()->json(array(
        //    'users' => $users,
        //));

        // grab credentials from the request
		/*$email = $request->get('email');
		$password = $request->get('password');

		$affectedRows = User::where('email', '=', $email)->where('password', '=', $password)->first();
        if($affectedRows){
            $status = "success";
            $message = 'Successfully signed';
        }else{
			$status = "error";
            $message = 'Invalid Username / Password';
		}		

					  
        return response()->json(array(
            'status' => $status,
            'message' => $message ,
        ));*/
    }

    public function validateToken() 
    {
        // Our routes file should have already authenticated this token, so we just return success here
        return API::response()->array(['status' => 'success'])->statusCode(200);
    }

    public function register(UserRequest $request)
    {
        $proceed = true;
        $status = null;

		$email = $request->get('email');
		
        $newUser = [
			'email' => $request->get('email'),	
			'password' => $request->get('password'),	
        ];

		$mobile = $request->get('mobile');
		if($request->get('mobile') == null ){
			$mobile = "";
		}
		
		$course = $request->get('course');
		if($request->get('course') == null ){
			$course = "";
		}
		
		
        $userEmail = [
            
			'user_email' => $request->get('email'),	
			'user_firstname' => $request->get('firstname'),
            'user_lastname' => $request->get('lastname'),
            'user_mobile' => $mobile,
			'user_course' => $course,
	    ];

        //check if email already existed.
        $affectedRows = UserProfile::where('user_email', '=', $email)->first();
        if($affectedRows){
            $status = "error";
            $message = 'Email already taken!';
            $proceed = false;
        }

        if($proceed){
            //create new user profile into users_profile table
            $userProfile = UserProfile::create($userEmail);

            if(!empty($userProfile)){
                //create new user into users table
                $user = User::create($newUser);
            }

            if(!empty($user->id)){
                $status = 'success';
                $message = "Successfully registered!";
            }
        }

        return response()->json(array(
            'status' => $status,
            'message' => $message,
        ));
    }

    public function register2(UserRequest $request)
    {
        $proceed = true;
        $status = null;

        $newUser = [
            'username' => $request->get('username'),
            'email' => $request->get('email'),
            'password' => bcrypt($request->get('password')),

        ];

        $userEmail = [
            'user_email' => $request->get('email'),
        ];

        //check if email already existed.
        $email = $userEmail['user_email'];
        $affectedRows = UserProfile::where('user_email', '=', $email)->first();
        if($affectedRows){
            $status = "error";
            $message = 'The email has already been taken!';
            $proceed = false;
        }

        if($proceed){
            //create new user profile into users_profile table
            $userProfile = UserProfile::create($userEmail);

            if(!empty($userProfile)){
                //create new user into users table
                $user = User::create($newUser);
            }

            if(!empty($user->id)){
                $status = 'success';
                $message = "Successfully registered!";
            }
        }

        return response()->json(array(
            'status' => $status,
            'message' => $message,
        ));
    }

    public function logout(Request $request) {

        $signature = $request->only('signature');

        //clear from active user
        $affectedRows = ActiveUser::where('signature', '=', $signature)->delete();

        if($affectedRows){
            $status = 'success';
        }else{
            $status = 'failed';
        }
        return response()->json(array(
            'status' => $status,
            'message' => "Successfully log-out!"
        ));


    }

	
	public function viewProfile(UserRequest $request)
    {
		
		$email = $request->get('email');
    
        $affectedRows = UserProfile::where('user_email', '=', $email)->first();

		if($affectedRows){
            $status = 'success';
            $message = "Profile info loaded";
        }else{
			$status = 'error';
            $message = "Failed to load profile info";
		}  

        return response()->json(array(
            'status' => $status,
            'message' => $message,
			'info' => $affectedRows,
        ));
    }
	
	public function updateProfile(UserRequest $request)
    {
		$email = $request->get('email');
		
		//split image
		$imageBase64 = $request->get('userImage');
		
		$binary=base64_decode($imageBase64);
        //header('Content-Type: bitmap; charset=utf-8');
        $imgName =  time().'_'.'profile.png';
        $file = fopen('user_image/'.$imgName, 'wb');
        fwrite($file, $binary);
        fclose($file);	
		
		$userProfile = [   
			'user_email' => $request->get('email'),	
			'user_firstname' => $request->get('firstname'),
            'user_lastname' => $request->get('lastname'),
            'user_mobile' => $request->get('mobile'),	
			'user_course' => $request->get('course'),	
			'user_image' => $imgName,	

	    ];
		
		$affectedRows = UserProfile::where('user_email', '=', $email)->update($userProfile);
            if($affectedRows){
                $status = 'success';
                $message = 'Successfully updated!';

            }else{
                $status = 'error';
                $message = 'Update profile failed!';
            }
			
        
        return response()->json(array(
            'status' => $status,
            'message' => $message,
			'info' => $affectedRows,
        ));
    }
	
	
}