<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Auth;
use Validator;
use App\User;
use App\Role;
use JWTAuth;
use Session;
use DB;

class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        $v = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users',
            'password'  => 'required|min:3|confirmed',
        ]);
        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()
            ], 422);
        }
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();
        return response()->json(['status' => 'success'], 200);
    }

    /**
     * Register a new user
     */
    public function updatePassword(Request $request)
    {
        $v = Validator::make($request->all(), [
            'password'  => 'required|min:3|confirmed',
        ]);
        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()
            ], 422);
        }
        $id = Auth::user()->id;
        $user = User::find($id)->update([
            'password' => bcrypt($request->password)
        ]);
        return response()->json(['status' => 'success'], 200);
    }
    /**
     * Login user and return a token
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        // dd($this->guard()->attempt($credentials));
        // try{    
        // // your code here.   
        //     $token = $this->guard()->attempt($credentials);
        // }catch(Exception $e){    
        //     return response()->json(['error' => 'login_error'], 401);
        // }
        // return response()->json(['status' => 'success'], 200)->header('Authorization', $token);
        // if ($token = $this->guard()->attempt($credentials)) {
        //     return response()->json(['status' => 'success'], 200)->header('Authorization', $token);
        // }
        // return response()->json(['error' => 'login_error'], 200);

        //checking data
        $usercek = User::where('email', $request->email)->value('id');
        $cek = DB::table('model_has_roles')->where('model_id', $usercek)->count();

        //cek jika user tidak terdaftar maka akan memproses proses ini
        if($cek == 0){
            return response([
                    'status' => 'errory',
                    'error' => 'invalid.credentials',
                    'msg' => 'Invalid Credentials.'
                ], 401);
        }
        $credentials = $request->only('email', 'password');
        if ( ! $token = $this->guard()->attempt($credentials)) {
        // if ( ! $token = JWTAuth::attempt($credentials)) {
                return response([
                    'status' => 'errors',
                    'error' => 'invalid.credentials',
                    'msg' => 'Invalid Credentials.'
                ], 401);
        }
        return response([
                'status' => 'success',
                'token' => $token
        ],200)
            ->header('Authorization', $token);
    }
    /**
     * Logout User
     */
    public function logout()
    {
        $this->guard()->logout();
        return response()->json([
            'status' => 'success',
            'msg' => 'Logged out Successfully.'
        ], 200);
    }
    /**
     * Get authenticated user
     */
    public function user(Request $request)
    {
        $user = User::where('id',Auth::user()->id)->first();
        $permission = DB::table('role_has_permissions')
                ->select('permissions.name')
                ->join('permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                ->join('roles', 'roles.id', '=', 'role_has_permissions.role_id')
                ->join('model_has_roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->where('model_id', $user->id)
                ->pluck('name')->toArray();

        $role = DB::table('model_has_roles')->select('role_id')->where('model_id', $user->id)->value('role_id');
        $data = DB::table('role_has_menus')->where('id_role', $role)->value('menus');
        $data = json_decode($data);
        // $setSession = Session::put($user);
        return response()->json([
            'status' => 'success',
            'data' => json_encode($permission, true),
            'role' => json_encode($data, true)
        ]);
    }


    public function getAuthenticatedUser()
    {
            try {

                    if (! $user = JWTAuth::parseToken()->authenticate()) {
                            return response()->json(['user_not_found'], 404);
                    }

            } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

                    return response()->json(['token_expired'], $e->getStatusCode());

            } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

                    return response()->json(['token_invalid'], $e->getStatusCode());

            } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

                    return response()->json(['token_absent'], $e->getStatusCode());

            }

            return response()->json(compact('user'));
    }
    
    /**
     * Refresh JWT token
     */
    public function refresh()
    {
        // dd(JWTAuth::getToken());
        // JWTAuth::removeToken('eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC92MVwvYXV0aFwvcmVmcmVzaCIsImlhdCI6MTYwMDE0NDgyMCwiZXhwIjoxNjAwMTQ4NDk3LCJuYmYiOjE2MDAxNDQ4OTcsImp0aSI6Ik5IdFpBemRGMmdpcDZTTlciLCJzdWIiOjUsInBydiI6Ijg3ZTBhZjFlZjlmZDE1ODEyZmRlYzk3MTUzYTE0ZTBiMDQ3NTQ2YWEifQ.2oNTodzSiUmg7hhSiyacXJgn9JYYiek3tnXK_d5odLs')
        if ($token = $this->guard()->refresh()) {
            return response()
                ->json(['status' => 'successs'], 200)
                ->header('Authorization', $token);
        }
        return response()->json(['error' => 'refresh_token_error'], 401);
    }
    /**
     * Return auth guard
     */
    private function guard()
    {
        return Auth::guard();
    }
}