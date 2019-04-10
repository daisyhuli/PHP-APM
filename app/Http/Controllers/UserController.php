<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /*register 存储新用户
    * @param Request $request
    * return Response
    */
    public function getInsert(Request $request){

        
        $email = $request->input('email');
        $password = $request->input('password');
        $name = $request->input('nickname');
        $num = DB::select("select id from users where email='$email' limit 1");
 
        $raw_fail = array(
                          ['code' => "0002", 'msg' => 'register failure'],
                          ['code' => "0002", 'msg' => 'The email has existed']);
    
        if($num){
            return json_encode($raw_fail[1]);
        }else{
            $bool = DB::table('users') -> insert(
                        ['name' =>$name, 'password' => $password, 'email' => $email ]);
            if($bool){
                Session::put(['token' => $email]);
                Cookie::queue('token', $email);
                return json_encode(array('code' => "0000", 'msg' => 'succeed', 'token' => $email));
            }else{
                return json_encode($raw_fail[0]);
            }
        }
       
 
    }

    // login 查找用户输入是否一致
    public function checkStatus(Request $request) {

        $email = $request->input('userName');
        $password = $request->input('password');
        
        // $input = $request->all();
        // return dd($input);

        $user = User::where('email', $email)->first();
    

        if(!$user) {
             // Do stuff if it doesn't exist.
                return json_encode(array('code' => "0002", 'msg' => 'user doesn\'t exist'));

        }else{
            if($user->password == $password) {
                Session::put(['token' => $email]);
                Cookie::queue('token', $email);
                return json_encode( 
                    array('code' => "0000", 'msg' => 'login sucessfully','token' => $user->email)
                );
            }else{
                return json_encode( 
                    array('code' => "0003", 'msg' => 'incorrect password')
                );
            }
        }
      
      

    }

    // home show different view according to loginStatus
    public function viewStatus(Request $request) {
        
        $cookie = $request->cookie('token');
   
        if(Session::has('token') && Session::get('token') == $cookie){
           
           //有值数组, 没有空数组
            $user = DB::select('select * from users where email = ?', [$cookie]);

            if($user != null) {
                $raw_success = array('code' => "0000", 'msg' => 'succeed','data' => $user);
                return json_encode( $raw_success);
            }else{
                $raw_fail = array('code' => "0002", 'msg' => 'please login','data' => $user);
                return json_encode($raw_fail);
            }
        }else{
            $raw_fail = array('code' => "0002", 'msg' => 'please login','data' => []);
            return json_encode($raw_fail);
        }
      
    }

    //logout
    public function logOut(Request $request) {
        $value = $request->session()->pull('token');
        $raw_fail = array('code' => "0002", 'msg' => 'please login','data' => []);
        return json_encode($raw_fail);
        
    }

}
