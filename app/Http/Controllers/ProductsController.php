<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Models\Products;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductsController extends Controller
{
    public function index($vendor_id){
        $vendor = Vendor::where('id', $vendor_id)->first();
            if(!$vendor){
                return response()->json([
                    'status' => 'success',
                    'message' => 'this vendor is not exist',
                ], Response::HTTP_FORBIDDEN);
            }
        $products = $vendor->products;
        $productsWithImages = [];

        foreach ($products as $product) {
            $url = Storage::url($product->image);
            $productsWithImages[] = [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'price' => $product->price,
                'image' => $url,
                'vendor_id'=>$product->vendor_id
            ];
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Products retrieved successfully',
            'products' => $productsWithImages,
        ], Response::HTTP_OK);
    }
    public function allProducts(){
        $products = Products::all();
        $productsWithImages = [];
        foreach ($products as $product) {
            $url = Storage::url($product->image);
            $productsWithImages[] = [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'price' => $product->price,
                'image' => $url,
                'vendor_id'=>$product->vendor_id
            ];
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Products retrieved successfully',
            'products' => $productsWithImages,
        ], Response::HTTP_OK);
    }
    public function store(Request $request, $vendor_id)
    {
        $user = Auth::user();
        $request->validate([
            'name' => 'sometimes|string',
            'description' => 'required',
            'price' => 'required|decimal',
            'image' => 'required'
        ]);
        if($user->hasRole('vendor')){
            $vendor = Vendor::where('id', $vendor_id)->first();
            if(!$vendor){
                return response()->json([
                    'status' => 'success',
                    'message' => 'this vendor is not exist',
                ], Response::HTTP_FORBIDDEN);
            }
            if($user->vendor->id !== $vendor->id){
                return response()->json([
                    'status' => 'success',
                    'message' => 'you can not add products to other vendors',
                ], Response::HTTP_FORBIDDEN);
            }
        }
        $ifHasThisProduct = Products::where('vendor_id', $vendor_id)
                                ->where('name', $request->name)
                                ->where('description',$request->description)
                                ->first();

        if($ifHasThisProduct){
            return response()->json([
                    'status' => 'error',
                    'message' => 'Product with this name and description already exists',
                    'product' => $ifHasThisProduct,
                ], Response::HTTP_CONFLICT);
        }
        $product = new Products();
        $product->name = $request->name;
        $product->price = $request->price;
        $product->description = $request->description;
        $product->vendor_id = $vendor_id;
        $path = $request->file('image')->store('public/images');
        $url = Storage::url($path);
        $product->image = $path;
        $product->save();
        return response()->json([
            'status' => 'success',
            'message' => 'Product created successfully.',
            'product-id' => $product->id,
            'product-name' => $product->name,
            'product-description' => $product->description,
            'product-price' => $product->price,
            'vendor_id' => $product->vendor_id,
            'product-image'=>$url,
        ],Response::HTTP_ACCEPTED);
    }
    public function update(StoreProductRequest $request, $vendor_id ,$product_id){
        $user = Auth::user();
        if($user->hasRole('vendor')){
            $vendor = Vendor::where('id', $vendor_id)->first();
            if(!$vendor){
                return response()->json([
                    'status' => 'success',
                    'message' => 'this vendor is not exist',
                ], Response::HTTP_FORBIDDEN);
            }
            if($user->vendor->id !== $vendor->id){
                return response()->json([
                    'status' => 'success',
                    'message' => 'you can not add products to other vendors',
                ], Response::HTTP_FORBIDDEN);
            }
        }
        $product = null;
        if($user->hasRole('vendor')){
            $product = Products::where('vendor_id',$vendor_id)->find($product_id);
        }else if($user->hasRole('admin')){
            $product = Products::find($product_id);
        }
        if(!$product){
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found in this vendor',
            ],Response::HTTP_NOT_FOUND);
        }
        $sameProduct = Products::where('name', $request->name)
                            ->where('description',$request->description)
                            ->whereNotIn('id', [$product_id])->first();
        if($sameProduct){
        return response()->json([
            'status' => 'error',
            'message' => 'Product with this name and description already exists',
        ],Response::HTTP_NOT_FOUND);
        }
        $product->name = $request->name;
        $product->price = $request->price;
        $product->description = $request->description;
        $product->vendor_id = $vendor_id;
        $path = $request->file('image')->store('public/images');
        $url = Storage::url($path);
        $product->image = $path;
        $product->save();
        return response()->json([
            'status' => 'success',
            'message' => 'Product updated successfully.',
            'id'=>$product->id,
            'product-name' => $product->name,
            'product-description' => $product->description,
            'product-price' => $product->price,
            'vendor_id' => $product->vendor_id,
            'product-image'=>$url,
        ],Response::HTTP_ACCEPTED);
    }
    public function delete($vendor_id,$product_id)
    {
        $user = Auth::user();
        $vendor = Vendor::where('id', $vendor_id)->first();
            if(!$vendor){
                return response()->json([
                    'status' => 'success',
                    'message' => 'this vendor is not exist',
                ], Response::HTTP_FORBIDDEN);
            }
        $product = null;
        if($user->hasRole('vendor')){
            $product = Products::where('vendor_id',$vendor_id)->find($product_id);
        }else if($user->hasRole('admin')){
            $product = Products::find($product_id);
        }
        if (!$product) {
            return response()->json([
                'status'=>'erorr',
                'message' => 'Product not found'
            ], Response::HTTP_NOT_FOUND);
        }
        $product->delete();
        return response()->json([
            'status'=>'success',
            'message' => 'Product deleted successfully'
        ]);
    }
}
