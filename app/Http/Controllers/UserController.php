<?php

namespace App\Http\Controllers;

use App\Mail\SendMail;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
class UserController extends Controller
{
    //userlist
    public function userlist(Request $request){
        
        $pageSize = $request->input('page_size', 10);
        session(['pageSize'=>$pageSize]);
        $created_user=User::find(auth()->user()->created_user_id);
        $all_users=User::select('id', 'name')->get()->whereNull('deleted_at');

        $created_all_user_id=User::select('created_user_id')->whereNull('deleted_at')->get();

            if (auth()->user()->type == 0) {
                // Get paginated users, including the 'created_user_id'
                $users = User::whereNull('deleted_at')->paginate($pageSize);
                
                // Fetch the created users' names using a single query
                $createdUserIds = $users->pluck('created_user_id')->filter()->unique()->toArray();
                
                $createdUsers = User::whereIn('id', $createdUserIds)
                                    ->whereNull('deleted_at')
                                    ->pluck('name', 'id')
                                    ->toArray();
            
                // Map names to the users' created_user_ids
                $names = [];
                foreach ($users as $user) {
                    $names[$user->id] = $createdUsers[$user->created_user_id] ?? 'Unknown';
                }
                
            return view('user.index', compact('users', 'names','pageSize'));
            }
       
        else{
    
            $users = User::whereNull('deleted_at')
            ->where('created_user_id', auth()->user()->id)
            ->with('createdBy')
            ->paginate($pageSize);

                // Initialize names array
                $names = [];

                // Collect names of creators
                foreach ($users as $user) {
                    $names[$user->id] = $user->createdBy ? $user->createdBy->name : 'Unknown';
                }

                return view('user.index', compact('users', 'names','pageSize'));
                        }
                        //dd($names);
                    
    
    } 
    //user register ui
    public function register(){
        return view('user.register');
    }
    //register validate and go confrim page
    public function registration(Request $request){
       // dd($request->profile);
        $request->validate([
            'name'=>'required',
            'email'=>'required|email',
            'password'=>'required|min:6',
            'confirmpass'=>'required|same:password',
            'profile'=>'required|image|mimes:jpeg,jpg,png,svg|max:2048'
        ],
        [
            'name.required'=>'Name can\'t blank.',
            'email.required'=>'Email can\'t blank.',
            'profile.required'=>'Profile can\'t blank.'
        ]);
        
        session(['type'=>$request->input('type')]);//get type admin or user

        $users=$request;
        $imageName=time().'.'.$request->profile->extension();
        $success=$request->profile->move(public_path('uploads'),$imageName);
        $imagePath = 'uploads/' . $imageName;
        session(['image'=>$imagePath]);
        $existingemail = User::withTrashed()
        ->where('email',$request->email)->first();
            if($existingemail){
                 if($existingemail->deleted_at){
                    return view('user.confirm_register',compact('users','imagePath'));
                }
                else{
                    return redirect()->back()->with(['error'=>'The email has already exist.']);
                }
            }
                    
            else{
               
                    return view('user.confirm_register',compact('users','imagePath'));
            }

    }
    //register save to database
    public function saveregister(Request $request){
        $request->validate([
            'name'=>'required',
            'email'=>'required|email',
            'password'=>'required|min:6',
            'confirmpass'=>'required|same:password',           
        ],
        [
            'name.required'=>'Name can\'t blank.',
            'email.required'=>'Email can\'t blank.',          
        ]);
      $image_path = session('image');
      $type =session('type');
      if($type == 'user'){
        $type_value = 1;
      }
      else{
        $type_value =0;
      }
     
        $existingemail = User::withTrashed()
        ->where('email',$request->email)->first();
        
            if($existingemail){
                
                 if($existingemail->deleted_at){

                    $existingemail->restore();

                    $existingemail->update([
                    'name'=>$request->name,
                    'email'=>$request->email,
                    'password'=>$request->password,
                    'profile'=>$image_path,
                    'phone'=>$request->phone,
                    'address'=>$request->address,
                    'dob'=>$request->dob,
                    'created_user_id'=>auth()->user()->id,
                    'updated_user_id'=>auth()->user()->id,
                    'created_at'=>Carbon::now(),
                    'updated_at'=>Carbon::now()

                   ]);
                   
                   Session::flash('register','Register Successfully');
                   return redirect()->back()->with(['register'=>'Register Successfully.']);
                   
                }
                else{
                    return redirect()->back()->with(['error'=>'The email has already exist.']);
                }
            }
                    
            else{
                if(auth()->user()->type == 0){
                    User::create([
                        'name'=>$request->name,
                        'email'=>$request->email,
                        'password'=>Hash::make($request->password),
                        'profile'=>$image_path,
                        'type'=>$type_value,                      
                        'phone'=>$request->phone,
                        'address'=>$request->address,
                        'dob'=>$request->dob,
                        'created_user_id'=>auth()->user()->id,
                        'updated_user_id'=>auth()->user()->id,
                        'created_at'=>Carbon::now(),
                        'updated_at'=>Carbon::now()
                    ]);
                    Session::flash('register','Register Successfully.');
                        return view('user.register');
                }
                else{
                    User::create([
                        'name'=>$request->name,
                        'email'=>$request->email,
                        'password'=>Hash::make($request->password),
                        'profile'=>$image_path,
                        'phone'=>$request->phone,
                        'address'=>$request->address,
                        'dob'=>$request->dob,
                        'created_user_id'=>auth()->user()->id,
                        'updated_user_id'=>auth()->user()->id,
                        'created_at'=>Carbon::now(),
                        'updated_at'=>Carbon::now()
    
                       ]);
                       Session::flash('register','Register Successfully.');
                        return view('user.register');
                }
                
            }

    }
    //show profile info
    public function profile($id){
        $user_data=User::find($id);
        return view('user.show_profile',compact('user_data'));
    }
    //show edit profile ui
    public function editprofile($id){
       $user=User::find($id);
        return view('user.edit_profile',compact('user'));
    }
    //update profile validation and store database
    public function update_profile(Request $request,$id){
        $request->validate([
            'name'=>'required',
            'email'=>'required' ,
                  
        ],[
            'name.required'=>'Name can\'t blank.',
            'email.reqied'=>'Email can\'t blank.'
        ]);
       //check type admin
        if(auth()->user()->type == 0){
            $type=$request->input('type');
           
            if($type == 'user'){
                $type_value =1;
            }
            else{
                $type_value=0;

            }
            if($request->new_profile){
                $imageName=time().'.'.$request->new_profile->extension();
                $success=$request->new_profile->move(public_path('uploads'),$imageName);
                $imagePath = 'uploads/' . $imageName;
                User::where('id',$id)->update([
                    'name'=>$request->name,
                    'email'=>$request->email,
                    'phone'=>$request->phone,
                    'dob'=>$request->dob,
                    'address'=>$request->address,
                    'type'=>$type_value,
                    'profile'=>$imagePath,
                    'updated_at'=>Carbon::now()
                ]);
            }
            else{
                User::where('id',$id)->update([
                    'name'=>$request->name,
                    'email'=>$request->email,
                    'phone'=>$request->phone,
                    'dob'=>$request->dob,
                    'address'=>$request->address,
                    'type'=>$type_value,
                    'updated_at'=>Carbon::now()
                ]);
            }
            Session::flash('profileedited',"Edit Profile Successfully");
            return redirect()->route('profile',['id' => $id]);
        }
        else{
            if($request->new_profile){
                $imageName=time().'.'.$request->new_profile->extension();
                $success=$request->new_profile->move(public_path('uploads'),$imageName);
                $imagePath = 'uploads/' . $imageName;
                User::where('id',$id)->update([
                    'name'=>$request->name,
                    'email'=>$request->email,
                    'phone'=>$request->phone,
                    'dob'=>$request->dob,
                    'address'=>$request->address,
                    'profile'=>$imagePath,
                    'updated_at'=>Carbon::now()
                ]);
            }
            else{
                User::where('id',$id)->update([
                    'name'=>$request->name,
                    'email'=>$request->email,
                    'phone'=>$request->phone,
                    'dob'=>$request->dob,
                    'address'=>$request->address,
                    'updated_at'=>Carbon::now()
                ]);
            }
            Session::flash('profileedited',"Edit Profile Successfully");
            return redirect()->route('profile',['id' => $id]);
        }
       
        
    }
    //forget password ui
    public function showforgetpassword(){
       
        return view('user.forget_password');
    }
    //reset pass and store token to database
    public function submitforgetpassword(Request $request){
        $request->validate([
            'email'=>'required|email'
        ]);
        //dd($request->email);
        $user=User::where('email',$request->email)->first();
        $userName=User::select('name')->where('email',$request->email)->first()->name;
        //dd($userName);
        if($user){
            $userEmail=$request->email;
            $token = Str::random(64);
            DB::table('password_reset_tokens')->insert([
                'email' => $request->email, 
                'token' => $token, 
                'created_at' => Carbon::now()
            ]);
           // dd($userEmail);
          
           $data = [
           'token'=>$token,
           'email' => $request->email,
            'name'=>$userName,
           ];
         Mail::to($userEmail)->send(new SendMail($data));
          // dd($bladeurl);
            //return view('user.reset_password');
            return redirect()->route('login')->with(['reset_pass'=>'Email sent with password reset instructions.']);
        }
        else{
            return redirect()->route('forgetpassword')->with(['error'=>'Email does not exist.']);
        }
        
    }
    //reset password ui
    public function reset_password(Request $request,$token){
        $user_token=$token;
        $email = $request->query('email');
        //dd($email);
        return view('user.reset_password',compact('user_token','email'));

        //TDO::get id ,check validation,and save to database
        //
    }
    //reset password and store new password
    public function submit_reset_password(Request $request){
       // dd($request->email);
        $request->validate([
            'password'=>'required',
            'password_confirm'=>'required'
        ]);
        $check_token =DB::table('password_reset_tokens')
                    ->where([
                        'email'=>$request->email,
                        'token'=>$request->token,
                    ])->first();
        if(!$check_token){
            return redirect()->route('login')->with('error','Invalid Token');
        }
        User::where('email',$request->email)
            ->update([
                'password'=>Hash::make($request->password),
                'updated_at'=>Carbon::now()
            ]);
        DB::table('password_reset_tokens')->where('email',$request->email)->delete();
        return redirect('/login')->with('message', 'Your password has been changed!');
    }
    //change password ui
    public function change_password(){
        return view('user.change_password');
    }
    //change password and new password is store database
    public function changed_password(Request $request){
        $request->validate([
            'cur_pass'=>'required',
            'new_pass'=>'required|min:6',
            'con_new_pass'=>'required|same:new_pass'
        ],
        [
           'cur_pass.required'=>'Current Password can\'t be blank.' ,
           'new_pass.required'=>'New Password can\'t be blank.',
           'con_new_pass.required'=>'Confirm New Password can\'t be blank.',
           'new_pass.min' => 'New Password must be at least 6 characters.',
            'con_new_pass.same' => 'New Password and Confirm New Password must match.',
        ]);
       $current_pass=$request->cur_pass;
       $new_password=$request->new_pass;
       $confirm_pass=$request->con_new_pass;

       $user = User::find(auth()->user()->id);
       $hashedPassword = $user->password;
       if (Hash::check($current_pass, $hashedPassword)) {
            if ($new_password === $confirm_pass) {

                $user->password = Hash::make($new_password);
                $user->save();

                return redirect()->route('user')->with('success', 'Password updated successfully.');
            } else {
                return redirect()->back()->withErrors(['error' => 'New passwords do not match.']);
            }
        } else {
        
            return redirect()->back()->withErrors(['error' => 'Current password is incorrect.']);
        }

  

    }


    
}
