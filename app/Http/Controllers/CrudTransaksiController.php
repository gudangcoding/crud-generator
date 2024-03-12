<?php

        namespace App\Http\Controllers;

        use Illuminate\Http\Request;
        use App\Models\CrudTransaksi;
        use Illuminate\Support\Facades\Validator;
        use Illuminate\Support\Facades\DB; // Tambahkan penggunaan DB
        use Illuminate\Database\Eloquent\SoftDeletes;
        use Maatwebsite\Excel\Facades\Excel;
        use Barryvdh\DomPDF\Facade as PDF;
        use App\Pdfs\CrudTransaksiPdf; // Sesuaikan dengan nama kelas PDF Anda
        use App\Imports\CrudTransaksiImport;

        class CrudTransaksiController extends Controller
        {

            public function index(Request $request)
            {
                // Ambil data filter dari request POST
                $filters = $request->all();

                // Inisialisasi query builder
                $query = DB::table('CrudTransaksi');

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
             * Menampilkan form untuk membuat data CrudTransaksi baru.
             */
            public function create()
            {
                return view('CrudTransaksi/CrudTransaksiController.create');
            }

            /**
             * Menyimpan data CrudTransaksi baru ke database.
             */
            public function store(Request $request)
            {
                // Validasi input
                $validator = Validator::make($request->all(), [
                    // Lakukan validasi sesuai dengan struktur kolom yang dibuat
                    'nama' => 'required','email' => 'required','alamat' => 'required',
            ]);

                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }

                // Simpan data CrudTransaksi ke database
                CrudTransaksi::create($request->all());

                return redirect()->route('CrudTransaksi/CrudTransaksiController.index')->with('success', 'CrudTransaksi berhasil disimpan.');
            }

            /**
             * Menampilkan detail data CrudTransaksi.
             */
            public function show($id)
            {
                $data = CrudTransaksi::findOrFail($id);
                return view('CrudTransaksi/CrudTransaksiController.show', compact('$\data'));
            }

            /**
             * Menampilkan form untuk mengedit data CrudTransaksi.
             */
            public function edit($id)
            {
                $data = CrudTransaksi::findOrFail($id);
                return view('CrudTransaksi/CrudTransaksiController.edit', compact('$data'));
            }

            /**
             * Menyimpan perubahan pada data CrudTransaksi ke database.
             */
            public function update(Request $request, $id)
            {
                // Validasi input
                $validator = Validator::make($request->all(), [
                    'nama' => 'required','email' => 'required','alamat' => 'required',
                ]);

                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }

                // Update data CrudTransaksi
                $data = CrudTransaksi::findOrFail($id);
                $data->update($request->all());

                return redirect()->route('CrudTransaksi/CrudTransaksiController.index')->with('success', 'CrudTransaksi berhasil diperbarui.');
            }

            /**
             * Menghapus data CrudTransaksi dari database.
             */
            public function destroy($id)
            {
                $data = CrudTransaksi::findOrFail($id);
                $data->delete();

                return redirect()->route('CrudTransaksi/CrudTransaksiController.index')->with('success', 'CrudTransaksi berhasil dihapus.');
            }

            public function import(Request $request)
            {
                $request->validate([
                    'file' => 'required|file|mimes:xlsx,xls',
                ]);

                $file = $request->file('file');

                Excel::import(new CrudTransaksiImport, $file);

                return redirect()->back()->with('success', 'Data CrudTransaksi berhasil diimpor.');
            }
            public function generatePdf()
            {
                // Ambil data yang ingin Anda tampilkan dalam PDF
                $data =CrudTransaksi::all(); // Contoh pengambilan semua data dari model

                // Buat instance dari kelas PDF
                $pdfGenerator = new CrudTransaksiPdf();

                // Panggil metode generatePdf dengan menyediakan data
                return $pdfGenerator->generatePdf($data);
            }

        }
        