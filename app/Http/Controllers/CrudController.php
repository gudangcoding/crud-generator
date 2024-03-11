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



    public function tampiKolom(Request $request)
    {
        $modelName = $request->input('modelName');

        // Dapatkan informasi kolom dari tabel dalam model
        $modelInstance = app()->make($modelName);
        $modelTableName = $modelInstance->getTable();
        $columns = Schema::getColumnListing($modelTableName);

        return $columns;
    }

    function generate(Request $request)
    {
        // echo json_encode($request->relasi);
        // die();
        // input satuan
        $namaTabel = $request->nama_tabel;
        $enum = $request->enum;
        $namaModel = $request->nama_model;
        $namaController = $request->nama_controller;
        $folderController = $request->folder_controller;
        $buat_dummy = $request->buat_dummy;
        $batasi = $request->batasi;
        $api = $request->api;
        //input array
        $kolom = $request->kolom;
        // Ambil data relasi dan acuan yang dibuat dari form
        $relasi = $request->relasi;
        $acuan = $request->acuan;

        $type = $request->type_data;
        $inputType = $request->inputType;
        $lengthData = $request->lengthData;
        $additionalInput = $request->additionalInput;
        $manualInput = $request->manualInput;
        $dbInput = $request->dbInput;
        $wajib = $request->wajib;
        $parts = explode('/', $folderController);
        $controllerName = end($parts);
        $folderName = reset($parts);
        //panggil fungsi yang sudah dibuat
        // $this->generateMigration($namaTabel, $kolom, $type, $enum, $lengthData, $acuan);
        // $this->generateModel($namaTabel, $kolom, $relasi, $acuan);
        // $this->generateFakeData($namaTabel, $kolom, $type, $namaModel);
        // $this->generateControllerWeb($namaTabel, $kolom, $namaModel, $namaController, $folderController);
        // $this->generateControllerAPI($namaTabel, $kolom, $namaModel, $namaController, $folderController);
        // $this->generateAuthApi();
        $this->generateRouteWeb($namaController, $folderController);
        // $this->generateRouteAPI($namaController, $folderController);
        // $this->generateViewIndex($namaTabel, $folderController, $kolom);
        // $this->generateViewCreate($namaTabel, $kolom, $acuan);
        // $this->generateViewEdit($namaTabel, $kolom, $acuan);
        // $this->generateViewShow($namaTabel, $kolom, $acuan);

        // $this->generatePostmanJson($namaTabel, $kolom, $namaModel, $folderController);
    }

    function generateMigration($namaTabel, $kolom, $type, $lengthData = 255, $enum = null, $acuan = [])
    {

        $tabel = $namaTabel . "s";
        $content = "<?php

            use Illuminate\Database\Migrations\Migration;
            use Illuminate\Database\Schema\Blueprint;
            use Illuminate\Support\Facades\Schema;
            use Ramsey\Uuid\Uuid;

            class Create" . ucfirst($namaTabel) . "Table extends Migration
            {
                public function up()
                {
                    Schema::create('$tabel', function (Blueprint \$table) {\n";
        $content .= "\$table->uuid('id')->primary();\n";
        $content .= "\$table->timestamps();\n";
        $content .= "\$table->softDeletes(); // Menambahkan soft deletes\n";
        if ($kolom) {
            foreach ($kolom as $kolom => $kol) {
                // echo  count($kolom);
                //jika ada relasi
                if (in_array($kol, $acuan)) {
                    $content .= "\$table->unsignedBigInteger('\${\$kol}_id');";
                    $content .= "\$table->foreign('\${\$kol}_id')->references('id')->on('\${\$kol}s')->onDelete('cascade');";
                    $content .= "\n";
                } else {
                    //jika tidak ada relasi
                    switch ($type) {
                        case 'char':
                            $content .= "\$table->string('$kol', $lengthData);\n";
                            break;
                        case 'varchar':
                            $content .= "\$table->string('$kol', $lengthData);\n";
                            break;
                        case 'text':
                            $content .= "\$table->text('$kol');\n";
                            break;
                        case 'int':
                            $content .= "\$table->integer('$kol', $lengthData);\n";
                            break;
                        case 'bigint':
                            $content .= "\$table->bigInteger('$kol', $lengthData);\n";
                            break;
                        case 'float':
                            $content .= "\$table->float('$kol');\n";
                            break;
                        case 'double':
                            $content .= "\$table->double('$kol');\n";
                            break;
                        case 'decimal':
                            $content .= "\$table->decimal('$kol');\n";
                            break;
                        case 'date':
                            $content .= "\$table->date('$kol');\n";
                            break;
                        case 'time':
                            $content .= "\$table->time('$kol');\n";
                            break;
                        case 'datetime':
                            $content .= "\$table->dateTime('$kol');\n";
                            break;
                        case 'timestamp':
                            $content .= "\$table->timestamp('$kol');\n";
                            break;
                        case 'enum':
                            $content .= "\$table->enum('$kol', [$enum])->default('\$default');\n";
                            break;
                        default:
                            $content .= "\$table->string('$kol', $lengthData);\n";
                            break;
                    }
                }
            }
        }
        $content .= "
                    });\n
                }

                public function down()
                {
                    Schema::dropIfExists('$tabel');\n
                }
            }";

        // Simpan migration ke dalam direktori migrations
        $migrationFileName = date('Y_m_d_His') . '_create_' . strtolower($tabel) . '_table.txt';
        $migrationPath = database_path('migrations/' . $migrationFileName);
        file_put_contents($migrationPath, $content);
    }





    function generateModel($namaModel, $kolom, $relasi, $acuan)
    {
        // Membuat model dengan relasi
        $content = "<?php

        namespace App\Models;\n

        use Illuminate\Database\Eloquent\Model;\n
        use Illuminate\Database\Eloquent\SoftDeletes;\n";

        $content .= "class $namaModel extends Model
        {
            use SoftDeletes;";

        $content .= "protected \$fillable = [\n";
        foreach ($kolom as $key => $value) {
            $content .= "'$value',\n";
        }
        $content .= "];\n";

        $content .= "protected \$dates = ['deleted_at']; // Tentukan kolom yang merupakan soft delete\n";



        // Definisikan relasi dengan model
        foreach ($relasi as $key => $value) {
            if ($relasi[$key] != null) {
                // if (in_array($value, $acuan)) {
                $parts = explode('\\', $value);
                $model = end($parts);
                $kolom_id = strtolower($model) . '_id';
                $content .= "  public function $model()\n
                            {\n
                                return \$this->belongsTo($model::class, '$kolom_id', '$acuan[$key]');\n
                            }\n";
                // }
            }
        }
        $content .= "
        }";

        // Simpan model ke dalam direktori Models
        $modelFileName = ucwords($namaModel) . '.php';
        $modelPath = app_path('Models/' . $modelFileName);
        File::put($modelPath, $content);
    }





    function generateFakeData($namaTabel, $kolom, $type, $model)
    {
        // Membuat factory
        $factoryContent = "<?php

        namespace Database\Factories;

        use Illuminate\Database\Eloquent\Factories\Factory;

        class " . $model . "Factory extends Factory
        {

            public function definition()
            {
                return [";


        // Menambahkan definisi factory sesuai dengan tipe data kolom
        foreach ($kolom as $namaKolom => $kol) {


            switch ($type) {
                case 'char':
                case 'varchar':
                case 'text':
                    $factoryContent .= "'$kol' => \$this->faker->sentence()," . PHP_EOL;
                    break;
                case 'int':
                case 'bigint':
                    $factoryContent .= "'$kol' => \$this->faker->numberBetween(1, 100)," . PHP_EOL;
                    break;
                case 'float':
                case 'doble':
                case 'decimal':
                    $factoryContent .= "'$kol' => \$this->faker->randomFloat(2, 0, 100)," . PHP_EOL;
                    break;
                case 'date':
                    $factoryContent .= "'$kol' => \$this->faker->date()," . PHP_EOL;
                    break;
                case 'time':
                    $factoryContent .= "'$kol' => \$this->faker->time()," . PHP_EOL;
                    break;
                case 'datetime':
                    $factoryContent .= "'$kol' => \$this->faker->dateTime()," . PHP_EOL;
                case 'timestamp':
                    $factoryContent .= "'$kol' => \$this->faker->timestamps()," . PHP_EOL;
                    break;
                case 'json':
                    $factoryContent .= "'$kol' =>  [\$this->faker->timestamps(),]" . PHP_EOL;

                    break;
                case 'enum':
                    // Pastikan Anda memiliki array opsi untuk ENUM
                    $enumOptions = implode("', '", $kol['length']);
                    $factoryContent .= "'$kol' => \$this->faker->randomElement(['$enumOptions'])," . PHP_EOL;
                    break;
                default:
                    // Jika tipe tidak dikenali, gunakan string sebagai default
                    $factoryContent .= "'$kol' => \$this->faker->word()," . PHP_EOL;
            }
        }

        $factoryContent .= " ];
            }
        };";

        // Simpan factory ke dalam direktori factories
        $factoryFileName = ucfirst($namaTabel) . "Factory.php";
        $factoryPath = database_path('factories/' . $factoryFileName);
        File::put($factoryPath, $factoryContent);


        // ini adalah kode yang ingin Anda sisipkan
        $codeToInsert = "\n\n\App\Models\\" . $model . "::factory()->count(10)->create();\n\n";

        // Baca isi dari DatabaseSeeder.php
        $databaseSeederPath = database_path('seeders/DatabaseSeeder.php');
        $databaseSeederContent = file_get_contents($databaseSeederPath);

        // Temukan posisi kurung kurawal pembuka di dalam fungsi run()
        $openBracePosition = strpos($databaseSeederContent, '{', strpos($databaseSeederContent, 'public function run()'));

        // Temukan posisi kurung kurawal penutup di dalam fungsi run()
        $closeBracePosition = strpos($databaseSeederContent, '}', $openBracePosition);

        // Pisahkan kode sebelum dan sesudah kurung kurawal
        $beforeInsert = substr($databaseSeederContent, 0, $openBracePosition + 1);
        $afterInsert = substr($databaseSeederContent, $closeBracePosition);

        // Gabungkan kembali dengan kode yang ingin disisipkan di tengah
        $modifiedSeederContent = $beforeInsert . $codeToInsert . $afterInsert;

        // Simpan kembali ke DatabaseSeeder.php
        file_put_contents($databaseSeederPath, $modifiedSeederContent);
    }

    function appendToPostmanJson($newData, $existingPath)
    {

        if (File::exists($existingPath)) {
            $existingData = json_decode(File::get($existingPath), true);
            $mergedData = array_merge_recursive($existingData, $newData);
            File::put($existingPath, json_encode($mergedData, JSON_PRETTY_PRINT));
        } else {
            File::put($existingPath, json_encode($newData, JSON_PRETTY_PRINT));
        }
    }


    function generatePostmanJson($namaTabel, $kolom, $namaModel, $folderController)
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
            $namaModel => [
                [
                    "name" => "Create " . ucfirst($namaTabel),
                    "request" => [
                        "method" => "POST",
                        "header" => [],
                        "body" => [
                            "mode" => "raw",
                            "raw" => json_encode($kolom, JSON_PRETTY_PRINT)
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
                            "raw" => json_encode($kolom, JSON_PRETTY_PRINT)
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

        // Simpan JSON ke dalam file
        $postmanPath = base_path('postman.json');
        // Mengecek apakah file Postman JSON sudah ada
        if (File::exists($postmanPath)) {
            // Membaca koleksi yang sudah ada dari file Postman JSON
            $existingCollections = json_decode(File::get($postmanPath), true);

            // Cek apakah koleksi dengan nama $namaModel sudah ada dalam file Postman JSON
            if (!isset($existingCollections[$namaModel])) {
                // Panggil fungsi appendToPostmanJson untuk menambahkan data baru
                $this->appendToPostmanJson(json_decode($postmanJson, true), $postmanPath);
            } else {
                echo "Koleksi $namaModel sudah ada dalam file Postman JSON." . PHP_EOL;
            }
        } else {
            // Jika file tidak ada, buat file baru dengan data koleksi
            File::put($postmanPath, $postmanJson);
        }
    }







    function generateControllerWeb($namaTabel, $kolom, $namaModel, $namaController, $folderController)
    {
        // Membuat controller dengan fungsi CRUD dan validasi
        $content = "<?php

        namespace App\Http\Controllers;

        use Illuminate\Http\Request;
        use App\Models\\$namaModel;
        use Illuminate\Support\Facades\Validator;
        use Illuminate\Support\Facades\DB; // Tambahkan penggunaan DB
        use Illuminate\Database\Eloquent\SoftDeletes;

        class $namaController extends Controller
        {

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
                    ";
        foreach ($kolom as $key => $col) {
            $content .= "'$col' => 'required',";
        }
        $content .= "
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
                    ";
        foreach ($kolom as $key => $col) {
            $content .= "'$col' => 'required',";
        }
        $content .= "
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
        File::put($controllerPath, $content);
    }

    function generateControllerAPI($namaTabel, $kolom, $namaModel, $namaController, $folderController)
    {

        // Membuat API controller
        $content = "<?php

            namespace App\Http\Controllers\API;

            use App\Http\Controllers\Controller;
            use Illuminate\Http\Request;
            use App\Models\\$namaModel;
            use Illuminate\Support\Facades\Validator;

            class {$namaController}API extends Controller
            {

                public function index()
                {
                    $" . "data = $namaModel::all();
                    return response()->json($" . "data);
                }


                public function store(Request $" . "request)
                {
                    // Validasi input
                    $" . "validator = Validator::make($" . "request->all(), [
                        ";
        foreach ($kolom as $key => $col) {
            $content .= "'$col' => 'required',\n";
        }
        $content .= "
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
                        ";
        foreach ($kolom as $key => $col) {
            $content .= "'$col' => 'required',\n";
        }
        $content .= "
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
        // Pengecekan apakah folder sudah ada, jika tidak, buat folder

        File::put($apiControllerPath, $content);
    }

    function generateAuthApi()
    {
        $authContent = "<?php

        namespace App\Http\Controllers\API;

        use App\Http\Controllers\Controller;
        use Illuminate\Http\Request;
        use App\Models\User; // Ubah User ke nama model yang digunakan jika berbeda
        use Illuminate\Support\Facades\Validator;
        use Illuminate\Support\Facades\Auth;

        class AuthControllerAPI extends Controller
        {

            public function profil(Request \$request)  {
                \$user = User::where('id', \$request->id)->first();
                return response()->json([
                    'success' => true,
                    'data' => \$user
                ], 201);
            }

            public function register(Request \$request)
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
                    'name' => \$request->name,
                    'email' => \$request->email,
                    'password' => bcrypt(\$request->password),
                ]);

                return response()->json(['message' => 'User berhasil didaftarkan', 'data' => \$user], 201);
            }


            public function login(Request \$request)
            {
                \$validator = Validator::make(\$request->all(), [
                    'email' => 'required|email',
                    'password' => 'required',
                ]);
                if (\$validator->fails()) {
                    return response()->json(['error' => \$validator->errors()->all()]);
                }

                if (Auth::guard()->attempt(['email' => \$request->email, 'password' => \$request->password])){
                    \$user = User::select('id', 'name', 'email','alamat','nohp','foto')->find(auth()->guard()->user()->id);
                    \$success = \$user;
                    \$token =  \$user->createToken('mytoken')->plainTextToken;
                    return response()->json([
                        'success' => true,
                        'message' => 'Login success!',
                        'data' => \$success,
                    ], 201);
                }else{
                    return response()->json([
                        'success' => false,
                        'message' => 'Login Failed!',
                    ], 401);
                }
            }


            public function logout(Request \$request)
            {
                // Revoke semua token yang terkait dengan pengguna saat ini
                \$request->user()->tokens()->delete();

                return response()->json(['message' => 'Logout berhasil']);
            }
        }
        ";
        // Simpan API controller ke dalam direktori Controllers/API
        $apiControllerFileName = "AuthControllerAPI.php";
        $apiControllerPath = app_path('Http/Controllers/API/' . $apiControllerFileName);
        File::put($apiControllerPath, $authContent);
    }
    // function generateRouteWeb($namaController, $folderController)
    // {
    //     // Generate routes untuk web
    //     $import = "use App\Http\Controllers\\{$namaController};";
    //     // Web routes
    //     $route = "Route::resource('$folderController', {$namaController}::class);";

    //     // Simpan route ke dalam file web.php
    //     $routePath = base_path('routes/web.php');

    //     // Membaca isi file web.php
    //     $existingContent = File::get($routePath);

    //     // Memisahkan konten menjadi array per baris
    //     $lines = explode("\n", $existingContent);

    //     // Menambahkan konten baru setelah baris ke-5 (misalnya)
    //     array_splice($lines, 5, 0, [$import]);

    //     // Menggabungkan kembali array ke dalam string
    //     $newContent = implode("\n", $lines);

    //     // Menulis kembali ke file
    //     File::put($routePath, "\n" . $newContent);

    //     // Menambahkan route setelah import
    //     File::append($routePath, $route);
    // }

    function generateRouteWeb($namaController, $folderController)
    {
        // Generate routes untuk web
        $import = "use App\Http\Controllers\\{$namaController};";
        // Web routes
        $route = "Route::resource('$folderController', {$namaController}::class);";

        // Simpan route ke dalam file web.php
        $routePath = base_path('routes/web.php');

        // Membaca isi file web.php
        $existingContent = File::get($routePath);

        // Memeriksa keberadaan $import dalam konten
        if (strpos($existingContent, $import) === false) {
            // Jika $import belum ada, tambahkan setelah baris ke-5 (misalnya)
            $lines = explode("\n", $existingContent);
            array_splice($lines, 5, 0, [$import]);
            $existingContent = implode("\n", $lines);
            File::put($routePath, "\n" . $existingContent);
        }

        // Memeriksa keberadaan $route dalam konten
        if (strpos($existingContent, $route) === false) {
            // Jika $route belum ada, tambahkan setelah $import
            File::append($routePath, $route);
        }
    }




    function generateRouteAPI($namaController, $folderController)
    {

        $import = " use App\Http\Controllers\API\\{$namaController}API;";
        $route = "Route::resource('api/$folderController', {$namaController}API::class);";
        // Simpan route ke dalam file web.php
        $routePath = base_path('routes/api.php');

        // Membaca isi file web.php
        $existingContent = File::get($routePath);

        // Memisahkan konten menjadi array per baris
        $lines = explode("\n", $existingContent);

        // Menambahkan konten baru setelah baris ke-5 (misalnya)
        array_splice($lines, 5, 0, [$import]);

        // Menggabungkan kembali array ke dalam string
        $newContent = implode("\n", $lines);

        // Menulis kembali ke file
        File::put($routePath, "\n" . $newContent);

        // Menambahkan route setelah import
        File::append($routePath, $route);
    }

    function generateViewIndex($namaTabel, $kolom, $folderController)
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
