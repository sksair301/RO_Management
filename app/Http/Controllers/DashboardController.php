<?php

namespace App\Http\Controllers;

use App\Models\RoForm;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function adminDashboard(){
        return response()->json([
            'success'=>True,
            'message'=>'Dashboard fetched successfully',
            'data'=>[
                'total_ro_form' => RoForm::count(),
                'pending_ro_form' => RoForm::where('status', 'pending')->count(),
                'approved_ro_form' => RoForm::where('status', 'approved')->count(),
                'rejected_ro_form' => RoForm::where('status', 'rejected')->count(),
                'cancelled_ro_form' => RoForm::where('status', 'cancelled')->count(),
                'today_ro_form' => RoForm::whereDate('created_at', today())->count(),
                'this_month_ro_form' => RoForm::whereMonth('created_at', now()->month)
                                                        ->whereYear('created_at', now()->year)->count(),
                'total_vendor' => Vendor::count(),
                'total_user' => User::count()
            ]
        ]);
    }
}
