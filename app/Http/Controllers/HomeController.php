<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use App\Models\Mentor;
use App\Models\Review;

class HomeController extends Controller
{
    public function index()
    {
        $domains = Domain::orderBy('name')->get();

        $testimonials = Review::query()
            ->with(['mentor.user'])
            ->latest()
            ->take(4)
            ->get();

        $featuredMentors = Mentor::query()
            ->with(['user'])
            ->latest()
            ->take(4)
            ->get();

        return view('home', compact('domains', 'testimonials', 'featuredMentors'));
    }
}
