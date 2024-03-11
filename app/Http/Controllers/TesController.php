<?php

        namespace App\Http\Controllers;

        use Illuminate\Http\Request;
        use App\Models\Tes;
        use Illuminate\Support\Facades\Validator;
        use Illuminate\Support\Facades\DB; // Tambahkan penggunaan DB
        use Illuminate\Database\Eloquent\SoftDeletes;

        class TesController extends Controller
        {

            public function index(Request $request)
            {
                // Ambil data filter dari request POST
                $filters = $request->all();

                // Inisialisasi query builder
                $query = DB::table('tes');

                // Tentukan kolom primary key
                $primaryKey = '';

                // Tentukan kolom yang akan ditampilkan
                $columns = [];

                foreach ($filters as $column => $value) {
                    if ($value) {
                        if ($column === 'id') {
                            $primaryKey = $value;
                        } else {
                            $columns[] = $column;
                            $query->where($column, 'like', '%' . $value . '%');
                        }
                    }
                }

                // Eksekusi query untuk mengambil data
                $data = $query->get($columns);

                // Ubah format data sesuai dengan yang diharapkan oleh DataTables
                $formattedData = [];

                foreach ($data as $row) {
                    $checkbox = '<input type="checkbox" value="' . $row->$primaryKey . '">';
                    $actions = '
                        <div class="btn-group">
                            <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-cogs"></i> Aksi
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="' . route('produk.edit', $row->$primaryKey) . '">Edit</a>
                                <a class="dropdown-item" href="' . route('produk.show', $row->$primaryKey) . '">Detail</a>
                                <div class="dropdown-divider"></div>
                                <button type="button" onclick="deleteData(\' ' . route('produk.destroy', $row->$primaryKey) . ' \')" class="dropdown-item">Delete</button>
                            </div>
                        </div>
                    ';

                    $formattedData[] = array_merge((array) $row, ['checkbox' => $checkbox, 'aksi' => $actions]);
                }

                return response()->json([
                    'data' => $formattedData
                ]);
            }


            /**
             * Menampilkan form untuk membuat data tes baru.
             */
            public function create()
            {
                return view('Tes/TesController.create');
            }

            /**
             * Menyimpan data tes baru ke database.
             */
            public function store(Request $request)
            {
                // Validasi input
                $validator = Validator::make($request->all(), [
                    // Lakukan validasi sesuai dengan struktur kolom yang dibuat
                    'tes' => 'required','bnbn' => 'required',
            ]);

                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }

                // Simpan data tes ke database
                Tes::create($request->all());

                return redirect()->route('Tes/TesController.index')->with('success', 'tes berhasil disimpan.');
            }

            /**
             * Menampilkan detail data tes.
             */
            public function show($id)
            {
                $data = Tes::findOrFail($id);
                return view('Tes/TesController.show', compact('$\data'));
            }

            /**
             * Menampilkan form untuk mengedit data tes.
             */
            public function edit($id)
            {
                $data = Tes::findOrFail($id);
                return view('Tes/TesController.edit', compact('$data'));
            }

            /**
             * Menyimpan perubahan pada data tes ke database.
             */
            public function update(Request $request, $id)
            {
                // Validasi input
                $validator = Validator::make($request->all(), [
                    'tes' => 'required','bnbn' => 'required',
                ]);

                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }

                // Update data tes
                $data = Tes::findOrFail($id);
                $data->update($request->all());

                return redirect()->route('Tes/TesController.index')->with('success', 'tes berhasil diperbarui.');
            }

            /**
             * Menghapus data tes dari database.
             */
            public function destroy($id)
            {
                $data = Tes::findOrFail($id);
                $data->delete();

                return redirect()->route('Tes/TesController.index')->with('success', 'tes berhasil dihapus.');
            }
        }
        