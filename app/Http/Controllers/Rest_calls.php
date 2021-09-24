<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use MongoDB\BSON\UTCDateTime;
use MongoDB\BSON\DateTime;
use App\Models\Users;
use Twilio\Rest\Client;


class Rest_calls extends Controller
{
    public function __construct(){

        $this->users = new users();
        // ini_set("display_errors", E_ALL);
        // error_reporting(E_ALL);

    }

    public function sendVarificationCode(Request $request){

        if( $request->header('PHP_AUTH_USER') &&  $request->header('PHP_AUTH_PW')  ){

            $username = $request->header('PHP_AUTH_USER');
            $password = $request->header('PHP_AUTH_PW');
        
            $validateCredentials = varify_basic_auth($password, $username);
            if($validateCredentials == true || $validateCredentials == 1){

                $phone_number = $request->input('phone_number');
                $service_id =  getenv("SERVICE_ID");
                $sid        =  getenv("TWILIO_SID");
                $token      =  getenv("TWILIO_AUTH_TOKEN");


                $twilio = new Client($sid, $token);

                $verification_check = $twilio->verify->v2->services($service_id)
                                                        ->verifications
                                                        ->create($phone_number, 'sms' );

                if($verification_check->sid){
                    
                    return response()->json( ['status' =>  'Code Send Successfully!'] , 200);
                }else{
                    
                    return response()->json([ 'status'  => 'SomeThing Wrong!'], 404);
                }
            }else{

                return response()->json([ 'status'  => 'Headers Faileds!'], 404);
            }

        }else{

            return response()->json([ 'status'  => 'Headers Are Missing!'], 404);
        }

    }//end function


    public function varifyCode(Request $request){

        if( $request->header('PHP_AUTH_USER') &&  $request->header('PHP_AUTH_PW')  ){

            $username = $request->header('PHP_AUTH_USER');
            $password = $request->header('PHP_AUTH_PW');
        
            $validateCredentials = varify_basic_auth($password, $username);
            if($validateCredentials == true || $validateCredentials == 1){


                $service_id =  getenv("SERVICE_ID");
                $sid        =  getenv("TWILIO_SID");
                $token      =  getenv("TWILIO_AUTH_TOKEN");

                $code   = $request->input('code');
                $phone  = $request->input('phone_number');;

                $twilio = new Client($sid, $token);

                $verification_check = $twilio->verify->v2->services($service_id)
                ->verificationChecks
                ->create($code, // code
                        ["to" => $phone]
                );

                if($verification_check->status == 'approved'){

                    return response()->json( ['status' =>  'Phone Verified'] , 200);
                }else{

                    return response()->json( ['status' =>  'SomeThing Went Wrong'] , 404);
                }
            }else{

                return response()->json([ 'status'  => 'Headers Faileds!'], 404);
            }

        }else{

            return response()->json([ 'status'  => 'Headers Are Missing!'], 404);
        }

    }//end function


    public function registerUser(Request $request){
        if( $request->header('PHP_AUTH_USER') &&  $request->header('PHP_AUTH_PW')  ){

            $username = $request->header('PHP_AUTH_USER');
            $password = $request->header('PHP_AUTH_PW');
        
            $validateCredentials = varify_basic_auth($password, $username);
            if($validateCredentials == true || $validateCredentials == 1){

                $arrayRegidter = [

                    'first_name'     =>  $request->input('first_name'),
                    'last_name'      =>  $request->input('last_name'),
                    'email'          =>  strtolower(trim($request->input('email'))),
                    'phone_number'   =>  $request->input('phone_number'),
                    'password'       =>  md5($request->input('password')),
                    'user_role'      =>  2,
                    'created_date'   =>  new UTCDateTime(new \DateTime())
                ];

                $statusChecking = $this->users->isAlreadyExists($request->input('email'));

                if($statusChecking == false || $statusChecking == 0){


                    $checkingPhone =  $this->users->isPhoneAlreadyExists($request->input('phone_number'));

                    if($checkingPhone == true || $checkingPhone == 1  ){

                        $response_array = [

                            'status' =>  'Phone Number is Already Exists',
                            'type'   =>  400
                        ];
        
                        return response()->json($response_array, 404);
                    }else{

                        $checking = $this->users->register($arrayRegidter);

                        $arrayRegidter['_id'] = (string)$checking['id'];
                        if($checking['status'] == true ||  $checking['status'] == 1){

                            return response()->json([ 'status'  => 'Successfully Registed', 'emial' => $request->input('email'), 'data' => $arrayRegidter, 'token' => '' ], 200);
                        }else{

                            return response()->json([ 'status'  => 'There is an error with your Database'], 404);
                        }
                    }

                }else{

                    return response()->json([ 'status'  => 'Email Already Exists'], 404);
                }

            }else{

                return response()->json([ 'status'  => 'Headers Faileds!'], 404);
            }

        }else{

            return response()->json([ 'status'  => 'Headers Are Missing!'], 404);
        }

    }//end function

    public function forgotPassword(Request $request){

        if( $request->header('PHP_AUTH_USER') &&  $request->header('PHP_AUTH_PW')  ){

            $username = $request->header('PHP_AUTH_USER');
            $password = $request->header('PHP_AUTH_PW');
        
            $validateCredentials = varify_basic_auth($password, $username);
            if($validateCredentials == true || $validateCredentials == 1){
                $email                  =   $request->input('email');
                $password               =   md5($request->input('password'));
                $confirmed_password     =   md5($request->input('confirmed_password'));

                if($password  =  $confirmed_password){
                    $this->users->updatePassword($email, $password);

                    return response()->json([ 'status'  => 'Password Successfully Updated'], 200);
                }else{

                    return response()->json([ 'status'  => 'Password and Confirmed Password not Matched'], 404);
                }
            }else{

                return response()->json([ 'status'  => 'Headers Faileds!'], 404);
            }

        }else{

            return response()->json([ 'status'  => 'Headers Are Missing!'], 404);
        }
    }//end function


    public function loginApi(Request $request){

        if( $request->header('PHP_AUTH_USER') &&  $request->header('PHP_AUTH_PW')  ){

            $username = $request->header('PHP_AUTH_USER');
            $password = $request->header('PHP_AUTH_PW');
        
            $validateCredentials = varify_basic_auth($password, $username);
            if($validateCredentials == true || $validateCredentials == 1){

                $email      =    strtolower(trim($request->input('email')));
                $password   =   md5($request->input('password'));

                $getUserData  = $this->users->getUserData($email);

                if(count($getUserData) > 0 ){

                    if($getUserData[0]['password']  ==  $password  ){

                        return response()->json( ['status' =>  'Code Send Successfully!', 'user_data' => $getUserData[0] ] , 200);

                    }else{

                        return response()->json([ 'status'  => 'Invalid UserName or Password'], 404);
                    }

                }else{

                    return response()->json([ 'status'  => 'Invalid UserName or Password'], 404);
                }
            }else{

                return response()->json([ 'status'  => 'Headers Faileds!'], 404);
            }

        }else{

            return response()->json([ 'status'  => 'Headers Are Missing!'], 404);
        }
    }

    //social signup
    public function RegisterUserUsingSocial(Request $request){

        $username = $request->header('PHP_AUTH_USER');
        $password = $request->header('PHP_AUTH_PW');
    
        $validateCredentials = varify_basic_auth($password, $username);
        if($validateCredentials == true || $validateCredentials == 1){

            $checkEmailStatus =  $this->users->socialExistsCheck((string)$request->input('email'), (string)$request->input('signup_source'));

            if($checkEmailStatus['status'] == true){

                $response_array = [

                    'status' =>  'Email Already Exists Please Try With Another Email',
                    'type'   =>  400
                ];

                return response()->json($response_array, 404);
            }else{


                $checkingPhone =  $this->users->isPhoneAlreadyExists($request->input('phone_number'));

                if($checkingPhone == true || $checkingPhone == 1  ){

                    $response_array = [

                        'status' =>  'Phone Number is Already Exists',
                        'type'   =>  400
                    ];
    
                    return response()->json($response_array, 404);
                }else{

                    $signupData = [

                        'first_name'     =>  $request->input('first_name'),
                        'last_name'      =>  $request->input('last_name'),
                        'email'          =>  strtolower(trim($request->input('email'))),
                        'phone_number'   =>  $request->input('phone_number'),
                        'password'       =>  md5($request->input('password')),
                        'user_role'      =>  2,
                        'created_date'   =>  new UTCDateTime(new \DateTime())

                    ];

                    $checking = $this->users->register($signupData);
                    $signupData['_id'] = (string)$checking['id'];

                    $response_array = [
                        
                        'data'   =>  $signupData,
                        'status' =>  'Your Account is Successfully Created',
                        'type'   =>  200,
                        'token'  => '',

                    ];

                    return response()->json($response_array, 200);
                }
            }

        }else{
            

            $response_array = [ 
                'status' =>  'Authorization Failed!!',
                'type'   =>  400
            ];
            return response()->json($response_array, 400);

        }
    }//end signup 

}//end controller

