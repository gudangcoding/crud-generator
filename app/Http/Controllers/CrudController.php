<?php

namespace App\Http\Controllers;

use Illuminate\Database\Migrations\Migrator;
use Illuminate\Support\Facades\File; //membaca model
use Illuminate\Support\Facades\Schema; //membaca kolom migration
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan; //agar bisa menggunakan perintah artisan


class CrudController extends Controller
{
    function index()
    {

        // Mendapatkan path ke direktori model
        $modelPath = app_path('Models');
        // Mendapatkan daftar file dalam direktori model
        $modelFiles = File::files($modelPath);

        // Memproses setiap file dan mendapatkan nama model
        $models = [];
        foreach ($modelFiles as $file) {
            $fileName = pathinfo($file, PATHINFO_FILENAME);
            $models[] = $fileName;
        }

        // Ambil informasi kolom dari tabel items
        $columns = Schema::getColumnListing($models[0]);

        // echo "Hello World";
        return view('crud.index', compact('models', 'columns'));
    }

    function generate(Request $request)
    {
        // Mengambil data yang diterima dari form
        $namaTabel = $request->input('nama_tabel');
        $namaModel = $request->input('nama_model');
        $namaController = $request->input('nama_controller');
        $folderController = $request->input('folder_controller');
        // Ambil data kolom yang dibuat dari form
        $kolom = $request->input('nama_kolom');
        // Ambil data relasi dan acuan yang dibuat dari form
        $relasi = $request->input('relasi');
        $acuan = $request->input('acuan');
        //ppanggil fungsi yang sudah dibuat
        $this->generateMigration($namaTabel, $kolom, $acuan);
        $this->generateModel($namaTabel, $kolom, $acuan);
        $this->generateController($namaTabel, $namaModel, $namaController, $folderController);
        $this->generateRouteWeb($namaController, $folderController);
        $this->generateRouteAPI($namaController, $folderController);
        $this->generateViewIndex($namaTabel, $kolom, $acuan);
        $this->generateViewCreate($namaTabel, $kolom, $acuan);
        $this->generateViewEdit($namaTabel, $kolom, $acuan);
        $this->generateViewShow($namaTabel, $kolom, $acuan);
    }

    function generateMigration($namaTabel, $kolom, $acuan)
    {
        //membuat migration
        $migrationContent = "<?php

            use Illuminate\Database\Migrations\Migration;
            use Illuminate\Database\Schema\Blueprint;
            use Illuminate\Support\Facades\Schema;

            class Create" . ucfirst($namaTabel) . "Table extends Migration
            {
                public function up()
                {
                    Schema::create('$namaTabel', function (Blueprint \$table) {
                        \$table->id();
                        \$table->timestamps();
                        // Buat kolom berdasarkan data kolom yang diterima
                        foreach ($kolom as \$namaKolom) {
                            if (in_array(\$namaKolom, ['" . implode("', '", $acuan) . "'])) {
                                \$table->unsignedBigInteger('\${\$namaKolom}_id');
                                \$table->foreign('\${\$namaKolom}_id')->references('id')->on('\${\$namaKolom}s')->onDelete('cascade');
                            } else {
                                \$table->string(\$namaKolom);
                            }
                        }
                    });
                }

                public function down()
                {
                    Schema::dropIfExists('$namaTabel');
                }
            }
            ";

        // Simpan migration ke dalam direktori migrations
        $migrationFileName = date('Y_m_d_His') . '_create_' . strtolower($namaTabel) . '_table.php';
        $migrationPath = database_path('migrations/' . $migrationFileName);
        File::put($migrationPath, $migrationContent);
    }


    function generateModel($namaModel, $kolom, $relasi)
    {
        // Membuat model dengan relasi
        $modelContent = "<?php

        namespace App\Models;

        use Illuminate\Database\Eloquent\Model;

        class $namaModel extends Model
        {
            protected \$fillable = [" . implode(', ', array_map(function ($kolom) {
            return "'$kolom'";
        }, $kolom)) . "];

            // Definisikan relasi dengan model
            " . implode("\n", array_map(function ($relasi) {
            return "public function $relasi()
            {
                return \$this->belongsTo($relasi::class);
            }";
        }, $relasi)) . "
        }
        ";

        // Simpan model ke dalam direktori Models
        $modelFileName = $namaModel . '.php';
        $modelPath = app_path('Models/' . $modelFileName);
        File::put($modelPath, $modelContent);
    }

    function generateController($namaTabel, $namaModel, $namaController, $folderController)
    {
        // Membuat controller dengan fungsi CRUD dan validasi
        $controllerContent = "<?php

        namespace App\Http\Controllers;

        use Illuminate\Http\Request;
        use App\Models\\$namaModel;
        use Validator;
        use DB; // Tambahkan penggunaan DB

        class $namaController extends Controller
        {
            /**
             * Menampilkan semua data $namaTabel.
             */
            public function index(Request $" . "request)
            {
                // Ambil data filter dari request POST
                $" . "filters = $" . "request->all();

                // Inisialisasi query builder
                $" . "query = DB::table('$namaTabel');

                // Terapkan filter jika ada
                foreach ($" . "filters as $" . "column => $" . "value) {
                    if ($" . "value) {
                        $" . "query->where($" . "column, 'like', '%' . $" . "value . '%');
                    }
                }

                // Eksekusi query untuk mengambil data
                $" . "data = $" . "query->get();

                // Kembalikan data sebagai respons JSON
                return response()->json($" . "data);
            }

            /**
             * Menampilkan form untuk membuat data $namaTabel baru.
             */
            public function create()
            {
                return view('$folderController.create');
            }

            /**
             * Menyimpan data $namaTabel baru ke database.
             */
            public function store(Request $" . "request)
            {
                // Validasi input
                $" . "validator = Validator::make($" . "request->all(), [
                    // Lakukan validasi sesuai dengan struktur kolom yang dibuat
                ]);

                if ($" . "validator->fails()) {
                    return redirect()->back()->withErrors($" . "validator)->withInput();
                }

                // Simpan data $namaTabel ke database
                $namaModel::create($" . "request->all());

                return redirect()->route('$folderController.index')->with('success', '$namaTabel berhasil disimpan.');
            }

            /**
             * Menampilkan detail data $namaTabel.
             */
            public function show($" . "id)
            {
                $" . "data = $namaModel::findOrFail($" . "id);
                return view('$folderController.show', compact('$\data'));
            }

            /**
             * Menampilkan form untuk mengedit data $namaTabel.
             */
            public function edit($" . "id)
            {
                $" . "data = $namaModel::findOrFail($" . "id);
                return view('$folderController.edit', compact('\$data'));
            }

            /**
             * Menyimpan perubahan pada data $namaTabel ke database.
             */
            public function update(Request $" . "request, $" . "id)
            {
                // Validasi input
                $" . "validator = Validator::make($" . "request->all(), [
                    // Lakukan validasi sesuai dengan struktur kolom yang dibuat
                ]);

                if ($" . "validator->fails()) {
                    return redirect()->back()->withErrors($" . "validator)->withInput();
                }

                // Update data $namaTabel
                $" . "data = $namaModel::findOrFail($" . "id);
                $" . "data->update($" . "request->all());

                return redirect()->route('$folderController.index')->with('success', '$namaTabel berhasil diperbarui.');
            }

            /**
             * Menghapus data $namaTabel dari database.
             */
            public function destroy($" . "id)
            {
                $" . "data = $namaModel::findOrFail($" . "id);
                $" . "data->delete();

                return redirect()->route('$folderController.index')->with('success', '$namaTabel berhasil dihapus.');
            }
        }
        ";

        // Simpan controller ke dalam direktori Controllers
        $controllerFileName = $namaController . '.php';
        $controllerPath = app_path('Http/Controllers/' . $controllerFileName);
        File::put($controllerPath, $controllerContent);
    }

    function generateControllerAPI($namaTabel, $namaModel, $namaController)
    {
        // Membuat API controller
        $apiControllerContent = "<?php

            namespace App\Http\Controllers\API;

            use App\Http\Controllers\Controller;
            use Illuminate\Http\Request;
            use App\Models\\$namaModel;
            use Validator;

            class {$namaController}API extends Controller
            {
                /**
                 * Display a listing of the resource.
                 *
                 * @return \Illuminate\Http\Response
                 */
                public function index()
                {
                    $" . "data = $namaModel::all();
                    return response()->json($" . "data);
                }

                /**
                 * Store a newly created resource in storage.
                 *
                 * @param  \Illuminate\Http\Request  $" . "request
                 * @return \Illuminate\Http\Response
                 */
                public function store(Request $" . "request)
                {
                    // Validasi input
                    $" . "validator = Validator::make($" . "request->all(), [
                        // Lakukan validasi sesuai dengan struktur kolom yang dibuat
                    ]);

                    if ($" . "validator->fails()) {
                        return response()->json(['error' => $" . "validator->errors()], 422);
                    }

                    // Simpan data $namaTabel ke database
                    $" . "data = $namaModel::create($" . "request->all());

                    return response()->json(['message' => '$namaTabel berhasil disimpan', 'data' => $" . "data], 201);
                }

                /**
                 * Display the specified resource.
                 *
                 * @param  int  $" . "id
                 * @return \Illuminate\Http\Response
                 */
                public function show($" . "id)
                {
                    $" . "data = $namaModel::findOrFail($" . "id);
                    return response()->json($" . "data);
                }

                /**
                 * Update the specified resource in storage.
                 *
                 * @param  \Illuminate\Http\Request  $" . "request
                 * @param  int  $" . "id
                 * @return \Illuminate\Http\Response
                 */
                public function update(Request $" . "request, $" . "id)
                {
                    // Validasi input
                    $" . "validator = Validator::make($" . "request->all(), [
                        // Lakukan validasi sesuai dengan struktur kolom yang dibuat
                    ]);

                    if ($" . "validator->fails()) {
                        return response()->json(['error' => $" . "validator->errors()], 422);
                    }

                    // Update data $namaTabel
                    $" . "data = $namaModel::findOrFail($" . "id);
                    $" . "data->update($" . "request->all());

                    return response()->json(['message' => '$namaTabel berhasil diperbarui', 'data' => $" . "data]);
                }

                /**
                 * Remove the specified resource from storage.
                 *
                 * @param  int  $" . "id
                 * @return \Illuminate\Http\Response
                 */
                public function destroy($" . "id)
                {
                    $" . "data = $namaModel::findOrFail($" . "id);
                    $" . "data->delete();

                    return response()->json(['message' => '$namaTabel berhasil dihapus']);
                }
            }
            ";

        // Simpan API controller ke dalam direktori Controllers/API
        $apiControllerFileName = "{$namaController}API.php";
        $apiControllerPath = app_path('Http/Controllers/API/' . $apiControllerFileName);
        File::put($apiControllerPath, $apiControllerContent);
    }

    function generateRouteWeb($namaController, $folderController)
    {
        // Generate routes untuk web
        $routeWebContent = "
            use App\Http\Controllers\\{$namaController};
            // Web routes
            Route::resource('$folderController', {$namaController}::class);
            ";

        // Simpan route ke dalam file web.php
        $routePath = base_path('routes/web.php');
        File::append($routePath, $routeWebContent);
    }

    function generateRouteAPI($namaController, $folderController)
    {
        // Generate routes untuk web
        $routeWebContent = "
            use App\Http\Controllers\\{$namaController};
            // Web routes
            Route::resource('$folderController', {$namaController}::class);
            ";
        // Generate routes untuk API
        $routeApiContent = "
        use App\Http\Controllers\API\\{$namaController}API;
        // API routes
        Route::resource('api/$folderController', {$namaController}API::class);
        ";

        // Simpan route ke dalam file web.php
        $routePath = base_path('routes/web.php');
        File::append($routePath, $routeWebContent);
        // Simpan route ke dalam file api.php
        $routePath = base_path('routes/api.php');
        File::append($routePath, $routeApiContent);
    }

    function generateViewIndex($namaTabel, $folderController, $kolom)
    {
        $namaModal = strtolower($namaTabel) . '.form.blade.php';
        // Contoh, membuat view blade
        $viewContent = "@extends('layouts.app')

        @section('content')
            <div class=\"card\">
                <div class=\"card-header\">
                    <h1>Data $namaTabel</h1>
                </div>
                <div class=\"card-body\">
                    <form id=\"filterForm\">
                        @csrf
                        <div class=\"form-row\">
                            @foreach ($kolom as \$namaKolom)
                                <div class=\"form-group col\">
                                    <input type=\"text\" name=\"{{ \$namaKolom }}\" class=\"form-control\" placeholder=\"Filter {{ ucfirst(\$namaKolom) }}\">
                                </div>
                            @endforeach
                            <div class=\"form-group col\">
                                <button type=\"button\" id=\"applyFilter\" class=\"btn btn-primary\">Apply Filter</button>
                            </div>
                        </div>
                    </form>
                    <table id=\"dataTable\" class=\"table table-striped table-bordered\">
                        <thead>
                            <tr>";
        // Tambahkan kolom ke dalam header table
        foreach ($kolom as $namaKolom) {
            $viewContent .= "
                                <th>{{ ucfirst('$namaKolom') }}</th>";
        }
        $viewContent .= "
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data akan dimasukkan di sini melalui JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>

            <script>
                $(document).ready(function() {
                    // Event handler untuk tombol Apply Filter
                    $('#applyFilter').on('click', function() {
                        applyFilter();
                    });

                    // Function untuk mengirimkan data filter ke server
                    function applyFilter() {
                        $.ajax({
                            url: '{{ route('data.index') }}',
                            type: 'POST',
                            data: $('#filterForm').serialize(),
                            success: function(data) {
                                table.clear().draw();
                                table.rows.add(data).draw();
                            }
                        });
                    }

                    // Inisialisasi DataTables
                    var table = $('#dataTable').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: {
                            url: '{{ route('data.index') }}',
                            type: 'POST', // Ganti tipe ke POST
                            data: function(d) {
                                d._token = '{{ csrf_token() }}'; // Sertakan CSRF token
                            },
                        },
                        columns: [";
        // Tambahkan konfigurasi kolom DataTables
        foreach ($kolom as $namaKolom) {
            $viewContent .= "
                                        { data: '$namaKolom', name: '$namaKolom' },";
        }
        $viewContent .= "
                        ],
                    });

                    // Menambahkan kolom filter secara dinamis
                    $('#dataTable thead th').each(function() {
                        var title = $(this).text();
                        $(this).html('<input type=\"text\" placeholder=\"Search ' + title + '\" />');
                    });

                    // Menerapkan filter
                    table.columns().every(function() {
                        var that = this;

                        $('input', this.header()).on('keyup change', function() {
                            if (that.search() !== this.value) {
                                that
                                    .search(this.value)
                                    .draw();
                            }
                        });
                    });
                });
            </script>
        @endsection";

        // Simpan view ke dalam direktori resources/views
        $viewFileName = strtolower($namaTabel) . '.blade.php';
        $viewPath = resource_path('views/' . $folderController . '/' . $viewFileName);
        File::put($viewPath, $viewContent);
    }

    function generateViewCreate($namaTabel, $folderController, $kolom)
    {
        $viewContent = "@extends('layouts.app')

        @section('content')
            <div class=\"card\">
                <div class=\"card-header\">
                    <h1>Create $namaTabel</h1>
                </div>
                <div class=\"card-body\">
                    <form id=\"createForm\">
                        @csrf
                        <div class=\"form-row\">
                            @foreach ($kolom as \$namaKolom)
                                <div class=\"form-group col\">
                                    <label for=\"{{ \$namaKolom }}\">{{ ucfirst(\$namaKolom) }}</label>
                                    <input type=\"text\" name=\"{{ \$namaKolom }}\" class=\"form-control\" id=\"{{ \$namaKolom }}\">
                                </div>
                            @endforeach
                        </div>
                        <button type=\"submit\" class=\"btn btn-primary\">Submit</button>
                    </form>
                </div>
            </div>

            <script>
                $(document).ready(function() {
                    $('#createForm').on('submit', function(e) {
                        e.preventDefault();
                        var formData = $(this).serialize();
                        $.ajax({
                            url: '{{ route('$folderController.store') }}',
                            type: 'POST',
                            data: formData,
                            success: function(response) {
                                $('#exampleModal').modal('hide');
                                // Tambahkan logika lainnya, seperti menampilkan pesan sukses atau mereset form
                            },
                            error: function(xhr) {
                                // Tambahkan logika untuk menangani kesalahan validasi atau lainnya
                            }
                        });
                    });
                });
            </script>
        @endsection";

        // Simpan view ke dalam direktori resources/views
        $viewFileName = 'create.blade.php';
        $viewPath = resource_path('views/' . $folderController . '/' . $viewFileName);
        File::put($viewPath, $viewContent);
    }
    function generateViewEdit($namaTabel, $folderController, $kolom)
    {
        $viewContent = "@extends('layouts.app')
            @section('content')
                <div class=\"card\">
                    <div class=\"card-header\">
                        <h1>Edit $namaTabel</h1>
                    </div>
                    <div class=\"card-body\">
                        <form id=\"editForm\">
                            @csrf
                            <div class=\"form-row\">
                                @foreach ($kolom as \$namaKolom)
                                    <div class=\"form-group col\">
                                        <label for=\"{{ \$namaKolom }}\">{{ ucfirst(\$namaKolom) }}</label>
                                        <input type=\"text\" name=\"{{ \$namaKolom }}\" class=\"form-control\" id=\"{{ \$namaKolom }}\">
                                    </div>
                                @endforeach
                            </div>
                            <button type=\"submit\" class=\"btn btn-primary\">Update</button>
                        </form>
                    </div>
                </div>

                <script>
                    $(document).ready(function() {
                        $('#editForm').on('submit', function(e) {
                            e.preventDefault();
                            var formData = $(this).serialize();
                            $.ajax({
                                url: '{{ route('$folderController.update', ['id' => $namaTabel->id]) }}',
                                type: 'PUT',
                                data: formData,
                                success: function(response) {
                                    $('#exampleModal').modal('hide');
                                    // Tambahkan logika lainnya, seperti menampilkan pesan sukses atau mereset form
                                },
                                error: function(xhr) {
                                    // Tambahkan logika untuk menangani kesalahan validasi atau lainnya
                                }
                            });
                        });
                    });
                </script>
            @endsection";

        // Simpan view ke dalam direktori resources/views
        $viewFileName = 'edit.blade.php';
        $viewPath = resource_path('views/' . $folderController . '/' . $viewFileName);
        File::put($viewPath, $viewContent);
    }
    function generateViewShow($namaTabel, $folderController, $kolom)
    {
        // Contoh, membuat view blade untuk show
        $viewContent = "@extends('layouts.app')

        @section('content')
            <div class=\"card\">
                <div class=\"card-header\">
                    <h1>Detail $namaTabel</h1>
                </div>
                <div class=\"card-body\">
                    <div class=\"table-responsive\">
                        <table class=\"table\">
                            <tbody>";
        foreach ($kolom as $namaKolom) {
            $viewContent .= "
                                <tr>
                                    <td>{{ ucfirst('$namaKolom') }}</td>
                                    <td>{{ $namaTabel->$namaKolom }}</td>
                                </tr>";
        }
        $viewContent .= "
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endsection";

        // Simpan view ke dalam direktori resources/views
        $viewFileName = 'show.blade.php';
        $viewPath = resource_path('views/' . $folderController . '/' . $viewFileName);
        File::put($viewPath, $viewContent);
    }
}