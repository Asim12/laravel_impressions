<?php

namespace App\Models;
use Jenssegers\Mongodb\Eloquent\Model;
use MongoDB\BSON\ObjectID;
use MongoDB\BSON\UTCDateTime;
// use Illuminate\Support\Facades\DB;
use MongoDB\BSON\DateTime;
use MongoDB;


class users extends Model
{
    protected $connection = 'mongodb';
    public function getUserData($email){

        $countUser = users::find( ['email' => $email]);
        return $countUser;
    }//end

    public function isAlreadyExists($email){

        $countUser = users::raw()->findOne( ['email' => $email]);

        if(!empty($countUser)){

            if(count($countUser) > 0){

                return true;
            }else{
    
                return false;
            }
        }else{

            return false;
        }



       
    }//end


    public function register($data){

        $countUser = users::raw()->insertOne($data);

        if($countUser->getInsertedId() ){

            return ['status' =>  true, 'id' => $countUser->getInsertedId()];
        }else{

            return ['status' =>  false] ;
        }
    }//end


    public function updatePassword($email, $password){

        $countUser = users::raw()->updateOne(['email' => $email], ['$set' => ['password' => $password]]);
    }//end 


    public function socialExistsCheck($email, $source){
    
        $countUser = users::raw()->findOne( ['email' => $email]);
        if(count($countUser) > 0 ){

            if(isset($countUser[0]['signup_source']) && $countUser[0]['signup_source']  ==  $source){

                return ['status' => false, 'id' => (string)$countUser[0]['_id'] ];
            }else{

                return ['status' => true, 'id' => ''];
            }
        }else{

            return ['status' => false, 'id' => ''];
        }
        
    }


    public function isPhoneAlreadyExists($phone){

        $countUser = users::raw()->findOne( ['phone_number' => $phone]);

        if(empty($countUser)){

            return false;
        }else{


            if(count($countUser) > 0){

                return true;
            }else{
    
                return false;
            }
        }
       
    }

}