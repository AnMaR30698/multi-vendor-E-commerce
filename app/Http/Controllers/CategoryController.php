<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
   
    public function index()
    {
        $categories = Category::with('subcategories')->get();
        $categoriesWithImages = [];

        foreach ($categories as $category) {
            $categoryImageUrl = Storage::url($category->image);

            $subcategoryData = [];
            foreach ($category->subcategories as $subcategory) {
                $subcategoryImageUrl = Storage::url($subcategory->image_path);
                $subcategoryData[] = [
                    'id' => $subcategory->id,
                    'name' => $subcategory->name,
                    'image_url' => $subcategoryImageUrl,
                ];
            }

            $categoriesWithImages[] = [
                'id' => $category->id,
                'name' => $category->name,
                'image_url' => $categoryImageUrl,
                'subcategories' => $subcategoryData,
            ];
        }

        return response()->json([
            'status' => 'success',
            'data' => $categoriesWithImages,
        ]);
    }
     
    public function show($id)
    {
        $category = Category::where('id',$id)->with('subcategories')->first();
        if(!$category){
            return response()->json([
                'status'=>'error',
                'message' => 'Category not found',
                
            ]);
        }
        $categoryWithImages = [];
        $categoryImageUrl = Storage::url($category->image);
        $subcategoryData = [];
        foreach ($category->subcategories as $subcategory) {
            $subcategoryImageUrl = Storage::url($subcategory->image);
            $subcategoryData[] = [
                'id' => $subcategory->id,
                'name' => $subcategory->name,
                'image_url' => $subcategoryImageUrl,
            ];
        }

        $categoryWithImages[] = [
            'id' => $category->id,
            'name' => $category->name,
            'image_url' => $categoryImageUrl,
            'subcategories' => $subcategoryData,
        ];

        return response()->json([
            'status' => 'success',
            'data' => $categoryWithImages,
        ]);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:categories|max:255',
            'image' => 'required|image',
        ]);
        $category = new Category();
        $category->name = $request->name;
        $path = $request->file('image')->store('public/images');
        $url = Storage::url($path);
        $category->image = $path; 
        $category->save();
    
        return response()->json([
            'status' => 'success',
            'message' => 'Category has been created successfully.',
            'id'=>$category->id,
            'category_name' => $category->name,
            'category_image' => $url,
        ],Response::HTTP_CREATED);
    }
    public function update(Request $request, $id)
    {
        $user= Auth::user();
            $request->validate([
                'name' => 'required|string|max:255',
                'image' => 'required',
            ]);
            $category = Category::find($id);
            if(!$category){
                return response()->json([
                    'status'=>'error',
                    'message' => 'Category not found',
                    
                ]);
            }
            $isExist = Category::whereNotIn('id',[$id])
                            ->where('name',$request->name)
                            ->first();
            if($isExist){
                return response()->json([
                    'status' => 'error',
                    'message' => 'Category with this name Already Exist',
                    'data' =>$isExist
                    ]);
            }
    
            $category->name = $request->name;
            $path = $request->file('image')->store('public/images');
            $url = Storage::url($path);
            $category->image = $path; 
            $category->save();
            return response()->json([
                'status' => 'success',
                'message' => 'Category updated successfully',
                'category_name' => $category->name,
                'category_image' => $url,
            ],Response::HTTP_ACCEPTED);
        #
    }
    public function destroy($id)
    {
        $category = Category::find($id);
        if(!$category){
            return response()->json([
                'status'=>'error',
                'message' => 'Category not found',
                
            ]);
        }
        Storage::disk('public')->delete($category->image);

        $category->subcategories()->delete();
        $category->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Category has been deleted successfully.',
        ]);
    }
}