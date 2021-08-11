<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Category;
use App\Section;
use Session;
Use Image;

class CategoryController extends Controller
{
    public function categories()
    {
        Session::put('page','categories');
        $categories = Category::with(['section','parentcategory'])->get();

        return view('admin.categories.categories')->with(compact('categories'));
    }

    public function updateCategoryStatus(Request $request)
    {
        if($request->ajax())
        {
            $data = $request->all();
            //echo "<pre>";print_r($data); die;


            if($data['status']=="Active")
            {
                $status = 0;
            }
            else
            {
                $status = 1;
            }
            Category::where('id',$data['category_id'])->update(['status'=>$status]);
            return response()->json(['status'=>$status,'category_id'=>$data['category_id']]);
        }
    }




    public function addEditCategory(Request $request, $id=null)
    {
        if($id=="")
        {
            $title = "Add Category";
            //add category
            $category = new Category;
            $categorydata = array();
            $getCategories=array();
            $message = "Category Added Successfully!";
        }
        else
        {
            //edit category

            $title = "Edit Category";
            $categorydata = Category::where('id',$id)->first();
            $categorydata = json_decode(json_encode($categorydata),true);
            $getCategories = Category::with('subcategories')->where(['parent_id'=>0,'section_id'=>$categorydata['section_id']])->get();
            $getCategories = json_decode(json_encode($getCategories),true);

            $category = Category::find($id);
            $message = "Category Updated Successfully!";

        }

        if($request->isMethod('post'))
        {
            $data = $request->all();


            //validations
            $rules = [
                'category_name' => 'required|regex:/^[\pL\s\-]+$/u',
                'section_id' => 'required',
                'category_image' => 'image',
                'url' => 'required'
            ];
            $customMessages = [
                'category_name.required' => 'Category Name is Required!',
                'category_name.regex' => 'Valid Name is Required!',
                'section_id.required' => 'Section is Required!',
                'url.required' => 'Category URL is Required!',
                'category_image.image' => 'Please Upload an Image File'
            ];
            $this->validate($request,$rules,$customMessages);



            //upload image
            if($request->hasFile('category_image')) {
                $image_tmp = $request->file('category_image');
                if ($image_tmp->isValid()) {
                    $extension = $image_tmp->getClientOriginalExtension();
                    $imageName = rand(111, 99999) . '.' . $extension;
                    $imagePath = 'images/category_images/' . $imageName;
                    Image::make($image_tmp)->save($imagePath);
                    //save
                    $category->category_image = $imageName;
                }
            }





            if(empty($data['category_discount']))
            {
                $data['category_discount'] = "";
            }
            if(empty($data['description']))
            {
                $data['description'] = "";
            }
            if(empty($data['met_description']))
            {
                $data['meta_description'] = "";
            }
            if(empty($data['meta_title']))
            {
                $data['meta_title'] = "";
            }
            if(empty($data['meta_keywords']))
            {
                $data['meta_keywords'] = "";
            }

            $category->parent_id=$data['parent_id'];
            $category->section_id=$data['section_id'];
            $category->category_name=$data['category_name'];
            $category->category_discount=$data['category_discount'];
            $category->description=$data['description'];
            $category->url=$data['url'];
            $category->meta_title=$data['meta_title'];
            $category->meta_description=$data['meta_keywords'];
            $category->meta_keywords=$data['meta_keywords'];
            $category->status=1;

            $category->save();

            Session::flash('success_message',$message);
            return redirect('admin/categories');
        }



        //get all sections
        $getSections = Section::get();


        return view('admin.categories.add_edit_category')->with(compact('title',
            'getSections', 'categorydata', 'getCategories'));
    }


    public function appendCategoryLevel(Request $request)
    {
        if($request->ajax())
        {
            $data = $request->all();
            $getCategories = Category::with('subcategories')->where(['section_id'=>$data['section_id'],'parent_id'=>0,'status'=>1])->get();
            $getCategories = json_decode(json_encode($getCategories),true);
            return view('admin.categories.append_categories_level')->with(compact('getCategories'));

        }
    }

    public function deleteCategoryImage($id)
    {
        //get cat image
        $categoryImage = Category::select('category_image')->where('id',$id)->first();

        //get cat image path
        $category_image_path = 'images/category_images/';

        //delete image from folder if exists
        if(file_exists($category_image_path.$categoryImage->category_image))
        {
            unlink($category_image_path.$categoryImage->category_image);
        }

        //delete from database
        Category::where('id',$id)->update(['category_image'=>'']);

        $message = 'Category Image has been deleted successfully!';
        session::flash('success_message',$message);

        return redirect()->back();
    }

    public function deleteCategory($id)
    {
        Category::where('id',$id)->delete();

        $message = 'Category has been deleted successfully!';
        session::flash('success_message',$message);

        return redirect()->back();

    }
}
