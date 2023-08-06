<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function editCustomer(Request $request)
    {
        $user = Auth::user();
        if(!$user){
            return response()->json([
                'status' => 'error',
                'message' => 'You are not customer you can not'
                ],Response::HTTP_NOT_FOUND);
        }
        $rules = [
            'name' => 'sometimes|string',
            'email' => 'sometimes|email|unique:users,email,'.$user->id.'|nullable',
            'phone' => 'sometimes|string|unique:users,phone,'.$user->id,
            'address' => 'sometimes|string|nullable',
            'verified' => 'sometimes|boolean',
        ];

        $request->validate($rules);

        $user->fill($request->only(array_keys($rules)));
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Profile updated successfully',
            'user' => $user,
        ]);
    }
    public function editVendor(Request $request)
    {
        $user = Auth::user();
        $vendor = $user->vendor;

        if(!$vendor){
            return response()->json([
                'status' => 'error',
                'message' => 'You are not a vendor and cannot perform this action.'
            ], Response::HTTP_NOT_FOUND);
        }

        // Define validation rules for user and vendor separately
        $userRules = [
            'name' => 'sometimes|string',
            'email' => 'sometimes|email|unique:users,email,'.$user->id.'|nullable',
            'phone' => 'sometimes|string|unique:users,phone,'.$user->id,
            'address' => 'sometimes|string|nullable',
            'verified' => 'sometimes|boolean',
        ];

        $vendorRules = [
            'store_name' => 'sometimes|required|string',
            'store_address' => 'sometimes|required|string',
            'driver_license_id' => 'sometimes|required|string',
            'id_number' => 'sometimes|required|string',
            'sub_category_id' => 'sometimes|required|integer|exists:sub_categories,id',
            'image' => 'sometimes|image',
        ];

        $request->validate($userRules + $vendorRules);

        // Update the user object
        $user->fill($request->only(array_keys($userRules)));
        $user->save();

        // Update the vendor object
        $vendor->fill($request->only(array_keys($vendorRules)));
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('public/images');
            $vendor->image = $imagePath;
        }
        $vendor->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Profile updated successfully',
            'user' => $user,
        ]);
    }
    public function editDeliveryBoy(Request $request)
    {
        $user = Auth::user();
        $deliveryBoy = $user->deliveryBoy;

        if(!$deliveryBoy){
            return response()->json([
                'status' => 'error',
                'message' => 'You are not a delivery boy and cannot perform this action.'
            ], Response::HTTP_NOT_FOUND);
        }

        // Define validation rules for user and delivery boy separately
        $userRules = [
            'name' => 'sometimes|string',
            'email' => 'sometimes|email|unique:users,email,'.$user->id.'|nullable',
            'phone' => 'sometimes|string|unique:users,phone,'.$user->id,
            'address' => 'sometimes|string|nullable',
            'verified' => 'sometimes|boolean',
            'image' => 'sometimes|required|image',
        ];

        $deliveryBoyRules = [
            'driver_license_id' => 'sometimes|required|string',
        ];

        $request->validate($userRules + $deliveryBoyRules);

        // Update the user object
        $user->fill($request->only(array_keys($userRules)));
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('public/images');
            $user->image =$imagePath;
        }
        $user->save();

        // Update the delivery boy object
        $deliveryBoy->fill($request->only(array_keys($deliveryBoyRules)));
        $deliveryBoy->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Profile updated successfully',
            'user' => $user,
        ]);
    }
    public function editAdmin(Request $request)
    {
        $user = Auth::user();
        $admin = $user->admin;

        if(!$admin){
            return response()->json([
                'status' => 'error',
                'message' => 'You are not an admin and cannot perform this action.'
            ], Response::HTTP_NOT_FOUND);
        }

        // Define validation rules for user and admin separately
        $userRules = [
            'name' => 'sometimes|string',
            'email' => 'sometimes|email|unique:users,email,'.$user->id.'|nullable',
            'phone' => 'sometimes|string|unique:users,phone,'.$user->id,
            'address' => 'sometimes|string|nullable',
            'verified' => 'sometimes|boolean',
        ];

        $adminRules = [
            'facebook' => 'sometimes|required|string|nullable',
            'twitter' => 'sometimes|required|string|nullable',
            'instagram' => 'sometimes|required|string|nullable',
        ];

        $request->validate($userRules + $adminRules);

        // Update the user object
        $user->fill($request->only(array_keys($userRules)));
        $user->save();

        // Update the admin object
        $admin->fill($request->only(array_keys($adminRules)));
        $admin->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Profile updated successfully',
            'user' => $user,
        ]);
    }
        
}







