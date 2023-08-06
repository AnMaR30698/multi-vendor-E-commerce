<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Subcategory;
use App\Models\Vendor;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SubcategoryController extends Controller
{
    public function index()
    {
        $subcategories = Subcategory::all(); // Get all subcategories
        $subcategoryData = [];
        foreach ($subcategories as $subcategory) {
            $subcategoryImageUrl = Storage::url($subcategory->image_path);
            $subcategoryData[] = [
                'id' => $subcategory->id,
                'name' => $subcategory->name,
                'image_url' => $subcategoryImageUrl,
            ];
        }
        return response()->json([
            'status' => 'success',
            'data' => $subcategoryData
        ]);
    }

    public function show($id)
    {
        // Get a single subcategory by ID
        $subcategory = Subcategory::find($id);
        if(!$subcategory){
            return response()->json([
                'status'=>'error',
                'message' => 'subcategory not found',
                
            ]);
        } 
        $subCategoryImageUrl = Storage::url($subcategory->image);
        $vendors = $subcategory->vendors; // Get all vendors for the subcategory
        $vendorData = [];
        foreach ($vendors as $vendor) {
            $vendorImageUrl = Storage::url($vendor->image);
            $vendorData[] = [
                'id' => $vendor->id,
                'user_id' => $vendor->user->id,
                'name' => $vendor->user->name,
                'email' => $vendor->user->email,
                'phone' => $vendor->user->phone,
                'address' => $vendor->user->address,
                'store_name' => $vendor->store_name,
                'store_address' => $vendor->store_address,
                'driver_license_id' => $vendor->driver_license_id,
                'id_number' => $vendor->id_number,
                'image_url' => $vendorImageUrl,
            ];
        }
        $subcategoriesWithVendors[] = [
            'id' => $subcategory->id,
            'name' => $subcategory->name,
            'image_url' => $subCategoryImageUrl,
            'vendors' => $vendorData,
        ];
        return response()->json([
            'status' => 'success',
            'data' => $subcategoriesWithVendors
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:sub_categories,name',
            'image' => 'required|image',
            'category_id' => 'required|exists:categories,id'
        ]);
        $category = Category::find($request->category_id);
        if(!$category){
            return response()->json([
                'status'=>'error',
                'message' => 'Category not found',
                
            ]);
        }
        
        $subcategory = new Subcategory();
        $subcategory->name = $request->name;
        $subcategory->category_id = $request->category_id;
        $path = $request->file('image')->store('public/images');
        $url = Storage::url($path);
        $subcategory->image = $path; 
        $subcategory->save();
    
        return response()->json([
            'status' => 'success',
            'message' => 'SubCategory has been created successfully.',
            'subcategory_id' =>$subcategory->id,
            'category'=> $category->name,
            'subcategory_name' => $subcategory->name,
            'subcategory_image' => $url,
        ],Response::HTTP_CREATED);
    }

    public function update(Request $request, $id)
    {
        $subcategory = Subcategory::find($id); // Get a single subcategory by ID
        if(!$subcategory){
            return response()->json([
                'status' => 'error',
                'message' => 'SubCategory not found',
                ]);
        }
        $request->validate([
            'name' => 'required|unique:sub_categories,name,'.$subcategory->id,
            'image' => 'required|image',
            'category_id' => 'required|exists:categories,id'
        ]);
        $category = Category::where('id',$subcategory->category_id)->find($request->category_id);
        if(!$category){
            return response()->json([
                'status'=>'error',
                'message' => 'Category not found',
            ]);
        }
        $subcategory->name = $request->name;
        $path = $request->file('image')->store('public/images');
        $url = Storage::url($path);
        $subcategory->image = $path; 
        $subcategory->save();
        return response()->json([
            'status' => 'success',
            'message' => 'SubCategory has been updated successfully.',
            'subcategory_id' =>$subcategory->id,
            'category'=> $category->name,
            'subcategory_name' => $subcategory->name,
            'subcategory_image' => $url,
        ],Response::HTTP_ACCEPTED);
    }
    public function destroy($id)
    {
        // Get a single subcategory by ID
        $subcategory = Subcategory::find($id); 
        if(!$subcategory){
            return response()->json([
                'status'=>'error',
                'message' => 'SubCategory not found',
            ]);
        }
        
        // Detach all vendors related to the subcategory
        $vendors = $subcategory->vendors;
        $subcategory->vendors()->detach();
    
        // Delete the detached vendors and associated users
        foreach ($vendors as $vendor) {
            $user = $vendor->user;
            $vendor->delete();
            if($user){
                $user->delete();
            }
        }

        Storage::disk('public')->delete($subcategory->image);
        
        $subcategory->delete(); // Delete subcategory
        return response()->json([
            'status' => 'success',
            'message' => 'SubCategory has been deleted successfully.',
        ]);
    }
}
