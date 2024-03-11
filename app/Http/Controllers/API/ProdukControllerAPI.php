<?php

            namespace App\Http\Controllers\API;

            use App\Http\Controllers\Controller;
            use Illuminate\Http\Request;
            use App\Models\Produk;
            use Illuminate\Support\Facades\Validator;

            class ProdukControllerAPI extends Controller
            {

                public function index()
                {
                    $data = Produk::all();
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

                    // Simpan data produk ke database
                    $data = Produk::create($request->all());

                    return response()->json(['message' => 'produk berhasil disimpan', 'data' => $data], 201);
                }

                /**
                 * Display the specified resource.
                 *
                 * @param  int  $id
                 * @return \Illuminate\Http\Response
                 */
                public function show($id)
                {
                    $data = Produk::findOrFail($id);
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

                    // Update data produk
                    $data = Produk::findOrFail($id);
                    $data->update($request->all());

                    return response()->json(['message' => 'produk berhasil diperbarui', 'data' => $data]);
                }

                /**
                 * Remove the specified resource from storage.
                 *
                 * @param  int  $id
                 * @return \Illuminate\Http\Response
                 */
                public function destroy($id)
                {
                    $data = Produk::findOrFail($id);
                    $data->delete();

                    return response()->json(['message' => 'produk berhasil dihapus']);
                }
            }
            