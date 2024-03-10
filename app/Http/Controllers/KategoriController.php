<?php
            namespace App\Http\Controllers;

            use Illuminate\Http\Request;
            use App\Models\Kategori;
            use App\Http\Controllers\Controller;

            class KategoriController extends Controller
            {
                public function index()
                {
                    $Kategori = Kategori::all();
                    return view('Kategori.index', compact('Kategori'));
                }

                public function create()
                {
                    return view('Kategori.create');
                }

                public function store(Request $request)
                {
                    // Logika untuk menyimpan data
                }

                public function show($id)
                {
                    // Logika untuk menampilkan detail data
                }

                public function edit($id)
                {
                    // Logika untuk menampilkan form edit
                }

                public function update(Request $request, $id)
                {
                    // Logika untuk menyimpan perubahan data
                }

                public function destroy($id)
                {
                    // Logika untuk menghapus data
                }
            }
            