<?php

            namespace App\Http\Controllers\API;

            use App\Http\Controllers\Controller;
            use Illuminate\Http\Request;
            use App\Models\CrudTransaksi;
            use Illuminate\Support\Facades\Validator;

            class CrudTransaksiControllerAPI extends Controller
            {

                public function index()
                {
                    $data = CrudTransaksi::all();
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

                    // Simpan data CrudTransaksi ke database
                    $data = CrudTransaksi::create($request->all());

                    return response()->json(['message' => 'CrudTransaksi berhasil disimpan', 'data' => $data], 201);
                }

                /**
                 * Display the specified resource.
                 *
                 * @param  int  $id
                 * @return \Illuminate\Http\Response
                 */
                public function show($id)
                {
                    $data = CrudTransaksi::findOrFail($id);
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

                    // Update data CrudTransaksi
                    $data = CrudTransaksi::findOrFail($id);
                    $data->update($request->all());

                    return response()->json(['message' => 'CrudTransaksi berhasil diperbarui', 'data' => $data]);
                }

                /**
                 * Remove the specified resource from storage.
                 *
                 * @param  int  $id
                 * @return \Illuminate\Http\Response
                 */
                public function destroy($id)
                {
                    $data = CrudTransaksi::findOrFail($id);
                    $data->delete();

                    return response()->json(['message' => 'CrudTransaksi berhasil dihapus']);
                }
            }
            