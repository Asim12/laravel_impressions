<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use MongoDB\BSON\UTCDateTime;
use MongoDB\BSON\DateTime;
use App\Models\Users;

class Login extends Controller
{
    public function __construct(){

        $this->users = new users();
    }

   

}