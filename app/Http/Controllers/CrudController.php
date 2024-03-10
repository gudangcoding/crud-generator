<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File; //membaca model
use Illuminate\Support\Facades\Schema; //membaca kolom migration
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan; //agar bisa menggunakan perintah artisan
use Illuminate\Database\Migrations\Migrator;


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
        $namespace = "App\Http\Controllers";
        $model = $request->input('nama_model');
        $columns = $request->input('columns');
        $view = $request->input('view');
        $route = $request->input('route');
        $controller = $request->input('nama_controller');
        $folder_controller = $request->input('folder_controller');
        $buat_controller = $namespace . "\\" . $controller;
        $pluralVariable = "produks";


        $this->generateController($namespace, $model, $controller);
        $this->generateModel($model);
        $this->generateMigration($model);
        // echo json_encode($request->all());

    }

    public function generateMigration($model)
    {
        // Jalankan perintah artisan untuk membuat migration
        Artisan::call('make:migration', ['name' => 'create_' . $model . 's_table']);
        // Mendapatkan instance dari kelas Migrator
        $migrator = app()->make(Migrator::class);
        // Mendapatkan daftar migrasi yang belum dijalankan
        $migrations = $migrator->getMigrationFiles(database_path('migrations'));
        // Ambil migrasi terbaru dari daftar migrasi yang belum dijalankan
        $newMigration = end($migrations);
        // return "Migration baru telah berhasil dibuat: $newMigration";
    }

    function generateController($namespace, $model, $controller)
    {
        $content_controller = "<?php
            namespace $namespace;

            use Illuminate\Http\Request;
            use App\Models\\$model;
            use App\Http\Controllers\Controller;

            class $controller extends Controller
            {
                public function index()
                {
                    $$model = $model::all();
                    return view('{$model}.index', compact('{$model}'));
                }

                public function create()
                {
                    return view('{$model}.create');
                }

                public function store(Request \$request)
                {
                    // Logika untuk menyimpan data
                }

                public function show(\$id)
                {
                    // Logika untuk menampilkan detail data
                }

                public function edit(\$id)
                {
                    // Logika untuk menampilkan form edit
                }

                public function update(Request \$request, \$id)
                {
                    // Logika untuk menyimpan perubahan data
                }

                public function destroy(\$id)
                {
                    // Logika untuk menghapus data
                }
            }
            ";
        //generate file controller
        File::put(app_path('Http/Controllers/' . $controller . '.php'), $content_controller);
    }
    function generateModel($model)
    {
        $modelContent = "<?php

            namespace App\Models;

            use Illuminate\Database\Eloquent\Model;

            class $model extends Model
            {
                protected \$fillable = ['nama', 'harga', 'deskripsi']; // Attribut yang dapat diisi secara massal

                // Definisikan relasi atau metode lain sesuai kebutuhan Anda di sini
            }
            ";


        //generate file model
        File::put(app_path('Models/' . $model . '.php'), $modelContent);
    }
}
