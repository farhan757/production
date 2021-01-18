<?php

namespace App\Http\Controllers\Profile;

use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use Validator;

class ChangePasswordController extends Controller
{
    //
    function show()
    {
    	return view('Profile.ChangePassword.form');
    }

    function exec(Request $request){
    	
    	if (!(Hash::check($request->input('current-password'), Auth::user()->password))) {
            // The passwords matches
            return view('Profile.ChangePassword.form')->with('error',"Your current password does not matches with the password you provided. Please try again.");
            //return redirect()->back()->with("error","Your current password does not matches with the password you provided. Please try again.");
        }

        if(strcmp($request->input('current-password'), $request->input('new-password')) == 0){
            //Current password and new password are same
            return view('Profile.ChangePassword.form')->with("error","New Password cannot be same as your current password. Please choose a different password.");
            //return redirect()->back()->with("error","New Password cannot be same as your current password. Please choose a different password.");
        }
        $this->validate($request,[
            'current-password' => 'required',
            'new-password' => 'required|string|min:6|confirmed',
        ]);
        /*
        $validatedData = $request->validate([
            'current-password' => 'required',
            'new-password' => 'required|string|min:6|confirmed',
        ]);*/

        //Change Password
        $user = Auth::user();
        $user->password = bcrypt($request->input('new-password'));
        $user->save();

        // /return redirect()->back()->with("data","Password changed successfully !");
        return view('Profile.ChangePassword.form')->with('data',"Password changed successfully !");
    }

    public function postCredentials(Request $request)
	{
	  if(Auth::Check())
	  {
	    $request_data = $request->All();
	    $validator = $this->admin_credential_rules($request_data);
	    if($validator->fails())
	    {
	    	return view('Profile.ChangePassword.form')->with("error","Please enter password");
	      //return response()->json(array('error' => $validator->getMessageBag()->toArray()), 400);
	    }
	    else
	    {  
	      $current_password = Auth::User()->password;           
	      if(Hash::check($request_data['current-password'], $current_password))
	      {           
	        $user_id = Auth::User()->id;                       
	        $obj_user = User::find($user_id);
	        $obj_user->password = Hash::make($request_data['password']);;
	        $obj_user->save(); 
	        return view('Profile.ChangePassword.form')->with('data',"Password changed successfully !");
	      }
	      else
	      {           
	        //$error = array('current-password' => 'Please enter correct current password');
	        //return response()->json(array('error' => $error), 400);
	       return view('Profile.ChangePassword.form')->with("error","Please enter correct current password");
	      }
	    }        
	  }
	  else
	  {
	    return redirect()->to('/');
	  }    
	}

    function admin_credential_rules(array $data)
	{
	  $messages = [
	    'current-password.required' => 'Please enter current password',
	    'password.required' => 'Please enter password',
	  ];

	  $validator = Validator::make($data, [
	    'current-password' => 'required',
	    'password' => 'required|same:password',
	    'password_confirmation' => 'required|same:password',     
	  ], $messages);

	  return $validator;
	}  
}
