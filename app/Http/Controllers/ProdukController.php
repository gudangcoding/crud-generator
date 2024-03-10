<?php
            namespace App\Http\Controllers;

            use Illuminate\Http\Request;
            use App\Models\Produk;
            use App\Http\Controllers\Controller;

            class ProdukController extends Controller
            {
                public function index()
                {
                    $Produk = Produk::all();
                    return view('Produk.index', compact('Produk'));
                }

                public function create()
                {
                    return view('Produk.create');
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
            