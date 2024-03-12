@extends('layouts.app')

        @section('content')
            <div class="card">
                <div class="card-header">
                    <h1>Detail tessaja</h1>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td>{{ ucfirst('nama') }}</td>
                                    <td>{{ tessaja->nama }}</td>
                                </tr>
                                <tr>
                                    <td>{{ ucfirst('email') }}</td>
                                    <td>{{ tessaja->email }}</td>
                                </tr>
                                <tr>
                                    <td>{{ ucfirst('alamat') }}</td>
                                    <td>{{ tessaja->alamat }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endsection