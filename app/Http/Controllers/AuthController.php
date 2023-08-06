<?php

namespace App\Http\Controllers;

use App\Helpers\AuthHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Http\Response;
use App\Helpers\VerificationHelper;
use App\Http\Requests\RegisterUserRequest;
use App\Models\Admin;
use App\Models\Cart;
use App\Models\Customer;
use App\Models\DeliveryBoy;
use App\Models\SubCategory;
use App\Models\Vendor;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register','forgotPassword','resetPassword']]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $credentials = [
            'phone' => $request->phone,
            'password' => $request->password,
            'verified' => true, // Check whether the user is verified
        ];

        $token = Auth::attempt($credentials);

        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $user = Auth::user();

        return response()->json([
            'status' => 'success',
            'message' => 'Login successful',
            'user' => $user,
            'authorization' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);
    }
    //! ? enable this in future
    // public function register(Request $request,VerificationHelper $verificationHelper){
    public function register(RegisterUserRequest $request){
        //! ? enable this in future
        // $phoneNumber = $request->phone;
        // $verificationCode = $request->verification_code;
        // if (!$verificationHelper->verifyCode($phoneNumber, $verificationCode)) {
        //     return response()->json([
        //         'message' => 'Invalid verification code.'
        //     ], Response::HTTP_UNPROCESSABLE_ENTITY);
        // }
        $user = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'verified'=> true
        ]);
       
        $relatedModel = $this->createRelatedModel($request->role,$user,$request);
        if(!$relatedModel){
            return response()->json([
                'status' => 'error',
                'message' => 'some thing error'
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $subcategories = null;
        if($user->role == 'vendor'){
            $subcategories = $request->subCategories;
            $relatedModel->subcategories()->attach($subcategories);
            $subcategories = SubCategory::where('id', $subcategories)->first();
            $subcategories = [
                'name'=>$subcategories->name,
                'image'=>$subcategories->image,
            ];
        }
        $token = Auth::login($user);
        $user->assignRole($request->role);
        if($user->hasRole('customer')){
            $cart = new Cart();
            $cart->customer()->associate($relatedModel);
            $cart->save();
        }
        if($user->role == 'vendor'){
            return response()->json([
                'status' => 'success',
                'message' => 'User created successfully',
                'user' => User::with($request->role)
                              ->where('id', $user->id)
                              ->first(),
                'categories'=>$subcategories,
                'authorization' => [
                    'token' => $token,
                    'type' => 'bearer',
                ]
            ],Response::HTTP_CREATED);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
            'user' => User::with($request->role)->where('id', $user->id)->first(),
            'authorization' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ],Response::HTTP_CREATED);
    }
    protected function createRelatedModel(string $role, User $user, Request $request){
        switch ($role) {
            case 'customer':
                return Customer::create([
                    'user_id' => $user->id,
                ]);
            case 'vendor':
                return Vendor::create([
                    'user_id' => $user->id,
                    'store_name' => $request->store_name,
                    'store_address' => $request->store_address,
                    'driver_license_id' => $request->driver_license_id,
                    'id_number' => $request->id_number,
                    'sub_category_id' => $request->sub_category_id,
                    'image'=> $request->file('image')->store('public/images')
                ]);
            case 'deliveryBoy':
                return DeliveryBoy::create([
                    'user_id' => $user->id,
                    'driver_license_id' => $request->driver_license_id,
                    'image'=> $request->file('image')->store('public/images')
                ]);
            case 'admin':
                return Admin::create([
                    'user_id' => $user->id,
                    'facebook' => $request->facebook,
                    'twitter' => $request->twitter,
                    'instagram' => $request->instagram,
                ]);
            default:
                return null;
        }
    }
    public function logout()
    {
        Auth::logout();
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ]);
    }
    public function refresh()
    {
        return response()->json([
            'status' => 'success',
            'user' => Auth::user(),
            'authorization' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ]);
    }
    public function deleteAccount()
    {
        $user = Auth::user();

        // Delete related models based on the user's role
        switch ($user->role) {
            case 'customer':
                $user->customer()->delete();
                break;
            case 'vendor':
                $user->vendor()->delete();
                break;
            case 'deliveryBoy':
                $user->deliveryBoy()->delete();
                break;
            case 'admin':
                $user->admin()->delete();
                break;
        }

        // Delete the user
        $user->delete();

        // Logout the user
        Auth::logout();

        return response()->json([
            'status' => 'success',
            'message' => 'Account deleted successfully',
        ]);
    }
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'status' => 'success',
                'message' => 'Password reset link sent to your email.',
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Unable to send password reset link.',
        ], Response::HTTP_BAD_REQUEST);
    }
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $credentials = $request->only(
            'email', 'password', 'password_confirmation', 'token'
        );

        $response = Password::reset($credentials, function ($user, $password) {
            $user->forceFill([
                'password' => Hash::make($password),
            ])->save();

            event(new PasswordReset($user));
        });

        if ($response === Password::PASSWORD_RESET) {
            return response()->json([
                'status' => 'success',
                'message' => 'Password has been reset successfully.',
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Unable to reset password.',
        ], Response::HTTP_BAD_REQUEST);
    }
}