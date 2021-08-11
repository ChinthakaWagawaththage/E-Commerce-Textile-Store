<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Product;
use App\Category;
use Session;
use App\Section;
use Image;

class ProductsController extends Controller
{
    public function products()
    {
        Session::put('page','products');

        $products = Product::with(['category'=>function($query){
            $query->select('id','category_name');
        },'section'=>function($query){
            $query->select('id','name');
        }])->get();


       // $products = json_decode(json_encode($products));

        return view('admin.products.products')->with(compact('products'));

    }


    //update product status
    public function updateProductStatus(Request $request)
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
            Product::where('id',$data['product_id'])->update(['status'=>$status]);
            return response()->json(['status'=>$status,'product_id'=>$data['product_id']]);
        }
    }



    //delete product
    public function deleteProduct($id)
    {
        Product::where('id',$id)->delete();

        $message = 'Product has been deleted successfully!';
        session::flash('success_message',$message);

        return redirect()->back();

    }


    //add-edit product
    public function addEditProduct(Request $request,$id=null)
    {
        if($id=="")
        {
            $title = "Add Product";
            $product = new Product;
            $productdata=array();
            $message = "Product Added Successfully";

        }
        else
        {
            $title = "Edit Product";
            $productdata = Product::find($id);
            $productdata = json_decode(json_encode($productdata),true);
            $product = Product::find($id);
            $message = "Product Updated Successfully";

        }

        if($request->isMethod('post'))
        {
            $data = $request->all();


            //validations
            $rules = [
                'category_id' => 'required',
                'product_name' => 'required|regex:/^[\pL\s\-]+$/u',
                'product_code' => 'required|regex:/^[\w-]*$/',
                'product_price' => 'required|numeric',
                'product_color' => 'required|regex:/^[\pL\s\-]+$/u'

            ];
            $customMessages = [
                'category_id.required' => 'Category ID is Required!',
                'product_name.required' => 'Product Name is Required!',
                'product_name.regex' => 'Valid Product Name is Required!',
                'product_code.required' => 'Product Code is Required!',
                'product_code.regex' => 'Valid Product Code is Required!',
                'product_price.required' => 'Product Price is Required!',
                'product_price.numeric' => 'Valid Product Price is Required!',
                'product_color.required' => 'Product Color is Required!',
                'product_color.regex' => 'Valid Product Color is Required!',

            ];
            $this->validate($request,$rules,$customMessages);

            if(empty($data['is_featured']))
            {
                $is_featured = "No";
            }
            else
            {
                $is_featured = "Yes";
            }


            if(empty($data['fabric']))
            {
                $data['fabric'] = "";
            }

            if(empty($data['pattern']))
            {
                $data['pattern'] = "";
            }

            if(empty($data['sleeve']))
            {
                $data['sleeve'] = "";
            }

            if(empty($data['fit']))
            {
                $data['fit'] = "";
            }

            if(empty($data['occasion']))
            {
                $data['occasion'] = "";
            }

            if(empty($data['meta_title']))
            {
                $data['meta_title'] = "";
            }

            if(empty($data['meta_description']))
            {
                $data['meta_description'] = "";
            }

            if(empty($data['meta_keywords']))
            {
                $data['meta_keywords'] = "";
            }

            if(empty($data['product_video']))
            {
                $data['product_video'] = "";
            }

            if(empty($data['main_image']))
            {
                $data['main_image'] = "";
            }

            if(empty($data['product_discount']))
            {
                $data['product_discount'] = 0;
            }

            if(empty($data['product_weight']))
            {
                $data['product_weight'] = 0;
            }

            if(empty($data['description']))
            {
                $data['description'] = "";
            }

            if(empty($data['wash_care']))
            {
                $data['wash_care'] = "";
            }

            //upload image
           /* if($request->hasFile('category_image')) {
                $image_tmp = $request->file('category_image');
                if ($image_tmp->isValid()) {
                    $extension = $image_tmp->getClientOriginalExtension();
                    $imageName = rand(111, 99999) . '.' . $extension;
                    $imagePath = 'images/category_images/' . $imageName;
                    Image::make($image_tmp)->save($imagePath);
                    //save
                    $category->category_image = $imageName;
                }
            }*/


            //upload product image
            if($request->hasFile('main_image'))
            {
                $image_temp = $request->file('main_image');
                if($image_temp->isValid())
                {
                    //Upload Image after Resize
                    $image_name = $image_temp->getClientOriginalName();
                    $extension = $image_temp->getClientOriginalExtension();
                    $imageName = rand(1111,99999).'.'.$extension;
                    $large_image_path = 'images/product_images/large/'.$imageName;
                    $medium_image_path = 'images/product_images/medium/'.$imageName;
                    $small_path_name = 'images/product_images/small/'.$imageName;
                    Image::make($image_temp)->save($large_image_path);
                    Image::make($image_temp)->resize(520,600)->save($medium_image_path);
                    Image::make($image_temp)->resize(260,300)->save($small_path_name);
                    $product->main_image = $imageName;
                }
            }

            //upload product video
            if($request->hasFile('product_video'))
            {
                $video_temp = $request->file('product_video');
                if($video_temp->isValid())
                {
                    $video_name = $video_temp->getClientOriginalName();
                    $extension = $video_temp->getClientOriginalExtension();
                    $videoName = $video_name.'-'.rand(111,9999).'.'.$extension;
                    $video_path = 'videos/product_videos/';
                    $video_temp->move($video_path,$videoName);
                    $product->product_video = $videoName;
                }
            }


            //save products details to table
            $categoryDetails = Category::find($data['category_id']);
            $product->section_id = $categoryDetails['section_id'];
            $product->category_id =  $data['category_id'];
            $product->product_name =  $data['product_name'];
            $product->product_code =  $data['product_code'];
            $product->product_price =  $data['product_price'];
            $product->product_color =  $data['product_color'];
            $product->product_discount =  $data['product_discount'];
            $product->product_weight =  $data['product_weight'];
            $product->description =  $data['description'];
            $product->wash_care =  $data['wash_care'];
            $product->fabric =  $data['fabric'];
            $product->pattern =  $data['pattern'];
            $product->sleeve =  $data['sleeve'];
            $product->fit =  $data['fit'];
            $product->occasion =  $data['occasion'];
            $product->meta_title =  $data['meta_title'];
            $product->meta_description =  $data['meta_description'];
            $product->meta_keywords =  $data['meta_keywords'];
            $product->is_featured=  $is_featured;

            if(empty($data['main_image']))
            {
                $product->main_image = "";
            }
            if(empty($data['product_video']))
            {
                $product->product_video = "";
            }


            $product->status = "1";
            $product->save();
            session::flash('success_message',$message);
            return redirect('admin/products');


        }

        //filter arrays
        $fabricArray = array('Cotton','Polyester','Wool');
        $sleeveArray = array('Full Sleeve', 'Half Sleeve','Short Sleeve','Sleeveless');
        $patternArray = array('Checked','Plain','Printed','Self','Solid');
        $fitArray = array('Regular Fit','Loose Fit','Slim Fit');
        $occasionArray = array('Casual','Formal','Party','Sportswear');

        //section with categories and sub categories
        $categories = Section::with('categories')->get();


        return view('admin.products.add_edit_product')->with(compact('title','fabricArray',
        'sleeveArray','patternArray','fitArray','occasionArray','categories','productdata'));
    }

    public function deleteProductImage($id)
    {
        //get product image
        $productImage = Product::select('main_image')->where('id',$id)->first();

        //get product image path
        $small_image_path = 'images/product_images/small/';
        $medium_image_path = 'images/product_images/medium/';
        $large_image_path = 'images/product_images/large/';

        //delete image from folder if exists
        if(file_exists($small_image_path.$productImage->main_image))
        {
            unlink($small_image_path.$productImage->main_image);
        }
        if(file_exists($medium_image_path.$productImage->main_image))
        {
            unlink($medium_image_path.$productImage->main_image);
        }
        if(file_exists($large_image_path.$productImage->main_image))
        {
            unlink($large_image_path.$productImage->main_image);
        }

        //delete from database
        Product::where('id',$id)->update(['main_image'=>'']);

        $message = 'Product Image has been deleted successfully!';
        session::flash('success_message',$message);

        return redirect()->back();
    }

    public function deleteProductVideo($id)
    {
        // get product_video
        $productVideo = Product::select('product_video')->where('id',$id)->first();

        // get product_video path
        $product_video_path = 'videos/product_videos/';

        //delete video from folder if exists
        if(file_exists($product_video_path.$productVideo->product_video))
        {
            unlink($product_video_path.$productVideo->product_video);
        }

        //delete from database
        Product::where('id',$id)->update(['product_video'=>'']);

        $message = 'Product Video has been deleted successfully!';
        session::flash('success_message',$message);

        return redirect()->back();
    }

    public function addAttributes($id)
    {
        $productdata = Product::find($id);
        $title = "Product Attributes";
        return view('admin.products.add_attributes')->with(compact('productdata','title'));
    }

}
