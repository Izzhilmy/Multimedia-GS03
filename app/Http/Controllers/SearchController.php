<?php

namespace App\Http\Controllers;

use App\Services\TextSearchService;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __construct(private TextSearchService $searchService) {}

    public function index(Request $request)
    {
        $description = trim($request->input('q', ''));
        $results     = $description !== ''
            ? $this->searchService->search($description, session('student.matric_no'))
            : null;

        return view('search.index', compact('description', 'results'));
    }
}
