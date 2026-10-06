<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = today();

        $stats = [
            'total' => Visitor::count(),
            'today' => Visitor::whereDate('visit_date', $today->toDateString())->count(),
            'inside' => Visitor::where('status', Visitor::STATUS_CHECKED_IN)->count(),
            'pending' => Visitor::where('status', Visitor::STATUS_PENDING)->count(),
            'approved' => Visitor::where('status', Visitor::STATUS_APPROVED)->count(),
            'rejected' => Visitor::where('status', Visitor::STATUS_REJECTED)->count(),
            'checkedOut' => Visitor::where('status', Visitor::STATUS_CHECKED_OUT)->count(),
        ];

        // Bar chart data: last 7 days
        $barLabels = [];
        $barData = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = $today->copy()->subDays($i);
            $barLabels[] = $day->translatedFormat('D, d M');
            $barData[] = Visitor::whereDate('visit_date', $day->toDateString())->count();
        }

        // Venn data: visitor segments overlap
        $venn = [
            'pre' => Visitor::where('visit_type', 'pre_registered')->count(),
            'walk' => Visitor::where('visit_type', 'walk_in')->count(),
            'vendor' => Visitor::where('visit_type', 'vendor')->count(),
            'pre_walk' => 0,
            'pre_vendor' => 0,
            'walk_vendor' => 0,
        ];

        $recent = Visitor::with('host')
            ->latest('id')
            ->limit(8)
            ->get();

        $inside = Visitor::with('host')
            ->where('status', Visitor::STATUS_CHECKED_IN)
            ->orderBy('checked_in_at')
            ->limit(8)
            ->get();

        // Status distribution for extra chart
        $statusDist = Visitor::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('dashboard', compact('stats', 'barLabels', 'barData', 'venn', 'recent', 'inside', 'statusDist'));
    }
}
