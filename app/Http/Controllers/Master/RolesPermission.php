<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserRoleMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RolesPermission extends Controller
{
    public function usersindex(){
        $roles = UserRoleMaster::orderBy('name','ASC')->get();
        $data['roles'] = $roles;
        return view('master.roles',$data);
    }

    public function userinsert(Request $post){
        $post->validate([
            'name' => 'required|unique:user_role_masters,name'
        ]);

        $roles = new UserRoleMaster();
        $roles->name = $post->name;
        $roles->save();
        return redirect()->route('usersindex')->with('success','Record Insert Successfully');
    }

    public function roleedit(Request $post,$id){
        $roles = UserRoleMaster::orderBy('name','ASC')->get();
        $userId = UserRoleMaster::where('id',$id)->first();
        $data['roles'] = $roles;
        if(!empty($userId)){
            $data['userid'] = $userId;
            return view('master.roles',$data);
        }else{
            $data['roles'] = $roles;
            return view('master.roles',$data);
        }
    }

    public function roleupdate(Request $post,$userid){
        $post->validate([
            'name' => 'required|unique:user_role_masters,name',
        ]);

        $roles = UserRoleMaster::where('id',$userid)->first();
        $roles->name = $post->name;
        $roles->save();
        return redirect()->route('usersindex')->with('success','Record Update Successfully');
    }

    public function users(){
        $roles = UserRoleMaster::orderBy('name','ASC')->get();
        $usersss = DB::table('users')
            ->select('users.*','user_role_masters.id as role_id','user_role_masters.name as rolename')
            ->leftJoin('user_role_masters','user_role_masters.id','=','users.role')
            ->orderBy('users.id','ASC')
            ->get();
        $data['usersss'] = $usersss;
        $data['roles'] = $roles;
        return view('master.assign',$data);
    }

    public function userregister(Request $post){
        $post->validate([
            'usertype' => 'required',
            'name' => 'required',
            // 'email' => 'required|email|unique:users,email',
            'mobile' => 'required|digits:10',
            'user_name' => 'required|string|regex:/^[a-zA-Z\s]+$/|unique:users,username',
            'password' => 'required'
        ]);

        $users = new User();
        $users->name = $post->name;
        $users->mobile  = $post->mobile;
        $users->email  = $post->email;
        $users->agent_id = $post->agents;
        $users->username = $post->user_name;
        $users->password = Hash::make($post->password);
        $users->role = $post->usertype;
        $users->save();
        return redirect()->route('usersss')->with('success','Record Insert Successfully');
    }

    public function useredits(Request $post,$id){
        $roles = UserRoleMaster::orderBy('name','ASC')->get();
        $userId = DB::table('users')
            ->where('id',$id)
            ->first();
        $usersss = DB::table('users')
        ->select('users.*','user_role_masters.id as role_id','user_role_masters.name as rolename')
        ->leftJoin('user_role_masters','user_role_masters.id','=','users.role')
        ->orderBy('users.id','ASC')
        ->get();

        $data['usersss'] = $usersss;
        if(!empty($userId)){
            $data['userid'] = $userId;
            $data['roles'] = $roles;
          return view('master.assign',$data);
        }else{
            $data['roles'] = $roles;
          return view('master.assign',$data);
        }
    }

    public function usersupdate(Request $post,$userid){
        $post->validate([
            'usertype' => 'required',
            'name' => 'required',
            // 'email' => 'required|email|unique:users,email,'.$userid.',id',
            'mobile' => 'required|digits:10',
            'user_name' => 'required|string|regex:/^[a-zA-Z\s]+$/|unique:users,username,'.$userid.',id',
        ]);

        $password = DB::table('users')->where('id',$post->userid)->first();

        $users = User::where('id',$post->userid)->first();
        $users->name = $post->name;
        $users->mobile  = $post->mobile;
        $users->email  = $post->email;
        $users->agent_id = $post->agents;
        $users->username = $post->user_name;
        $users->password = Hash::make($post->password) ? Hash::make($post->password) : $password->password;
        $users->role = $post->usertype;
        $users->save();
        return redirect()->route('usersss')->with('success','Record Update Successfully');
    }

    public function getallagents(Request $post){
        $usertype = $post->userType;
        if(!empty($usertype)){
            $allAgents = DB::table('agent_masters')->where('status','Active')->orderBy('id','ASC')->get();

            if($allAgents){
                return response()->json(['status' => 'success','allagents' => $allAgents]);
            }else{
                return response()->json(['status' => 'Fail','messages' => 'Record Not Found']);
            }
        }

    }

}
