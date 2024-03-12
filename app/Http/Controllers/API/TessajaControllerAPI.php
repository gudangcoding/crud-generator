<?php

            namespace App\Http\Controllers\API;

            use App\Http\Controllers\Controller;
            use Illuminate\Http\Request;
            use App\Models\Tessaja;
            use Illuminate\Support\Facades\Validator;

            class TessajaControllerAPI extends Controller
            {

                public function index()
                {
                    $data = Tessaja::all();
                    return response()->json($data);
                }


                public function store(Request $request)
                {
                    // Validasi input
                    $validator = Validator::make($request->all(), [
                        'nama' => 'required',
'email' => 'required',
'alamat' => 'required',

                    ]);

                    if ($validator->fails()) {
                        return response()->json(['error' => $validator->errors()], 422);
                    }

                    // Simpan data tessaja ke database
                    $data = Tessaja::create($request->all());

                    return response()->json(['message' => 'tessaja berhasil disimpan', 'data' => $data], 201);
                }

                /**
                 * Display the specified resource.
                 *
                 * @param  int  $id
                 * @return \Illuminate\Http\Response
                 */
                public function show($id)
                {
                    $data = Tessaja::findOrFail($id);
                    return response()->json($data);
                }

                /**
                 * Update the specified resource in storage.
                 *
                 * @param  \Illuminate\Http\Request  $request
                 * @param  int  $id
                 * @return \Illuminate\Http\Response
                 */
                public function update(Request $request, $id)
                {
                    // Validasi input
                    $validator = Validator::make($request->all(), [
                        'nama' => 'required',
'email' => 'required',
'alamat' => 'required',

                    ]);

                    if ($validator->fails()) {
                        return response()->json(['error' => $validator->errors()], 422);
                    }

                    // Update data tessaja
                    $data = Tessaja::findOrFail($id);
                    $data->update($request->all());

                    return response()->json(['message' => 'tessaja berhasil diperbarui', 'data' => $data]);
                }

                /**
                 * Remove the specified resource from storage.
                 *
                 * @param  int  $id
                 * @return \Illuminate\Http\Response
                 */
                public function destroy($id)
                {
                    $data = Tessaja::findOrFail($id);
                    $data->delete();

                    return response()->json(['message' => 'tessaja berhasil dihapus']);
                }
            }
            