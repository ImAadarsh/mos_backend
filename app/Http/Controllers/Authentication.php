<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\User;
use Dirape\Token\Token;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class Authentication extends Controller
{
    public function register(Request $request)
    {
        $rules = array(
            "mobile" => "required",
            "email" => "required|email",
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $validator->errors();
        } else {
            $mobileNumber = $request->phone;
            // Remove whitespace and symbols
            $mobile = preg_replace('/[^0-9]/', '', $mobileNumber);
            if (User::where('email', $request->email)->first()) {
                return response(["status" => false, "message" => "Your Email ID is Already registered. Kindly Use Different Email Id to create a new account."], 200);
            }
            if (User::where('mobile', $mobile)->first()) {
                return response(["status" => false, "message" => "Your Mobile. No is Already registered. Kindly Use Different Mobile No. to create a new account."], 200);
            }
            $user = User::where('mobile', $mobile)->orwhere('email', $request->email)->first();
            if (!$user) {
                $user = new User();
                $user->email = $request->email;
                $user->mobile = $request->mobile;
                $user->first_name = $request->first_name;
                $user->last_name = $request->last_name;
                $user->password = Hash::make($request->password);
                $user->user_type = "admin";
                $user->remember_token = (new Token())->Unique('users', 'remember_token', 60);
                $user->save();
                return response(["status" => true, "message" => "Admin is registered sucessfully."], 200);

            } else {
                return response(["status" => false, "message" => "Phone number or Email is already registered."], 200);
            }
        }
    }
    public function login(Request $request)
    {
        $rules =array(
            "email" => "required|email",
            "password" => "required|min:6",
            "user_type" => "required|in:admin",
        );
        $validator= Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $validator->errors();
        } else {
            if(!User::where('email',$request->email)->where('user_type','admin')->first()){
                return response(["status" =>"failed", "message"=>"User is not Registered or Invaild User Type"], 401);
            }
            $user = User::where('email',$request->email)->where('user_type','admin')->first();
            if(!Hash::check($request->password, $user->password)){
                return response(["status" =>"failed", "message"=>"Incorrect Password"], 401);
            }
            else{
            $user->remember_token = (new Token())->Unique('users', 'remember_token', 60);
            $result= $user->save();
            if ($result) {
                $response = [
                'user' => $user,
                "message"=>"User is Logged IN"
            ];
                return response($response, 200);
            }
            }
        }
    }
    public function getstarted(Request $request)
    {
        $rules =array(
            "mobile" => "required",
            "country_code" => "required",
        );
        $validator= Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $validator->errors();
        } else {
            $mobile = preg_replace('/[^0-9]/', '', $request->mobile);
            if(!User::where('mobile',$mobile )->where('user_type','user')->first()){
                $new = new User();
                $new->mobile = $mobile;
                $new->country_code = $request->country_code;
                $new->mobile_verified = 1;
                $new->remember_token = (new Token())->Unique('users', 'remember_token', 60);
                $new->save();
                $cart = Cart::where('user_id',$new->id)->where('is_bought',0)->first();
                if(!$cart){
                    $cart = new Cart();
                    $cart->user_id = $new->id;
                    $cart->save();
                }
                return response(["status" =>"true", "message"=>"Fill the Details", "data" => $new, "code" => "step1", "cart_id"=> $cart->id], 200);
            }else{
                $user = User::where('mobile',$mobile )->where('user_type','user')->first();
                $cart = Cart::where('user_id',$user->id)->where('is_bought',0)->first();
                    if(!$cart){
                        $cart = new Cart();
                        $cart->user_id = $user->id;
                        $cart->save();
                    }
                if($user->is_data==0){
                    $user->remember_token = (new Token())->Unique('users', 'remember_token', 60);
                    $user->save();
                    return response(["status" =>"true", "message"=>"Fill the Details", "data" => $user, "code" => "step1", "cart_id"=> $cart->id], 200);
                }else{
                    $user->remember_token = (new Token())->Unique('users', 'remember_token', 60);
                    $user->save();
                    return response(["status" =>"true", "message"=>"Student Verified.", "data" => $user, "code" => "final", "cart_id"=> $cart->id], 200);
                }

            }
            
        }
    }
    public function profilecreation(Request $request)
    {
        $rules =array(
            "first_name" => "required",
            "last_name" => "required",
            "school" => "required",
            "city" => "required",
            "grade" => "required",
            "remember_token" => "required"
        );
        $validator= Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $validator->errors();
        } else {
            $mobile = preg_replace('/[^0-9]/', '', $request->mobile);
            if(User::where('remember_token',$request->remember_token)->where('is_data',1)->first()){
                $new = User::where('remember_token',$request->remember_token)->where('user_type','user')->first();
                return response(["status" =>"true", "message"=>"Profile is already created.", "data" => $new, "code" => "step1"], 200);
            }
            if(User::where('remember_token',$request->remember_token)->where('user_type','user')->first()){
                $new = User::where('remember_token',$request->remember_token)->where('user_type','user')->first();
                $new->first_name = $request->first_name;
                $new->last_name = $request->last_name;
                $new->school = $request->school;
                $new->city = $request->city;
                if(User::where('email',$request->email)->where('user_type','user')->first()){
                    return response(["status" =>"false", "message"=>"This Email Address is enrolled with another account. Please try different email address.", "code" => "email"], 401);
                }
                $new->email = $request->email;
                $new->grade = $request->grade;
                $new->is_data = 1;
                $new->save();

                $user = User::find($new->id);
                try {
                    $data = ['name' => $new->first_name ];
                    $email = $user->email;
                    $name = $user->first_name;
                    Mail::send('verification', $data, function ($message) use ($email, $name) {
                        $message->to($email, $name)->subject('Your Account is created with magic of skills. | Verify Your Email Id');
                    });
                    
                } catch (Exception $e) {
                    return response(["status" =>"false", "message"=>$e->getMessage()], 401);
                }
                return response(["status" =>"true", "message"=>"Profile is created.", "data" => $new, "code" => "step1"], 200);
            }else{
               
                return response(["status" =>"false", "message"=>"Session is expired. Don't worry try again."], 401);
            }
            
        }
    }
    public function emailverification(Request $request)
    {
        $rules =array(
            "email" => "required",
            "remember_token" => "required"
        );
        $validator= Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $validator->errors();
        } else {
            if(User::where('remember_token',$request->remember_token)->where('email',$request->email)->where('email_verified',1)->first()){
                return response(["status" =>"true", "message"=>"Email is already verified"], 200);
            }
            if(User::where('remember_token',$request->remember_token)->where('user_type','user')->first()){
                if(User::where('email',$request->email)->where('user_type','user')->where('remember_token','!=',$request->remember_token)->first()){
                    return response(["status" =>"false", "message"=>"This Email Address is enrolled with another account. Please try different email address.", "code" => "email"], 401);
                }
                $new = User::where('remember_token',$request->remember_token)->where('user_type','user')->first();
                $new->email = $request->email;
                $new->email_verified = 1;
                $new->save();
                return response(["status" =>"true", "message"=>"Email is verified"], 200);
            }else{
                return response(["status" =>"true", "message"=>"Validation Token is Expired. Please login Again"], 200);

            }
            
        }
    }
}
