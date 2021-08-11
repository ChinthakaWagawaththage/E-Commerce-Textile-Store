<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Session;
use App\Admin;
use Hash;
use Image;

class AdminController extends Controller
{
    public function dashboard(){
        session::put('page','dashboard');
        return view('admin.admin_dashboard');
    }

    public function login(Request $request){

        if($request->isMethod('post')){
            $data = $request->all();
            //echo "<pre>"; print_r($data); die;

            $validatedData = $request->validate([
                'email' => 'required|email|max:100',
                'password' => 'required',
            ]);

            if(Auth::guard('admin')->attempt(['email'=>$data['email'],'password'=>$data['password']])){
                return redirect('admin/dashboard');
            }
            else{
                Session::flash('error_message', 'Invalid Credentials!!!');
                return redirect()->back();
            }
        }

        return view('admin.admin_login');
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect('/admin');
    }

    public function settings()
    {
        session::put('page','settings');
        //Auth::guard('admin')->user()->id;
        return view('admin.admin_settings');
    }

    public function checkCurrentPassword(Request $request){
        $data = $request->all();
        //echo "<pre>"; print_r($data);
        //echo "<pre>"; print_r(Auth::guard('admin')->user()->password); die;
        if(Hash::check($data['current_pwd'],Auth::guard( 'admin' )->user()->password))
        {
            echo "true";
        }
        else
        {
            echo "false";
        }
    }

    public function updateCurrentPassword(Request $request){
        if($request->isMethod('post'))
        {
            $data = $request->all();

            if(Hash::check($data['current_pwd'],Auth::guard( 'admin' )->user()->password))
            {
                if($data['new_pwd']==$data['confirm_pwd'])
                {
                    Admin::where('id',Auth::guard('admin')->user()->id)->update(['password'=>bcrypt($data['new_pwd'])]);
                    Session::flash('success_message','Password Updated Successfully');
                    return redirect()->back();
                }
                else
                {
                    Session::flash('error_message','New Password & Confirmed Password are not Matching!');
                    return redirect()->back();
                }
            }
        }
            else
            {
                Session::flash('error_message','Your Password is Incorrect!');
                return redirect()->back();
            }
        }

    public function updateAdminDetails(Request $request){

        session::put('page','update-admin-details');

        if($request->isMethod('post'))
        {
            $data = $request->all();
            //echo "<pre>"; print_r($data); die;
            $rules = [
                'admin_name' => 'required|regex:/^[\pL\s\-]+$/u',
                'admin_mobile' => 'required|numeric',
                'admin_image' => 'image'
            ];
            $customMessages = [
                'admin_name.required' => 'Name is Required!',
                'admin_name.regex' => 'Valid Name is Required!',
                'admin_mobile.required' => 'Mobile is Required!',
                'admin_mobile.numeric' => 'Valid Mobile Number is Required',
                'admin_image.image' => 'Please Upload an Image File'
            ];
            $this->validate($request,$rules,$customMessages);

            if($request->hasFile('admin_image'))
            {
                $image_tmp = $request->file('admin_image');
                if($image_tmp->isValid())
                {
                    $extension = $image_tmp->getClientOriginalExtension();
                    $imageName = rand(111,99999).'.'.$extension;
                    $imagePath = 'images/admin_images/admin_photos/'.$imageName;
                    Image::make($image_tmp)->save($imagePath);
                }
                else if(!empty($data['current_admin_image']))
                {
                    $imageName=$data['current_admin_image'];
                }
                else
                {
                    $imageName="";
                }
            }

            Admin::where('email',Auth::guard('admin')->user()->email)
            ->update(['name'=>$data['admin_name'],'mobile'=>$data['admin_mobile'],'image'=>$imageName]);
            session::flash('success_message','Admin Details Updated Successfully!');
            return redirect()->back();
        }
        return view('admin.update_admin_details');

    }
}

