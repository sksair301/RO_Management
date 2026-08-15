<?php

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\RoForm;
use Illuminate\Support\Facades\Route;
use App\Exports\AccountsExport;
use Maatwebsite\Excel\Facades\Excel;

// Route::get('/test-pdf', function () {
//     $roForm = RoForm::with('createdBy')->find(14);

//     if (!$roForm) {
//         return 'No RO Form found in database';
//     }

//     $pdf = Pdf::loadView('pdf.ro-form', compact('roForm'));

//     return $pdf->stream('service-order.pdf');
// });

// Route::get('/test-excel', function () {
//     return Excel::download(new AccountsExport(), 'accounts.xlsx');
// });
