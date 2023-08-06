<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'phone' => 'required|string|max:255|unique:users',
            'address' => 'nullable|string|max:255',
            'role' => 'required|string|in:customer,vendor,deliveryBoy,admin',
            //? customer
        // 'verification_code' => 'required_if:role,customer|string',
            //? delivery boy
            'driver_license_id'=>'required_if:role,delivery_boy',
            'image'=>'required_if:role,delivery_boy',

            //? vendor
            'driver_license_id'=>'required_if:role,vendor',
            'id_number'=>'required_if:role,vendor',
            // 'sub_category_id'=>'required_if:role,vendor',
            'store_name'=>'required_if:role,vendor',
            'store_address'=>'required_if:role,vendor',
            'image'=>'required_if:role,vendor|image',
            //? admin
            'facebook' => 'required_if:role,admin|string|max:255',
            'twitter' => 'required_if:role,admin|string|max:255',
            'instagram' => 'required_if:role,admin|string|max:255',
        ];
    }
}
