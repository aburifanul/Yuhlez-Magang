@extends('intern.layouts.app')

@section('title', 'Dashboard Intern')

@section('content')

<div class="container-fluid">

    <div class="mb-4">
        <h2>Dashboard Intern</h2>
        <p class="text-muted">
            Selamat datang, {{ $user->name }}
        </p>
    </div>

    <div class="row g-4">

        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="text-muted">Profil</h6>

                    <h4>
                        Profil Saya
                    </h4>

                    <a href="#" class="btn btn-primary">
                        Lihat Profil
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="text-muted">Program</h6>

                    <h4>
                        Program Magang
                    </h4>

                    <a href="#" class="btn btn-primary">
                        Lihat Program
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="text-muted">Pendaftaran</h6>

                    <h4>
                        Status Pendaftaran
                    </h4>

                    <a href="#" class="btn btn-primary">
                        Lihat Status
                    </a>
                </div>
            </div>
        </div>

    </div>

</div>

@endsectionS