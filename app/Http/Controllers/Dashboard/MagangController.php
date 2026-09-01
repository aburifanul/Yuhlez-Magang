<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;

class MagangController extends Controller
{
    public function index()
    {
        $programMagang = [

            [
                'judul' => 'Frontend Developer Intern',
                'perusahaan' => 'Yuhlez Technology',
                'kategori' => 'Teknologi',
                'lokasi' => 'Jakarta',
                'durasi' => '3 Bulan',
                'icon' => 'bi bi-code-slash',
            ],

            [
                'judul' => 'UI/UX Designer Intern',
                'perusahaan' => 'Creative Digital Studio',
                'kategori' => 'Desain',
                'lokasi' => 'Bandung',
                'durasi' => '3 Bulan',
                'icon' => 'bi bi-palette',
            ],

            [
                'judul' => 'Digital Marketing Intern',
                'perusahaan' => 'Nusantara Digital',
                'kategori' => 'Marketing',
                'lokasi' => 'Surabaya',
                'durasi' => '6 Bulan',
                'icon' => 'bi bi-megaphone',
            ],

            [
                'judul' => 'Business Development Intern',
                'perusahaan' => 'Startup Indonesia',
                'kategori' => 'Bisnis',
                'lokasi' => 'Yogyakarta',
                'durasi' => '3 Bulan',
                'icon' => 'bi bi-bar-chart',
            ],

            [
                'judul' => 'Backend Developer Intern',
                'perusahaan' => 'Tech Nusantara',
                'kategori' => 'Teknologi',
                'lokasi' => 'Jakarta',
                'durasi' => '6 Bulan',
                'icon' => 'bi bi-server',
            ],

            [
                'judul' => 'Graphic Designer Intern',
                'perusahaan' => 'Digital Kreatif',
                'kategori' => 'Desain',
                'lokasi' => 'Bandung',
                'durasi' => '3 Bulan',
                'icon' => 'bi bi-brush',
            ],

        ];


        return view(
            'dashboard.magang',
            compact('programMagang')
        );
    }
}