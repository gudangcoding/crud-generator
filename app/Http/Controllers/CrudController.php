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


        // echo json_encode($columns);
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
        $this->generateControllerWeb($namaTabel, $namaModel, $namaController, $folderController);
        $this->generateControllerAPI($namaTabel, $namaModel, $namaController, $folderController);
        $this->generateAuthApi($namaController, $folderController);
        $this->generateRouteWeb($namaController, $folderController);
        $this->generateRouteAPI($namaController, $folderController);
        $this->generateViewIndex($namaTabel, $kolom, $acuan);
        $this->generateViewCreate($namaTabel, $kolom, $acuan);
        $this->generateViewEdit($namaTabel, $kolom, $acuan);
        $this->generateViewShow($namaTabel, $kolom, $acuan);
        $this->generateFakeData($namaTabel, $kolom);
        $this->generatePostmanJson($namaTabel);
    }

    function generateMigration($namaTabel, $kolom, $acuan)
    {
        // Membuat migration
        $migrationContent = "<?php

        use Illuminate\Database\Migrations\Migration;
        use Illuminate\Database\Schema\Blueprint;
        use Illuminate\Support\Facades\Schema;
        use Ramsey\Uuid\Uuid;

        class Create" . ucfirst($namaTabel) . "Table extends Migration
        {
            public function up()
            {
                Schema::create('$namaTabel', function (Blueprint \$table) {
                    \$table->uuid('id')->primary();
                    \$table->timestamps();
                    \$table->softDeletes(); // Menambahkan soft deletes
                    // Buat kolom berdasarkan data kolom yang diterima
                    foreach ($kolom as \$namaKolom => \$detailKolom) {
                        \$type = \$detailKolom['type'];
                        \$length = isset(\$detailKolom['length']) ? \$detailKolom['length'] : null;
                        \$default = isset(\$detailKolom['default']) ? \$detailKolom['default'] : null;

                        if (in_array(\$namaKolom, ['" . implode("', '", $acuan) . "'])) {
                            \$table->unsignedBigInteger('\${\$namaKolom}_id');
                            \$table->foreign('\${\$namaKolom}_id')->references('id')->on('\${\$namaKolom}s')->onDelete('cascade');
                        } else {
                            // Tentukan tipe dan panjang data untuk kolom
                            switch (\$type) {
                                case 'CHAR':
                                    \$table->char(\$namaKolom, \$length);
                                    break;
                                case 'VARCHAR':
                                    \$table->string(\$namaKolom, \$length);
                                    break;
                                case 'TEXT':
                                    \$table->text(\$namaKolom);
                                    break;
                                case 'INT':
                                    \$table->integer(\$namaKolom);
                                    break;
                                case 'BIGINT':
                                    \$table->bigInteger(\$namaKolom);
                                    break;
                                case 'FLOAT':
                                    \$table->float(\$namaKolom);
                                    break;
                                case 'DOUBLE':
                                    \$table->double(\$namaKolom);
                                    break;
                                case 'DECIMAL':
                                    \$table->decimal(\$namaKolom);
                                    break;
                                case 'DATE':
                                    \$table->date(\$namaKolom);
                                    break;
                                case 'TIME':
                                    \$table->time(\$namaKolom);
                                    break;
                                case 'DATETIME':
                                    \$table->dateTime(\$namaKolom);
                                    break;
                                case 'TIMESTAMP':
                                    \$table->timestamp(\$namaKolom);
                                    break;
                                case 'ENUM':
                                    // Pastikan Anda memiliki array opsi untuk ENUM
                                    \$table->enum(\$namaKolom, \$length)->default(\$default);
                                    break;
                                default:
                                    // Jika tipe tidak dikenali, gunakan string sebagai default
                                    \$table->string(\$namaKolom);
                            }
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


    function generateFakeData($namaTabel, $kolom)
    {
        // Membuat factory
        $factoryContent = "<?php

            use Faker\Generator as Faker;

            \$factory->define(App\\Models\\" . ucfirst($namaTabel) . "::class, function (Faker \$faker) {
                return [";

        // Menambahkan definisi factory sesuai dengan tipe data kolom
        foreach ($kolom as $namaKolom => $detailKolom) {
            $type = $detailKolom['type'];

            switch ($type) {
                case 'CHAR':
                case 'VARCHAR':
                case 'TEXT':
                    $factoryContent .= "\n            '$namaKolom' => \$faker->sentence(),";
                    break;
                case 'INT':
                case 'BIGINT':
                    $factoryContent .= "\n            '$namaKolom' => \$faker->numberBetween(1, 100),";
                    break;
                case 'FLOAT':
                case 'DOUBLE':
                case 'DECIMAL':
                    $factoryContent .= "\n            '$namaKolom' => \$faker->randomFloat(2, 0, 100),";
                    break;
                case 'DATE':
                    $factoryContent .= "\n            '$namaKolom' => \$faker->date(),";
                    break;
                case 'TIME':
                    $factoryContent .= "\n            '$namaKolom' => \$faker->time(),";
                    break;
                case 'DATETIME':
                case 'TIMESTAMP':
                    $factoryContent .= "\n            '$namaKolom' => \$faker->dateTime(),";
                    break;
                case 'ENUM':
                    // Pastikan Anda memiliki array opsi untuk ENUM
                    $enumOptions = implode("', '", $detailKolom['length']);
                    $factoryContent .= "\n            '$namaKolom' => \$faker->randomElement(['$enumOptions']),";
                    break;
                default:
                    // Jika tipe tidak dikenali, gunakan string sebagai default
                    $factoryContent .= "\n            '$namaKolom' => \$faker->word(),";
            }
        }

        $factoryContent .= "
                ];
            });
            ";

        // Simpan factory ke dalam direktori factories
        $factoryFileName = ucfirst($namaTabel) . "Factory.php";
        $factoryPath = database_path('factories/' . $factoryFileName);
        File::put($factoryPath, $factoryContent);

        // Tambahkan factory ke DatabaseSeeder.php
        $databaseSeederPath = database_path('seeders/DatabaseSeeder.php');
        $databaseSeederContent = File::get($databaseSeederPath);

        // Perbarui DatabaseSeeder.php dengan penambahan factory
        $factoryImportStatement = "use App\\Models\\" . ucfirst($namaTabel) . ";\n";
        $factoryFactoryStatement = "        " . ucfirst($namaTabel) . "::factory()->count(10)->create();\n";

        // Periksa apakah sudah ada impor dan pemanggilan factory dalam DatabaseSeeder.php
        if (strpos($databaseSeederContent, $factoryImportStatement) === false) {
            // Jika belum, tambahkan impor
            $databaseSeederContent = str_replace("<?php", "<?php\n\n" . $factoryImportStatement, $databaseSeederContent);
        }

        // Periksa apakah sudah ada pemanggilan factory dalam DatabaseSeeder.php
        if (strpos($databaseSeederContent, $factoryFactoryStatement) === false) {
            // Jika belum, tambahkan pemanggilan factory di dalam fungsi run()
            $databaseSeederContent = str_replace("public function run()", "public function run()\n    {\n        " . $factoryFactoryStatement, $databaseSeederContent);
        }

        // Simpan DatabaseSeeder.php
        File::put($databaseSeederPath, $databaseSeederContent);
    }

    function generatePostmanJson($namaTabel)
    {
        // Base URL aplikasi
        $baseUrl = "http://localhost:8000";

        // Data untuk koleksi Postman
        $postmanData = [
            "info" => [
                "_postman_id" => "unique-id",
                "name" => "Nama Koleksi",
                "description" => "Deskripsi Koleksi",
                "schema" => "https://schema.getpostman.com/json/collection/v2.1.0/collection.json"
            ],
            "item" => [
                [
                    "name" => "Create " . ucfirst($namaTabel),
                    "request" => [
                        "method" => "POST",
                        "header" => [],
                        "body" => [
                            "mode" => "raw",
                            "raw" => "{\n    \"key\": \"value\"\n}"
                        ],
                        "url" => [
                            "raw" => $baseUrl . "/api/" . strtolower($namaTabel),
                            "protocol" => "http",
                            "host" => ["localhost:8000"],
                            "path" => ["api", strtolower($namaTabel)]
                        ]
                    ],
                    "response" => []
                ],
                [
                    "name" => "Read " . ucfirst($namaTabel),
                    "request" => [
                        "method" => "GET",
                        "header" => [],
                        "url" => [
                            "raw" => $baseUrl . "/api/" . strtolower($namaTabel),
                            "protocol" => "http",
                            "host" => ["localhost:8000"],
                            "path" => ["api", strtolower($namaTabel)]
                        ]
                    ],
                    "response" => []
                ],
                [
                    "name" => "Update " . ucfirst($namaTabel),
                    "request" => [
                        "method" => "PUT",
                        "header" => [],
                        "body" => [
                            "mode" => "raw",
                            "raw" => "{\n    \"key\": \"value\"\n}"
                        ],
                        "url" => [
                            "raw" => $baseUrl . "/api/" . strtolower($namaTabel) . "/{id}",
                            "protocol" => "http",
                            "host" => ["localhost:8000"],
                            "path" => ["api", strtolower($namaTabel), "{id}"]
                        ]
                    ],
                    "response" => []
                ],
                [
                    "name" => "Delete " . ucfirst($namaTabel),
                    "request" => [
                        "method" => "DELETE",
                        "header" => [],
                        "url" => [
                            "raw" => $baseUrl . "/api/" . strtolower($namaTabel) . "/{id}",
                            "protocol" => "http",
                            "host" => ["localhost:8000"],
                            "path" => ["api", strtolower($namaTabel), "{id}"]
                        ]
                    ],
                    "response" => []
                ]
            ]
        ];

        // Konversi ke JSON
        $postmanJson = json_encode($postmanData, JSON_PRETTY_PRINT);

        return $postmanJson;
    }

    function generateModel($namaModel, $kolom, $relasi)
    {
        // Membuat model dengan relasi
        $modelContent = "<?php

        namespace App\Models;

        use Illuminate\Database\Eloquent\Model;
        use Illuminate\Database\Eloquent\SoftDeletes;

        class $namaModel extends Model
        {
            protected \$fillable = [" . implode(', ', array_map(function ($kolom) {
            return "'$kolom'";
        }, $kolom)) . "];

        protected \$dates = ['deleted_at']; // Tentukan kolom yang merupakan soft delete

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

    function generateControllerWeb($namaTabel, $namaModel, $namaController, $folderController)
    {
        // Membuat controller dengan fungsi CRUD dan validasi
        $controllerContent = "<?php

        namespace App\Http\Controllers;

        use Illuminate\Http\Request;
        use App\Models\\$namaModel;
        use Validator;
        use DB; // Tambahkan penggunaan DB
        use Illuminate\Database\Eloquent\SoftDeletes;

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

                // Tentukan kolom primary key
                $" . "primaryKey = '';

                // Tentukan kolom yang akan ditampilkan
                $" . "columns = [];

                foreach ($" . "filters as $" . "column => $" . "value) {
                    if ($" . "value) {
                        if ($" . "column === 'id') {
                            $" . "primaryKey = $" . "value;
                        } else {
                            $" . "columns[] = $" . "column;
                            $" . "query->where($" . "column, 'like', '%' . $" . "value . '%');
                        }
                    }
                }

                // Eksekusi query untuk mengambil data
                $" . "data = $" . "query->get($" . "columns);

                // Ubah format data sesuai dengan yang diharapkan oleh DataTables
                $" . "formattedData = [];

                foreach ($" . "data as $" . "row) {
                    $" . "checkbox = '<input type=\"checkbox\" value=\"' . $" . "row->\$primaryKey . '\">';
                    $" . "actions = '
                        <div class=\"btn-group\">
                            <button type=\"button\" class=\"btn btn-xs btn-default dropdown-toggle\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">
                                <i class=\"fa fa-cogs\"></i> Aksi
                            </button>
                            <div class=\"dropdown-menu\">
                                <a class=\"dropdown-item\" href=\"' . route('produk.edit', $" . "row->\$primaryKey) . '\">Edit</a>
                                <a class=\"dropdown-item\" href=\"' . route('produk.show', $" . "row->\$primaryKey) . '\">Detail</a>
                                <div class=\"dropdown-divider\"></div>
                                <button type=\"button\" onclick=\"deleteData(\' ' . route('produk.destroy', $" . "row->\$primaryKey) . ' \')\" class=\"dropdown-item\">Delete</button>
                            </div>
                        </div>
                    ';

                    $" . "formattedData[] = array_merge((array) $" . "row, ['checkbox' => $" . "checkbox, 'aksi' => $" . "actions]);
                }

                return response()->json([
                    'data' => $" . "formattedData
                ]);
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

    function generateAuthApi(Request $request, $namaController)
    {
        $authContent = "<?php

        namespace App\Http\Controllers\API;

        use App\Http\Controllers\Controller;
        use Illuminate\Http\Request;
        use App\Models\User; // Ubah User ke nama model yang digunakan jika berbeda
        use Validator;

        class AuthControllerAPI extends Controller
        {
            /**
             * Register a new user.
             *
             * @param  \Illuminate\Http\Request  $request
             * @return \Illuminate\Http\Response
             */
            public function register(Request $request)
            {
                // Validasi input
                \$validator = Validator::make(\$request->all(), [
                    'name' => 'required|string|max:255',
                    'email' => 'required|string|email|max:255|unique:users',
                    'password' => 'required|string|min:8',
                ]);

                if (\$validator->fails()) {
                    return response()->json(['error' => \$validator->errors()], 422);
                }

                // Simpan data user ke database
                \$user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => bcrypt($request->password),
                ]);

                return response()->json(['message' => 'User berhasil didaftarkan', 'data' => \$user], 201);
            }

            /**
             * Log in an existing user.
             *
             * @param  \Illuminate\Http\Request  $request
             * @return \Illuminate\Http\Response
             */
            public function login(Request $request)
            {
                // Validasi input
                \$validator = Validator::make($request->all(), [
                    'email' => 'required|string|email',
                    'password' => 'required|string',
                ]);

                if (\$validator->fails()) {
                    return response()->json(['error' => \$validator->errors()], 422);
                }

                // Coba autentikasi user
                if (!auth()->attempt($request->only('email', 'password'))) {
                    return response()->json(['error' => 'Unauthorized'], 401);
                }

                // Generate token
                \$accessToken = auth()->user()->createToken('authToken')->accessToken;

                return response()->json(['user' => auth()->user(), 'access_token' => \$accessToken]);
            }

            /**
             * Log out the authenticated user.
             *
             * @param  \Illuminate\Http\Request  $request
             * @return \Illuminate\Http\Response
             */
            public function logout(Request $request)
            {
                // Revoke semua token yang terkait dengan pengguna saat ini
                $request->user()->tokens()->delete();

                return response()->json(['message' => 'Logout berhasil']);
            }
        }
        ";
        // Simpan API controller ke dalam direktori Controllers/API
        $apiControllerFileName = "{$namaController}API.php";
        $apiControllerPath = app_path('Http/Controllers/API/' . $apiControllerFileName);
        File::put($apiControllerPath, $authContent);
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
        // Generate routes untuk API
        $routeApiContent = "
        use App\Http\Controllers\API\\{$namaController}API;
        // API routes
        Route::resource('api/$folderController', {$namaController}API::class);
        ";
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
                <h1>Data {{ \$namaTabel }}</h1>
            </div>
            <div class=\"card-body\">
                <!-- Tambahkan tombol-tombol untuk tambah data, edit data, dan lihat data -->
                <div class=\"mb-3\">
                    <a href=\"{{ route('$folderController.create') }}\" class=\"btn btn-success\">Tambah Data</a>
                    <button type=\"button\" class=\"btn btn-primary\" id=\"bulkDelete\">Hapus Data Terpilih</button>
                </div>
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
                        <tr>
                            <!-- Tambahkan kolom untuk cek semua -->
                            <th><input type=\"checkbox\" id=\"selectAll\"></th>
                            @foreach ($kolom as \$namaKolom)
                            <th>{{ ucfirst(\$namaKolom) }}</th>
                            @endforeach
                            <th>Aksi</th>
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

                // Event handler untuk tombol Hapus Data Terpilih
                $('#bulkDelete').on('click', function() {
                    bulkDelete();
                });

                // Function untuk mengirimkan data filter ke server
                function applyFilter() {
                    $.ajax({
                        url: '{{ route('$folderController.index') }}',
                        type: 'POST',
                        data: $('#filterForm').serialize(),
                        success: function(data) {
                            table.clear().draw();
                            table.rows.add(data).draw();
                        }
                    });
                }

                // Function untuk mengirimkan data ID yang dipilih untuk penghapusan bulk
                function bulkDelete() {
                    var selectedIds = [];

                    $('input:checked').each(function() {
                        if ($(this).attr('id') !== 'selectAll') {
                            selectedIds.push($(this).val());
                        }
                    });

                    if (selectedIds.length > 0) {
                        // Kirim ID yang dipilih ke server untuk penghapusan bulk
                        $.ajax({
                            url: '{{ route('$folderController.bulkDelete') }}',
                            type: 'POST',
                            data: {
                                ids: selectedIds,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                // Tindakan setelah penghapusan berhasil
                            }
                        });
                    } else {
                        alert('Pilih setidaknya satu item untuk dihapus.');
                    }
                }

                // Inisialisasi DataTables
                var table = $('#dataTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '{{ route('$folderController.index') }}',
                        type: 'POST', // Ganti tipe ke POST
                        data: function(d) {
                            d._token = '{{ csrf_token() }}'; // Sertakan CSRF token
                        },
                    },
                    columns: [
                        // Tambahkan kolom checkbox
                        {
                            data: 'checkbox',
                            orderable: false,
                            searchable: false
                        },
                        @foreach ($kolom as \$namaKolom)
                        {
                            data: '{{ \$namaKolom }}',
                            name: '{{ \$namaKolom }}'
                        },
                        @endforeach
                        // Kolom untuk aksi
                        {
                            data: 'aksi',
                            orderable: false,
                            searchable: false
                        }
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
                    <div class=\"form-row\">";

        foreach ($kolom as $namaKolom => $type) {
            $inputType = '';
            switch ($type) {
                case 'text':
                case 'number':
                case 'date':
                case 'time':
                case 'email':
                case 'password':
                    $inputType = "<input type=\"$type\" name=\"$namaKolom\" class=\"form-control\" id=\"$namaKolom\">";
                    break;
                case 'checkbox':
                case 'radio':
                    $inputType = "<input type=\"$type\" name=\"$namaKolom\" id=\"$namaKolom\">";
                    break;
                case 'file':
                    $inputType = "<input type=\"$type\" name=\"$namaKolom\" class=\"form-control-file\" id=\"$namaKolom\">";
                    break;
                case 'textarea':
                    $inputType = "<textarea name=\"$namaKolom\" class=\"form-control\" id=\"$namaKolom\"></textarea>";
                    break;
                case 'select':
                case 'multiselect':
                    $inputType = "<select name=\"$namaKolom\" class=\"form-control\" id=\"$namaKolom\">";
                    // Tambahkan logika untuk option jika dibutuhkan
                    $inputType .= "</select>";
                    break;
                default:
                    $inputType = "<input type=\"text\" name=\"$namaKolom\" class=\"form-control\" id=\"$namaKolom\">";
            }

            $viewContent .= "<div class=\"form-group col\">
                            <label for=\"$namaKolom\">" . ucfirst($namaKolom) . "</label>
                            {!! $inputType !!}
                        </div>";
        }

        $viewContent .= "</div>
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
                        <div class=\"form-row\">";
        foreach ($kolom as $namaKolom => $type) {
            switch ($type) {
                case 'text':
                case 'number':
                case 'date':
                case 'time':
                case 'email':
                case 'password':
                    $inputValue = "<input type=\"$type\" name=\"$namaKolom\" class=\"form-control\" id=\"$namaKolom\" value=\"{{ \$namaTabel->$namaKolom }}\">";
                    break;
                case 'checkbox':
                case 'radio':
                    $checked = ($namaTabel->$namaKolom == 1) ? 'checked' : '';
                    $inputValue = "<input type=\"$type\" name=\"$namaKolom\" id=\"$namaKolom\" value=\"1\" $checked>";
                    break;
                case 'file':
                    $inputValue = "<input type=\"$type\" name=\"$namaKolom\" class=\"form-control-file\" id=\"$namaKolom\">";
                    break;
                case 'textarea':
                    $inputValue = "<textarea name=\"$namaKolom\" class=\"form-control\" id=\"$namaKolom\">{{ \$namaTabel->$namaKolom }}</textarea>";
                    break;
                case 'select':
                    $options = ['active', 'inactive']; // Misalnya, ambil dari database atau sesuai kebutuhan
                    $inputValue = '<select name="' . $namaKolom . '" class="form-control" id="' . $namaKolom . '">';
                    foreach ($options as $option) {
                        $selected = ($namaTabel->$namaKolom == $option) ? 'selected' : '';
                        $inputValue .= '<option value="' . $option . '" ' . $selected . '>' . ucfirst($option) . '</option>';
                    }
                    $inputValue .= '</select>';
                    break;
                case 'multiselect':
                    $options = ['admin', 'user']; // Misalnya, ambil dari database atau sesuai kebutuhan
                    $inputValue = '<select multiple name="' . $namaKolom . '[]" class="form-control" id="' . $namaKolom . '">';
                    foreach ($options as $option) {
                        $selected = (in_array($option, explode(',', $namaTabel->$namaKolom))) ? 'selected' : '';
                        $inputValue .= '<option value="' . $option . '" ' . $selected . '>' . ucfirst($option) . '</option>';
                    }
                    $inputValue .= '</select>';
                    break;
                default:
                    $inputValue = "<input type=\"text\" name=\"$namaKolom\" class=\"form-control\" id=\"$namaKolom\" value=\"{{ \$namaTabel->$namaKolom }}\">";
            }

            $viewContent .= "<div class=\"form-group col\">
                                    <label for=\"$namaKolom\">{{ ucfirst($namaKolom) }}</label>
                                    {!! $inputValue !!}
                                </div>";
        }
        $viewContent .= "</div>
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
