<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\ResultResponse;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Register an user
     */
    public function signup(Request $request)
    {
        $first_name = $request->get('first_name');
        $last_name = $request->get('last_name');
        $dni = $request->get('dni');
        $email = $request->get('email');
        $password = $request->get('password');
        $password_confirm = $request->get('password_confirm');
        $phone = strval($request->get('phone'));
        $iban = $request->get('iban');
        $about = $request->get('about');
        $country = $request->get('country');
        $city = $request->get('city');
        $address = $request->get('address');
        $role = $request->get('role');

        $rules = [
            'first_name' => 'required|min:2|max:20|regex:/^[^0-9]*$/',
            'last_name' => 'required|min:2|max:20|regex:/^[^0-9]*$/',
            'dni' => 'required|size:9|regex:/^\d{8}[a-zA-Z]$/',
            'email' => 'required|unique:users|email',
            'password' => ['required', Password::min(10)->mixedCase()->numbers()->symbols()],
            'password_confirm' => 'required|same:password',
            'phone' => 'string|nullable|min:9|max:12|regex:/^\+{0,1}\d*$/',
            'iban' => 'required|size:24|regex:/^ES\d{22}$/',
            'about' => 'string|nullable|min:20|max:250',
            'country' => 'string|nullable',
            'city' => 'string|nullable',
            'address' => 'string|nullable',
            'role' => ['required', Rule::in(['admin', 'planner', 'customer'])],
        ];

        $validator = Validator::make($request->all(), $rules);

        $resultResponse = new ResultResponse();

        if ($validator->fails()){
            $resultResponse->setStatusCode(ResultResponse::ERROR_CODE);
            $resultResponse->setMessage($validator->errors());
            return response()->json($resultResponse);
        }
        
        try{
            $user = new User([
                'first_name' => $first_name,
                'last_name' => $last_name,
                'dni' => $dni,
                'email' => $email,
                'password' => Hash::make($password),
                'phone' => $phone,
                'address' => $address,
                'role' => $role,
                'city' => $city,
                'country' => $country,
                'iban' => $iban,
                'about' => $about
            ]);
            $user->save();
            $resultResponse->setData($user);
            $resultResponse->setStatusCode(ResultResponse::SUCCESS_CODE);
            $resultResponse->setMessage(ResultResponse::TXT_SUCCESS_CODE);
        }catch(\Exception $e){
            $resultResponse->setStatusCode(ResultResponse::ERROR_CODE);
            $resultResponse->setMessage(ResultResponse::TXT_ERROR_CODE);
        }

        return response()->json($resultResponse);
    }

    /**
     * Login an user
     */
    public function login(Request $request)
    {
        $email = $request->get('email');
        $password = $request->get('password');


        $rules = [
            'email' => 'required|email',
            'password' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        $resultResponse = new ResultResponse();

        if ($validator->fails()){
            $resultResponse->setStatusCode(ResultResponse::ERROR_CODE);
            $resultResponse->setMessage($validator->errors());
            return response()->json($resultResponse);
        }
        
        try{
            $user = User::where('email', $email)->firstOrFail();

            if(Auth::attempt(['email' => $email, 'password' => $password])){
                $resultResponse->setData($user);
                $resultResponse->setStatusCode(ResultResponse::SUCCESS_CODE);
                $resultResponse->setMessage(ResultResponse::TXT_SUCCESS_CODE);
            }
            else{
                $resultResponse->setStatusCode(ResultResponse::ERROR_CODE);
                $resultResponse->setMessage('El email o la contraseña son incorrectos.');
            }
        }catch(\Exception $e){
            $resultResponse->setStatusCode(ResultResponse::ERROR_CODE);
            $resultResponse->setMessage('El email o la contraseña son incorrectos.');
        }

        return response()->json($resultResponse);
    }
}
