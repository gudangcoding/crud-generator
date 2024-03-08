@extends('layouts.app')
@section('content')
    <div class="card">
        <div class="card-header">
            <h1>CRUD Generator</h1>
        </div>
        <div class="card-body">

            <form action="" method="post">
                @csrf
                <div class="row">
                    <div class="col-2">
                        <label for="nama_tabel">Nama Tabel</label>
                        <input type="text" class="form-control" name="nama_tabel" placeholder="Masukkan nama Tabel">
                    </div>
                    <div class="col-2">
                        <label for="nama_model">Nama Model</label>
                        <input type="text" class="form-control" name="nama_model">
                    </div>
                    <div class="col-2">
                        <label for="nama_controller">Nama Controller</label>
                        <input type="text" class="form-control" name="nama_controller">
                    </div>
                    <div class="col-3">
                        <label for="folder_controller">Folder Controller</label>
                        <input type="text" class="form-control" name="folder_controller"
                            placeholder="Isi jika controller di buat dalam folder">
                    </div>
                    <div class="col-1 mt-5">
                        <input type="checkbox" name="buat_dummy" class="form-check-input">
                        <label for="folder_controller">Dummy Data?</label>
                    </div>
                    <div class="col-1 mt-5">
                        <input type="checkbox" name="batasi" class="form-check-input">
                        <label for="folder_controller">Autentikasi?</label>
                    </div>
                </div>
                <div class="row mt-3 mb-3">
                    <div class="col-2">
                        <label for="nama_kolom">Nama Kolom</label>
                        <input type="text" class="form-control" name="nama_kolom" placeholder="nama kolom Tabel">
                    </div>
                    <div class="col-2">
                        <label for="type_data">Type Data</label>
                        <input type="text" class="form-control" name="type_data" placeholder="nama kolom Tabel">
                    </div>
                    <div class="col-2">
                        <label for="panjang_data">Nama Kolom</label>
                        <input type="text" class="form-control" name="panjang_data" placeholder="nama kolom Tabel">
                    </div>
                    <div class="col-2">
                        <label for="jenis_input">Jenis Input</label>
                        <input type="text" class="form-control" name="jenis_input" placeholder="nama kolom Tabel">
                    </div>
                    <div class="col-2">
                        <label for="relasi">Relasi</label>
                        <input type="text" class="form-control" name="relasi" placeholder="nama kolom Tabel">
                    </div>
                    <div class="col-1 mt-3">
                        <button type="button" name="" id="" class="btn btn-success">
                            add
                        </button>
                    </div>
                </div>


                <button type="button" name="" id="" class="btn btn-primary">
                    Create
                </button>



            </form>
        </div>
    @endsection
