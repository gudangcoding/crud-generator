@extends('layouts.app')

        @section('content')
        <div class="card">
            <div class="card-header">
                <h1>Create barang</h1>
            </div>
            <div class="card-body">
                <form id="createForm">
                    @csrf
                    <div class="form-row"><div class="form-group col">
                            <label for="tes">Tes</label>
                            <input type="text" name="tes" class="form-control" id="tes">
                        </div><div class="form-group col">
                            <label for="bnbn">Bnbn</label>
                            <input type="text" name="bnbn" class="form-control" id="bnbn">
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
                        url: '{{ route('barang.store') }}',
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