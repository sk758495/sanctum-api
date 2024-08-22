<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['posts'] = Post::all();

        return response()->json([
            'status'=> true,
            'data' => $data,
            'message'=>'All post data',
        ],200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validateUser = Validator::make(
            $request->all(),
            [
                'title'=>'required',
                'description'=>'required',
                'image'=>'required|mimes:png,jpg,jpeg,gif',
            ]
            );

            if($validateUser -> fails()){
                return response()->json([
                    'status'=> false,
                    'message'=>'validation Error',
                    'errors'=>$validateUser->errors()->all()
                ],401);
            }

            $img = $request->image;
            $ext =$img->getClientOriginalExtension();
            $imageName =Time().'.'.$ext;
            $img ->move(public_path().'/uploads',$imageName);

            $post = Post::create([
                'title'=>$request->title,
                'description'=>$request->description,
                'image'=>$imageName,
            ]);

            return response()->json([
                'status'=> true,
                'message'=>'post Method Created Succesffully',
                'post'=>$post,
            ],200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data['post'] = Post::select(
            'id',
            'title',
            'description',
            'image'
        )->where(['id'=> $id])->get();

        return response()->json([
            'status'=> true,
            'message'=>'Your single post',
            'data'=>$data,
        ],200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validateUser = Validator::make(
            $request->all(),
            [
                'title'=>'required',
                'description'=>'required',
                'image'=>'required|mimes:png,jpg,jpeg,gif',
            ]
            );

            if($validateUser -> fails()){
                return response()->json([
                    'status'=> false,
                    'message'=>'Update validation Error',
                    'errors'=>$validateUser->errors()->all()
                ],401);
            }

            $post = Post::select('id','image')->get();

            if($request->image != ''){
                $path = public_path().'/uploads';

                if($post->image != '' && $post->image != null){
                    $old_file = $path. $post->image;
                    if(file_exists($old_file)){
                        unlink($old_file);
                    }
                }
                $img = $request->image;
                $ext =$img->getClientOriginalExtension();
                $imageName =Time().'.'.$ext;
                $img ->move(public_path().'/uploads',$imageName);
            }    else{
                $imageName = $post-> image;
            }                  

            $post = Post::where(['id'=> $id])->Update([
                'title'=>$request->title,
                'description'=>$request->description,
                'image'=>$imageName,
            ]);

            return response()->json([
                'status'=> true,
                'message'=>'post Updated Succesffully',
                'post'=>$post,
            ],200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

        $imagePath = Post::select('image')->where('id',$id)->get();
        $filePath = public_path().'/uploads'. $imagePath[0]['image'];

        unlink($filePath);



        $post = Post::where('id',$id)->delete();
        
        return response()->json([
            'status'=> true,
            'message'=>'post hash been deleted Succesffully',
            'post'=>$post,
        ],200);
    }
}
