<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Hobby;
use App\Models\User;
use App\Models\UserHobby;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $response = [
            'status' => config('constants.status.error'),
            'msg'   => config('constants.msg.error')
        ];
        try {
            $User = User::with(['category', 'Userhobby'])->orderBy('id')->get();
            $response['status']  = config('constants.status.success');
            $response['msg'] = config('constants.msg.datafetchedsuccess');
            $response['data'] = $User;
            return response()->json($response);
        } catch (\Exception $e) {
            $response['msg'] = $e;
            return response()->json($response);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $response = [
            'status' => config('constants.status.error'),
            'msg'   => config('constants.msg.error')
        ];
        try {

            $validator = Validator::make($request->all(), [
                'name' => 'required|max:191',
                'contact_no' => 'required|numeric|digits:10',
                'hobby' => 'required|array|min:1',
                'category' => 'required',
                'profile_pic' => 'max:2048|mimes:jpg,jpeg,png'
            ], [
                'numeric' => 'Phone number should contain only numbers',
                'digits' => 'Phone number must be 10 digit'
            ]);

            if ($validator->fails()) {
                $response['status']  = config('constants.status.error');
                $response['msg'] = $validator->errors()->first();;
            } else {
                    $User = new User() ;
                    $hobbyArr = $request->input('hobby');
                    $User->name = $request->input('name');
                    $User->contact_no = $request->input('contact_no');
                    $User->category_id = $request->input('category');

                    //Note File Upload
                    if($request->hasFile('profile_pic')) {
                        $file = $request->file('profile_pic');
                        $name = str_replace(' ','',trim($file->getClientOriginalName()));
                        $file->move(public_path().'/uploads/', $name);
                        $User->profile_pic= URL::to('/').'/uploads/'.str_replace(' ','',trim($file->getClientOriginalName()));
                     }

                    $User->save();

                 
                    //NOTE Edit Hobby
                    foreach ($hobbyArr as $hob_val) {
                        $hobby[] =  [
                            'user_id' => $User->id,
                            'hobby_id' => $hob_val
                        ];
                    }
                    UserHobby::insert($hobby);
                    
                    

                    $response['status']  = config('constants.status.success');
                    $response['msg'] = config('constants.msg.dataupdatesuccess');
                    $response['data'] = $User;
            }
            return response()->json($response);
        } catch (\Exception $e) {
            $response['msg'] = $e;
            return response()->json($response);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $response = [
            'status' => config('constants.status.error'),
            'msg'   => config('constants.msg.error')
        ];
        try {

            $validator = Validator::make($request->all(), [
                'name' => 'required|max:191',
                'contact_no' => 'required|numeric|digits:10',
                'hobby' => 'required|array|min:1',
                'category' => 'required',
                'profile_pic' => 'max:2048|mimes:jpg,jpeg,png'
            ], [
                'numeric' => 'Phone number should contain only numbers',
                'digits' => 'Phone number must be 10 digit'
            ]);

            if ($validator->fails()) {
                $response['status']  = config('constants.status.error');
                $response['msg'] = $validator->errors()->first();;
            } else {
                $User = User::find($id);
                if ($User) {
                    $hobbyArr = $request->input('hobby');
                    $User->name = $request->input('name');
                    $User->contact_no = $request->input('contact_no');
                    $User->category_id = $request->input('category');

                    //Note File Upload
                    if($request->hasFile('profile_pic')) {
                        $file = $request->file('profile_pic');
                        $name = str_replace(' ','',trim($file->getClientOriginalName()));
                        $file->move(public_path().'/uploads/', $name);
                        $User->profile_pic= URL::to('/').'/uploads/'.str_replace(' ','',trim($file->getClientOriginalName()));
                     }

                    $User->update();

                    //NOTE Delete Exisiting Hobby
                    UserHobby::where('user_id', $id)->delete();
                    //NOTE Edit Hobby
                    foreach ($hobbyArr as $hob_val) {
                        $hobby[] =  [
                            'user_id' => $id,
                            'hobby_id' => $hob_val
                        ];
                    }
                    UserHobby::insert($hobby);
                    
                    

                    $response['status']  = config('constants.status.success');
                    $response['msg'] = config('constants.msg.dataupdatesuccess');
                    $response['data'] = $User;
                } else {
                    $response['status']  = config('constants.status.error');
                    $response['msg'] = config('constants.msg.nouserfound');
                }
            }
            return response()->json($response);
        } catch (\Exception $e) {
            $response['msg'] = $e;
            return response()->json($response);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function bulkDestroy(Request $request)
    {
        $response = [
            'status' => config('constants.status.error'),
            'msg'   => config('constants.msg.error')
        ];
        try {
            $User = User::whereIn('id',$request->user_id)->delete();
            $response['status']  = config('constants.status.success');
            $response['msg'] = config('constants.msg.datadeletesuccess');
            $response['data'] = $User;
            return response()->json($response);
        } catch (\Exception $e) {
            $response['msg'] = $e;
            return response()->json($response);
        }
    }

    public function getCategory()
    {
        $response = [
            'status' => config('constants.status.error'),
            'msg'   => config('constants.msg.error')
        ];
        try {
            $Category = Category::orderBy('id')->get();
            $response['status']  = config('constants.status.success');
            $response['msg'] = config('constants.msg.datafetchedsuccess');
            $response['data'] = $Category;
            return response()->json($response);
        } catch (\Exception $e) {
            $response['msg'] = $e;
            return response()->json($response);
        }
    }

    public function getHobby()
    {
        $response = [
            'status' => config('constants.status.error'),
            'msg'   => config('constants.msg.error')
        ];
        try {
            $Category = Hobby::orderBy('id')->get();
            $response['status']  = config('constants.status.success');
            $response['msg'] = config('constants.msg.datafetchedsuccess');
            $response['data'] = $Category;
            return response()->json($response);
        } catch (\Exception $e) {
            $response['msg'] = $e;
            return response()->json($response);
        }
    }
}
