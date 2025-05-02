<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Pindata;
use App\Models\Api;
use App\Models\Circle;
use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use App\Models\SessionMaster;
use App\Models\BranchMaster;
use Hash;
use DB;
class UserController extends Controller
{

    public function index()
    {
        $data['title'] = "Login";
        $data['sessions'] = SessionMaster::where('status', 'Active')->get();
        return view('auth.login1')->with($data);
    }

    public function login(Request $post)
    {
        // dd($post);
        $user = User::where('username', $post->mobile)->first();
        $sesstion = SessionMaster::where('id', $post->sessionId)->first();
        $branch = BranchMaster::first();
        if (!$sesstion) {
            return response()->json(['statuscode' => "ERR", 'message' => "Please select valid session"]);
        }
        $sessionDate = date('Y', strtotime($sesstion->startDate)) . "-" . date('Y', strtotime($sesstion->endDate));

        if (!$user) {
            return response()->json(['statuscode' => "ERR", 'message' => "Your aren't registred with us."]);
        }

        session(['sessionId' =>  $sesstion->id]);
        session(['sessionyear' =>  $sessionDate]);
        session(['sessionStart' =>  $sesstion->startDate]);
        session(['sessionEnd' =>  $sesstion->endDate]);
        session(['currentdate' =>  date('d-m-Y')]);
        session(['Branchname' =>  $branch->name ?? ""]);
        $log['ipAddress'] = $post->ip();
        $log['userAgent'] = $post->server('HTTP_USER_AGENT');
        $log['userId'] = $user->id;
        $log['geoLocation'] = '-3.831990' / '-38.552900';
        $log['url'] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $log['parameters'] = 'portal';
        \DB::table('user_activity_logs')->insert($log);

        $otprequired = "No";

        if (!\Auth::validate(['username' => $post->mobile, 'password' => $post->password])) {
            return response()->json(['status' => 'Username or password is incorrect'], 400);
        }

        if (!\Auth::validate(['username' => $post->mobile, 'password' => $post->password, 'status' => "active"])) {
            return response()->json(['status' => 'Your account currently de-activated, please contact administrator'], 400);
        }
        if ($otprequired == "yes") {
            if ($post->has('otp') && $post->otp == "resend") {
                if ($user->otpresend < 3) {
                    $otpmailid = \App\Models\PortalSetting::where('code', 'otpsendmailid')->first();
                    $otpmailname = \App\Models\PortalSetting::where('code', 'otpsendmailname')->first();
                    $otp = rand(111111, 999999);
                    $regards = "";
                    $msg = "Dear partner, your login otp is " . $otp . " Don't share with anyone Regards " . $regards . " \r\nLCO FINTECH(OPC) PRIVATE LIMITED";
                    $send = \Myhelper::sms($post->mobile, $msg);
                    $mail = \Myhelper::mail('mail.otp', ["otp" => $otp, "name" => $user->name, "subhead" => "Login OTP"], $user->email, $user->name, $otpmailid->value, $otpmailname->value, "Login Otp");
                    if ($send == 'success' || $mail == "success") {
                        User::where('mobile', $post->mobile)->update(['otpverify' => $otp, 'otpresend' => $user->otpresend + 1]);
                        return response()->json(['status' => 'otpsent'], 200);
                    } else {
                        return response()->json(['status' => 'Please contact your service provider provider'], 400);
                    }
                } else {
                    return response()->json(['status' => 'Otp resend limit exceed, please contact your service provider'], 400);
                }
            }

            if ($user->otpverify == "yes" || !$post->has('otp')) {
                $otp = rand(111111, 999999);
                $regards = "";
                $msg = "Dear partner, your login otp is " . $otp . " Don't share with anyone Regards " . $regards . " \r\nLCO FINTECH(OPC) PRIVATE LIMITED";

                $send = \Myhelper::sms($post->mobile, $msg);
                $otpmailid = \App\Models\PortalSetting::where('code', 'otpsendmailid')->first();
                $otpmailname = \App\Models\PortalSetting::where('code', 'otpsendmailname')->first();
                $mail = \Myhelper::mail('mail.otp', ["otp" => $otp, "name" => $user->name, "subhead" => "Login OTP"], $user->email, $user->name, $otpmailid->value, $otpmailname->value, "Login Otp");
                if ($send == 'success' || $mail == "success") {
                    User::where('mobile', $post->mobile)->update(['otpverify' => $otp]);
                    return response()->json(['status' => 'otpsent'], 200);
                } else {
                    return response()->json(['status' => 'Please contact your service provider provider'], 400);
                }
            } else {
                if (!$post->has('otp')) {
                    return response()->json(['status' => 'preotp'], 200);
                }
            }

            if (\Auth::attempt(['mobile' => $post->mobile, 'password' => $post->password, 'otpverify' => $post->otp, 'status' => "active"])) {
                return response()->json(['status' => 'Login'], 200);
            } else {
                return response()->json(['status' => 'Please provide correct otp'], 400);
            }
        } else {
            if (\Auth::attempt(['username' => $post->mobile, 'password' => $post->password, 'status' => "active"])) {
                return response()->json(['status' => 'Login'], 200);
            } else {
                return response()->json(['status' => 'Something went wrong, please contact administrator'], 400);
            }
        }
    }

    public function logout(Request $request)
    {
        \Auth::guard()->logout();
        $request->session()->invalidate();
        return redirect('/');
    }

    public function passwordReset(Request $post)
    {
        $rules = array(
            'type' => 'required',
            'mobile' => 'required|numeric',
        );

        $validate = \Myhelper::FormValidator($rules, $post);
        if ($validate != "no") {
            return $validate;
        }


        if ($post->type == "request") {
            $user = User::where('mobile', $post->mobile)->first();
            if ($user) {
                $company = \App\Models\Company::where('id', $user->company_id)->first();
                $otp = '123456'; //rand(11111111, 99999999);
                if ($company->senderid) {
                    $regards = "";
                    // $content = "Dear partner, your password reset token is " . $otp . " Don't share with anyone Regards " . $regards . ". LCO FINTECH(OPC) PRIVATE LIMITED";
                    // $sms = \Myhelper::sms($post->mobile, $content);

                } else {
                    $sms = true;
                }
                $otpmailid = \App\Models\PortalSetting::where('code', 'otpsendmailid')->first();
                $otpmailname = \App\Models\PortalSetting::where('code', 'otpsendmailname')->first();
                try {
                    $mail = \Myhelper::mail('mail.password', ["token" => $otp, "name" => $user->name, "subhead" => "Reset Password"], $user->email, $user->name, $otpmailid->value, $otpmailname->value, "Reset Password");
                } catch (\Exception $e) {
                    $mail = true;


                    // return response()->json(['status' => 'ERR', 'message' => "Something went wrong1"], 400);
                }
                //dd($sms);
                if ($sms || $mail) {
                    User::where('mobile', $post->mobile)->update(['remember_token' => $otp]);
                    return response()->json(['status' => 'TXN', 'message' => "Password reset token sent successfully"], 200);
                } else {
                    return response()->json(['status' => 'ERR', 'message' => "Something went wrong2"], 400);
                }
            } else {
                return response()->json(['status' => 'ERR', 'message' => "You aren't registered with us"], 400);
            }
        } else {
            $user = User::where('mobile', $post->mobile)->where('remember_token', $post->token)->get();
            if ($user->count() == 1) {
                $update = User::where('mobile', $post->mobile)->update(['password' => bcrypt($post->password), 'passwordold' => $post->password]);
                if ($update) {
                    return response()->json(['status' => "TXN", 'message' => "Password reset successfully"], 200);
                } else {
                    return response()->json(['status' => 'ERR', 'message' => "Something went wrong"], 400);
                }
            } else {
                return response()->json(['status' => 'ERR', 'message' => "Please enter valid token"], 400);
            }
        }
    }



    public function usersList($type)
    {
        $role = Role::where('slug', $type)->first();
        if (!$role) {
            abort(404);
        }
        $data['id'] = 0;
        $data['role'] = $role;
        $data['type'] = $type;
        return view('user.index')->with($data);
    }

    public function userStore(Request $post)
    {
        $rules = array(
            'name' => 'required',
            'email' => 'required|string|email|max:255|unique:users',
            'mobile' => 'required|unique:users',
        );

        $validator = \Validator::make($post->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $insert = $post->all();
        $insert['password'] = \Hash::make(rand(111111, 999999));
        $action = User::updateOrCreate(['id' => $post->id], $insert);

        if ($action) {
            return response()->json(['status' => "success"], 200);
        } else {
            return response()->json(['status' => "Task Failed, please try again"], 200);
        }
    }

    public function changesession($id){

         $sesstion = SessionMaster::where('id', $id)->first();
         if($sesstion){
                $sessionDate = date('Y', strtotime($sesstion->startDate)) . "-" . date('Y', strtotime($sesstion->endDate));
                session(['sessionId' =>  $sesstion->id]);
                session(['sessionyear' =>  $sessionDate]);
                session(['sessionStart' =>  $sesstion->startDate]);
                session(['sessionEnd' =>  $sesstion->endDate]);
                return response()->json(['status' => "success"], 200);
         }else{
                return response()->json(['status' => "Task Failed, please try again"], 200);
         }

        dd($id) ;
    }

}
