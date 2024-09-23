<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use App\Models\Brand;

class AdminController extends Controller
{
    public function index()
    {
        return view("admin.index");
    }

    public function brands()
    {
        $brands = Brand::orderBy('id','DESC')->paginate(10);
        return view("admin.brands",compact('brands'));
    }

    public function add_brand()
    {
         return view("admin.brand-add");
    }

    public function brand_store(Request $request)
    {
        $request->validate([
             'name' => 'required',
             'slug' => 'required|unique:brands,slug',
             'image' => 'mimes:png,jpg,jpeg|max:2048'
        ]);

        $brand = new Brand();
        $brand->name = $request->name;
        $brand->slug = Str::slug($request->name);
        $image = $request->file('image');
        $file_extention = $request->file('image')->extension();
        $file_name = Carbon::now()->timestamp.'.'.$file_extention;
        $this->GenerateBrandThumbnailImage($image,$file_name);
        $brand->image = $file_name;
        $brand->save();
        return redirect()->route('admin.brands')->with('status','Record has been added successfully !');
   }

    public function brand_edit($id)
    {
        $brand = Brand::find($id);
        return view('admin.brand-edit',compact('brand'));
    }
    public function update_brand(Request $request)
{
    $request->validate([
        'name' => 'required',
        'slug' => 'required|unique:brands,slug,'.$request->id,
        'image' => 'mimes:png,jpg,jpeg|max:2048'
    ]);
    $brand = Brand::find($request->id);
    $brand->name = $request->name;
    $brand->slug = $request->slug;
    if($request->hasFile('image'))
    {
        if (File::exists(public_path('uploads/brands').'/'.$brand->image)) {
            File::delete(public_path('uploads/brands').'/'.$brand->image);
        }
        $image = $request->file('image');
        $file_extention = $request->file('image')->extension();
        $file_name = Carbon::now()->timestamp . '.' . $file_extention;
        $this->GenerateBrandThumbnailImage($image,$file_name);
        $brand->image = $file_name;
    }
    $brand->save();
    return redirect()->route('admin.brands')->with('status','Record has been updated successfully !');
}

public function delete_brand($id)
{
    $brand = Brand::find($id);
    if (File::exists(public_path('uploads/brands').'/'.$brand->image)) {
        File::delete(public_path('uploads/brands').'/'.$brand->image);
    }
    $brand->delete();
    return redirect()->route('admin.brands')->with('status','Record has been deleted successfully !');
}

    public function GenerateBrandThumbnailImage($image, $imageName)
    {
        $destinationPath = public_path('uploads/brands');
        $img = Image::read($image->path());
        $img->cover(124,124,"top");
        $img->resize(124,124,function($constraint){
            $constraint->aspectRatio();
        })->save($destinationPath.'/'.$imageName);
    }
}
