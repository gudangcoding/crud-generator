<?php

            namespace App\Http\Controllers\API;

            use App\Http\Controllers\Controller;
            use Illuminate\Http\Request;
            use App\Models\Barang;
            use Illuminate\Support\Facades\Validator;

            class BarangControllerAPI extends Controller
            {

                public function index()
                {
                    $data = Barang::all();
                    return response()->json($data);
                }


                public function store(Request $request)
                {
                    // Validasi input
                    $validator = Validator::make($request->all(), [
                        'tes' => 'required',
'bnbn' => 'required',

                    ]);

                    if ($validator->fails()) {
                        return response()->json(['error' => $validator->errors()], 422);
                    }

                    // Simpan data barang ke database
                    $data = Barang::create($request->all());

                    return response()->json(['message' => 'barang berhasil disimpan', 'data' => $data], 201);
                }

                /**
                 * Display the specified resource.
                 *
                 * @param  int  $id
                 * @return \Illuminate\Http\Response
                 */
                public function show($id)
                {
                    $data = Barang::findOrFail($id);
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
                        'tes' => 'required',
'bnbn' => 'required',

                    ]);

                    if ($validator->fails()) {
                        return response()->json(['error' => $validator->errors()], 422);
                    }

                    // Update data barang
                    $data = Barang::findOrFail($id);
                    $data->update($request->all());

                    return response()->json(['message' => 'barang berhasil diperbarui', 'data' => $data]);
                }

                /**
                 * Remove the specified resource from storage.
                 *
                 * @param  int  $id
                 * @return \Illuminate\Http\Response
                 */
                public function destroy($id)
                {
                    $data = Barang::findOrFail($id);
                    $data->delete();

                    return response()->json(['message' => 'barang berhasil dihapus']);
                }
            }
            