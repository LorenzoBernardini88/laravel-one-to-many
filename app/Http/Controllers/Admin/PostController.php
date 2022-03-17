<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data_post = Post::all();
        return view('admin.posts.index',compact('data_post'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Post $post)
    {
        return view('admin.posts.create', compact('post'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            "post_author" => "required|string|max:50|unique:posts",
            "title" => "required|string|max:50",
            "content" => "required|string",
            "post_date" => "required",
            
        ]);
        $form_data = $request->all();

        $slugTmp = Str::slug($form_data['title']);

        $count = 1;

        while(Post::where('slug',$slugTmp)->first()){
            $slugTmp = Str::slug($form_data['title']).'-'.$count;
            $count ++;
        }

        $form_data['slug'] = $slugTmp;

        

        
        $newpost = Post::create($form_data);

        return redirect()->route('admin.posts.show', $newpost->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        return view('admin.posts.show',compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        return view('admin.posts.edit',compact('post'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        $request->validate([
            "post_author" => "required|string|max:50",
            "title" => "required|string|max:50",
            "content" => "required|string",
            "post_date" => "required",
            
        ]);
        $form_data = $request->all();

        if($post->title == $form_data['title']){
            $slug = $post->slug;
        }else{
            $slug = Str::slug($form_data['title']);
            $count = 1;
            while(Post::where('slug',$slug)
                ->where('id','!=',$post->id)
                ->first()){
                    $slug = Str::slug($form_data['title']).'-'.$count;
                    $count ++;
                }
            }
        $form_data['slug'] = $slug; 

        

        
        $post->update($form_data);

        return redirect()->route('admin.posts.show', $post->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        $post->delete();
        return redirect()->route('admin.posts.index');
    }
}
