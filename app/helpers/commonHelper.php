<?php 

    if(!function_exists('testing')){

        if (!function_exists('varify_basic_auth')) {
            function varify_basic_auth($passwordAuth , $userName) {

                $password = md5($passwordAuth);
                $username = md5($userName);

                $password1 = md5('asim92578@gmail.com');
                $username1 = md5('impressions');

                if($password  == $password1  && $username ==  $username1){

                    return true;
                }else{

                    return false;
                }
            }
        } //end num

    }//end if function exists

    