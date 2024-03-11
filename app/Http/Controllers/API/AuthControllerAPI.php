<?php

        namespace App\Http\Controllers\API;

        use App\Http\Controllers\Controller;
        use Illuminate\Http\Request;
        use App\Models\User; // Ubah User ke nama model yang digunakan jika berbeda
        use Illuminate\Support\Facades\Validator;
        use Illuminate\Support\Facades\Auth;

        class AuthControllerAPI extends Controller
        {

            public function profil(Request $request)  {
                $user = User::where('id', $request->id)->first();
                return response()->json([
                    'success' => true,
                    'data' => $user
                ], 201);
            }

            public function register(Request $request)
            {
                // Validasi input
                $validator = Validator::make($request->all(), [
                    'name' => 'required|string|max:255',
                    'email' => 'required|string|email|max:255|unique:users',
                    'password' => 'required|string|min:8',
                ]);

                if ($validator->fails()) {
                    return response()->json(['error' => $validator->errors()], 422);
                }

                // Simpan data user ke database
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => bcrypt($request->password),
                ]);

                return response()->json(['message' => 'User berhasil didaftarkan', 'data' => $user], 201);
            }


            public function login(Request $request)
            {
                $validator = Validator::make($request->all(), [
                    'email' => 'required|email',
                    'password' => 'required',
                ]);
                if ($validator->fails()) {
                    return response()->json(['error' => $validator->errors()->all()]);
                }

                if (Auth::guard()->attempt(['email' => $request->email, 'password' => $request->password])){
                    $user = User::select('id', 'name', 'email','alamat','nohp','foto')->find(auth()->guard()->user()->id);
                    $success = $user;
                    $token =  $user->createToken('mytoken')->plainTextToken;
                    return response()->json([
                        'success' => true,
                        'message' => 'Login success!',
                        'data' => $success,
                    ], 201);
                }else{
                    return response()->json([
                        'success' => false,
                        'message' => 'Login Failed!',
                    ], 401);
                }
            }


            public function logout(Request $request)
            {
                // Revoke semua token yang terkait dengan pengguna saat ini
                $request->user()->tokens()->delete();

                return response()->json(['message' => 'Logout berhasil']);
            }
        }
        