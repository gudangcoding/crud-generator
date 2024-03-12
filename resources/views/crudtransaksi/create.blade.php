@extends('layouts.app')

        @section('content')
        <div class="card">
            <div class="card-header">
                <h1>Create CrudTransaksi</h1>
            </div>
            <div class="card-body">
                <form id="createForm">
                    @csrf
                    <div class="form-row"><div class="form-group col">
                            <label for="nama">Nama</label>
                            <input type="text" name="nama" class="form-control" id="nama">
                        </div><div class="form-group col">
                            <label for="email">Email</label>
                            <input type="text" name="email" class="form-control" id="email">
                        </div><div class="form-group col">
                            <label for="alamat">Alamat</label>
                            <input type="text" name="alamat" class="form-control" id="alamat">
                        </div></div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>

        <script>
            $(document).ready(function() {
                $('#createForm').on('submit', function(e) {
                    e.preventDefault();
                    var formData = $(this).serialize();
                    $.ajax({
                        url: '{{ route('crudtransaksi.store') }}',
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
        @endsection