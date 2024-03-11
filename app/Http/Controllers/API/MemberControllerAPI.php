<?php

            namespace App\Http\Controllers\API;

            use App\Http\Controllers\Controller;
            use Illuminate\Http\Request;
            use App\Models\Member;
            use Illuminate\Support\Facades\Validator;

            class MemberControllerAPI extends Controller
            {

                public function index()
                {
                    $data = Member::all();
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

                    // Simpan data member ke database
                    $data = Member::create($request->all());

                    return response()->json(['message' => 'member berhasil disimpan', 'data' => $data], 201);
                }

                /**
                 * Display the specified resource.
                 *
                 * @param  int  $id
                 * @return \Illuminate\Http\Response
                 */
                public function show($id)
                {
                    $data = Member::findOrFail($id);
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

                    // Update data member
                    $data = Member::findOrFail($id);
                    $data->update($request->all());

                    return response()->json(['message' => 'member berhasil diperbarui', 'data' => $data]);
                }

                /**
                 * Remove the specified resource from storage.
                 *
                 * @param  int  $id
                 * @return \Illuminate\Http\Response
                 */
                public function destroy($id)
                {
                    $data = Member::findOrFail($id);
                    $data->delete();

                    return response()->json(['message' => 'member berhasil dihapus']);
                }
            }
            