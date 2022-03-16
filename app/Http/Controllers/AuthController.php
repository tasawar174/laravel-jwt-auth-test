<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use Validator;

class AuthController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }
	
    public function login(Request $request)
	{
    	$validator = Validator::make($request->all(), [
            'name' => 'required',
            'password' => 'required|min:12',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toArray(), 400);
        }
        if (! $token = auth()->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
		return response()->json(['name' => $request->name, 'token' => $token], 200);
    }
	
    public function register(Request $request) 
	{
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:12|regex:/[a-z]/|regex:/[A-Z]/',
			'confirm_password'  =>  'required|same:password',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toArray(), 400);
        }
		$data	=	$request->all();
		$data['password']	=	bcrypt($request->password);
        $user = User::create($data);
        return response()->json(['message' => 'User successfully registered'], 200);
    }
	    
    public function userProfile() 
	{
        return response()->json(auth()->user());
    }
}