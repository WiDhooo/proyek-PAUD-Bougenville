<?php

namespace App\Http\Controllers;

use App\Models\Ebook;
use Illuminate\Http\Request;

class LandingPageController extends Controller
{
    public function index()
    {
        // Ambil data ebook dari database agar variabel $ebooks tidak undefined
        $ebooks = Ebook::latest()->take(3)->get(); 

        // Kirim variabel ke view 'beranda'
        return view('beranda', compact('ebooks'));
    }
}