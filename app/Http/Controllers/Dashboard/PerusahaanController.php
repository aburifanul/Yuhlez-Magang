<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;

class PerusahaanController extends Controller
{
    public function index()
    {
        $perusahaan = [
            [
                'nama' => 'Yuhlez Technology',
                'kategori' => 'Teknologi',
                'lokasi' => 'Jakarta',
                'program' => 8,
                'deskripsi' => 'Perusahaan teknologi yang bergerak dalam pengembangan aplikasi dan solusi digital.',
                'icon' => 'bi bi-code-slash',
                'warna' => 'yellow',
            ],

            [
                'nama' => 'Creative Digital Studio',
                'kategori' => 'Desain & Kreatif',
                'lokasi' => 'Bandung',
                'program' => 5,
                'deskripsi' => 'Studio kreatif yang berfokus pada desain digital, branding, dan pengalaman pengguna.',
                'icon' => 'bi bi-palette',
                'warna' => 'black',
            ],

            [
                'nama' => 'Nusantara Digital',
                'kategori' => 'Marketing',
                'lokasi' => 'Surabaya',
                'program' => 6,
                'deskripsi' => 'Perusahaan digital yang membantu bisnis mengembangkan pemasaran dan strategi digital.',
                'icon' => 'bi bi-megaphone',
                'warna' => 'yellow',
            ],

            [
                'nama' => 'Startup Indonesia',
                'kategori' => 'Bisnis',
                'lokasi' => 'Yogyakarta',
                'program' => 4,
                'deskripsi' => 'Startup yang mengembangkan berbagai solusi bisnis dan teknologi untuk masyarakat.',
                'icon' => 'bi bi-graph-up-arrow',
                'warna' => 'black',
            ],

            [
                'nama' => 'Digital Kreatif',
                'kategori' => 'Teknologi',
                'lokasi' => 'Jakarta',
                'program' => 7,
                'deskripsi' => 'Perusahaan kreatif digital yang mengembangkan produk dan konten berbasis teknologi.',
                'icon' => 'bi bi-laptop',
                'warna' => 'yellow',
            ],

            [
                'nama' => 'Tech Nusantara',
                'kategori' => 'Teknologi',
                'lokasi' => 'Bandung',
                'program' => 5,
                'deskripsi' => 'Perusahaan teknologi yang berfokus pada pengembangan sistem informasi dan aplikasi.',
                'icon' => 'bi bi-building',
                'warna' => 'black',
            ],
        ];

        return view('dashboard.perusahaan', compact('perusahaan'));
    }
}