<?php

namespace App\Http\Controllers;
use GrahamCampbell\Markdown\Facades\Markdown;

use Illuminate\Http\Request;

class WikiController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
//        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $readme_md = file_get_contents(app_path('../readme.md'));
        $readme_content = Markdown::convertToHtml($readme_md);
        return response()->view('wiki', array('readme_content' => $readme_content), 200);
    }
}