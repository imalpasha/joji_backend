<?php

namespace Api\Controllers;

use App\ActiveUser;
use App\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Api\Requests\ProfileListRequest;

class ProfileListController extends BaseController
{

    public function profileList(ProfileListRequest $request){

        $token = JWTAuth::getToken();

        //select all user info
        $list = User::select('users.id','email')->join('users_profile', 'users_profile.user_email', '=', 'users.email')->get(array('email', 'id'));

        //add user status (online / offline)
        $x = 0;
        foreach($list as $list_val){
            $active = ActiveUser::where('email','=',$list_val['email'])->select('signature')->first();
            if($active != null){
                $list[$x]['user_status'] = "online";
            }else{
                $list[$x]['user_status'] = "offline";
            }
            $x++;
        }

        if(count($list) == 0){
            $status = "error";
            $message = "No user available for now!";
        }else{

            $status = "success";
            $message = "Successfully listed all user!";
            //$token = JWTAuth::refresh($token);
        }

        return response()->json(array(
            'status' => $status,
            'message' => $message,
            'auth_token' => $token,
            'listUser' => $list,

        ));

    }

    public function update(UpdateProfileRequest $request)
    {

        $status = null;
        $message = null;
        $proceed = true;

        $token = JWTAuth::getToken();

        /*try{
             JWTAuth::parseToken()->authenticate();
        }catch(TokenInvalidException $e){
            $status = "error";
            $message = "Token is invalid!";
            $proceed = false;
        }*/

        if($proceed){

            $signature = $request->get('signature');
            $userProfile = [
                'user_dob' => $request->get('userDOB'),
                'user_mobile' => $request->get('userMobile'),
                'user_height' => $request->get('userHeight'),
                'user_weight' => $request->get('userWeight'),
                'user_smoke' => $request->get('userSmoke'),
                'user_religion' => $request->get('userReligion'),
                'user_state' => $request->get('userState'),
                'user_town' => $request->get('userTown'),
                'user_education' => $request->get('userEducation'),
                'user_occupation' => $request->get('userOccupation')
            ];

            //select email from active_user (check if user exist in active user table)
            $email = ActiveUser::where('signature', '=', $signature)->select('email')->first();

            if($email == null){
                return response()->json(array(
                    //'auth_token' => compact('token')['token'],
                    'status' => "failed",
                    'message' => "Invalid signature provided!",
                ));
                exit;
            }

            //if exist - do update query on users_profile table
            $affectedRows = UserProfile::where('user_email', '=', $email->email)->update($userProfile);
            if($affectedRows){

                $status = 'success';
                $message = 'Successfully updated!';
                $token = JWTAuth::refresh($token);

            }else{
                $status = 'error';
                $message = 'Update profile failed!';
            }
        }

        return response()->json(array(
            'auth_token' => $token,
            'status' => $status,
            'message' => $message,
        ));
    }

}