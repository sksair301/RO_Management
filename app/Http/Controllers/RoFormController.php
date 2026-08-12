<?php

namespace App\Http\Controllers;

use App\Models\RoForm;
use App\Models\Vendor;
use App\Models\User;
use App\Services\AmountCalculator;
use App\Services\RoNumberGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Mail;
use App\Mail\RoFormMail;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

use App\Mail\SendRoToAccountsMail;

class RoFormController extends Controller implements HasMiddleware
{
    public static function middleware():array{

        return[
            new Middleware ('permissions:view-ro-form', only:['index','show']),
            new Middleware ('permissions:create-ro-form', only:['store']),
            new Middleware ('permissions:edit-ro-form', only:['update']),
            new Middleware ('permissions:delete-ro-form', only:['destroy'])
        ];
    }
    protected $amountCalculator;

    protected $roNumberGenerator;

    public function __construct(RoNumberGenerator $roNumberGenerator, AmountCalculator $amountCalculator)
    {
        $this->roNumberGenerator = $roNumberGenerator;
        $this->amountCalculator = $amountCalculator;
    }

    public function index(Request $request)
    {
        $user = $request->attributes->get('user');

        $query = RoForm::with(
            'department:id,name', 'primaryLead:id,first_name,last_name', 'secondaryLead:id,first_name,last_name',
            'createdBy:id,first_name,last_name', 'updatedBy:id,first_name,last_name',
            'rejectedBy:id,first_name,last_name', 'cancelledBy:id,first_name,last_name',
            'account:id,ro_form_id');

        $search = trim($request->search);

        if(!empty($search)){
            $query->where('ro_number' , 'like' , '%' .$search. '%')
            ->orWhere('vendor_name', 'like', '%' .$search. '%')
            ->orWhere('client_name', 'like', '%' .$search. '%');
        }

        $status = request('status');

        if($status){
            $query->where('status', $status);
        }

        $role = strtolower($user->roles->name);

        if ($role != 'admin') {
            $query->where('departments_id', $user->departments_id);
        }

        $roForm = $query->latest()->paginate(5);

        return response()->json([
            'success' => true,
            'message' => 'Successfully fetched',
            'data' => $roForm,
        ], 200);
    }

    public function store(Request $request)
    {
        $user = $request->attributes->get('user');

        $valid = Validator::make($request->all(), [
            'vendor_id' => 'required|exists:vendors,id',
            'client_name' => 'required|string|max:255',
            'service' => 'required|string|max:255',
            'ad_type' => 'required|string|max:255',
            'ad_unit' => 'required|string|max:255',
            'buy_type' => 'required|string|max:255',
            'deliverables' => 'required|string',
            'volume' => 'required|numeric',
            'bid' => 'required|numeric',
            // 'total_amount' => 'required|numeric',
            'completion_date' => 'required|date',
            'buying_price' => 'required|numeric',
            'selling_price' => 'required|numeric',
            'commission_percent' => 'nullable|numeric|min:0|max:100',

        ]);

        if ($valid->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'error' => $valid->errors(),
            ], 422);
        }

        $data = $valid->validated();

        $vendor = Vendor::find($data['vendor_id']);

        if (! $vendor) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor not found',
            ], 404);
        }

        $roNumber = $this->roNumberGenerator->generate();
        $totalAmount = $this->amountCalculator->calculate($data['buy_type'], $data['deliverables'], $data['volume'], $data['bid'], $data['buying_price']);

        $roForm = RoForm::create([
            'ro_number' => $roNumber,

            'vendor_id' => $vendor->id,
            'vendor_name' => $vendor->vendor_name,
            'vendor_address' => $vendor->vendor_address,
            'vendor_gst_no' => $vendor->vendor_gst_no,
            'vendor_contact' => $vendor->vendor_contact,
            'vendor_email' => $vendor->vendor_email,

            'client_name' => $data['client_name'],
            'service' => $data['service'],
            'ad_type' => $data['ad_type'],
            'ad_unit' => $data['ad_unit'],
            'buy_type' => $data['buy_type'],
            'deliverables' => $data['deliverables'],
            'volume' => $data['volume'],
            'bid' => $data['bid'],
            'total_amount' => $totalAmount,
            'completion_date' => $data['completion_date'],
            'buying_price' => $data['buying_price'],
            'selling_price' => $data['selling_price'],
            'commission_percent' => $data['commission_percent'] ?? 0,

            'status' => 'pending',

            'departments_id' => $user->departments_id,
            'primary_lead_id' => $user->primary_lead_id,
            'secondary_lead_id' => $user->secondary_lead_id,

            'created_by' => $user->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'successfully created',
            'data' => $roForm
        ], 201);
    }

    public function show(Request $request, $id)
    {
        $user = $request->attributes->get('user');

        $roForm = RoForm::with('createdBy', 'updatedBy', 'rejectedBy', 'cancelledBy', 'account')->find($id);

        if (! $roForm) {
            return response()->json([
                'success' => false,
                'message' => 'Form not found',
            ], 404);
        }

        $role = strtolower($user->roles->name);

        if($role != 'admin'){

            if($roForm->departments_id != $user->departments_id){
                return response()->json([
                    'success'=>false,
                    'message'=>'You are not authorized to view this RO Form.'
                ],403);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Successfully fetched',
            'data' => $roForm,
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $user = $request->attributes->get('user');

        $roForm = RoForm::with('vendor', 'createdBy', 'updatedBy', 'rejectedBy', 'cancelledBy', 'approvedBy')->find($id);

        if (! $roForm) {
            return response()->json([
                'success' => false,
                'message' => 'Form not found',
            ], 404);
        }

        $role = strtolower($user->roles->name);

        if ($role != 'admin' && $roForm->departments_id != $user->departments_id) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to edit this RO Form.'
            ], 403);
        }

        if (in_array($roForm->status, ['approved', 'cancelled'])) {
            return response()->json([
                'success' => false,
                'message' => 'This RO Form cannot be edited.'
            ], 422);
        }

        if (in_array($roForm->status, ['approved', 'cancelled'])) {
            return response()->json([
                'success' => false,
                'message' => 'This Ro form cannot be edited.',
            ], 422);
        }

        $valid = Validator::make($request->all(), [
            'vendor_id' => 'sometimes|exists:vendors,id',
            'vendor_name' => 'sometimes|string|max:255',
            'vendor_address' => 'sometimes|string',
            'vendor_gst_no' => 'sometimes|string|max:50',
            'vendor_contact' => 'sometimes|string|max:20',
            'vendor_email' => 'sometimes|email|max:255',

            'client_name' => 'sometimes|string|max:255',
            'service' => 'sometimes|string|max:255',
            'ad_type' => 'sometimes|string|max:255',
            'ad_unit' => 'sometimes|string|max:255',
            'buy_type' => 'sometimes|string|max:255',
            'deliverables' => 'sometimes|string',
            'volume' => 'sometimes|numeric',
            'bid' => 'sometimes|numeric',
            'completion_date' => 'sometimes|date',
            'buying_price' => 'sometimes|numeric',
            'selling_price' => 'sometimes|numeric',
            'commission_percent' => 'sometimes|numeric|min:0|max:100',
        ]);

        if ($valid->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'error' => $valid->errors(),
            ], 422);
        }

        $data = $valid->validated();

        if (isset($data['vendor_id'])) {
            $vendor = Vendor::find($data['vendor_id']);

            $data['vendor_name'] = $data['vendor_name'] ?? $vendor->vendor_name;
            $data['vendor_address'] = $data['vendor_address'] ?? $vendor->vendor_address;
            $data['vendor_gst_no'] = $data['vendor_gst_no'] ?? $vendor->vendor_gst_no;
            $data['vendor_contact'] = $data['vendor_contact'] ?? $vendor->vendor_contact;
            $data['vendor_email'] = $data['vendor_email'] ?? $vendor->vendor_email;
        }

        $buy_type = $data['buy_type'] ?? $roForm->buy_type;
        $deliverables = $data['deliverables'] ?? $roForm->deliverables;
        $volume = $data['volume'] ?? $roForm->volume;
        $bid = $data['bid'] ?? $roForm->bid;
        $buying_price = $data['buying_price'] ?? $roForm->buying_price;

        $totalAmount = $this->amountCalculator->calculate($buy_type, $deliverables, $volume, $bid, $buying_price);

        $data['total_amount'] = $totalAmount;
        $data['updated_by'] = $user->id;
        $roForm->increment('revision_count');

        $roForm->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Successfully updated',
            'data' => $roForm->fresh()->load([
                'createdBy',
                'updatedBy',
                'approvedBy',
                'rejectedBy',
                'cancelledBy',
            ]),
        ], 200);

    }

    public function destroy($id)
    {
        $roForm = RoForm::find($id);

        if (! $roForm) {
            return response()->json([
                'success' => false,
                'message' => 'Ro Form not found',
            ], 404);
        }

        $roForm->delete();

        return response()->json([
            'success' => true,
            'message' => 'Successfully deleted',
        ],200);
    }

    public function approve(Request $request, $id)
    {
        $user = $request->attributes->get('user');

        $roForm = RoForm::find($id);

        if(!$roForm){
            return response()->json([
                'success'=>false,
                'message'=>'Ro form not found'
            ],404);
        }

        if($roForm->status != 'pending'){
            return response()->json([
                'success'=>false,
                'message'=>'Only pending forms can approve'
            ],422);
        }

        if(
            $user->id != $roForm->primary_lead_id &&
            $user->id != $roForm->secondary_lead_id
        ){
            return response()->json([
                'success'=>false,
                'message'=>'Only assigned leads can approve the Ro form'
            ],403);
        }

        $roForm->update([
            'status'=> 'approved',
            'approved_by' => $user->id,
            'approved_at' => now()
        ]);

        return response()->json([
            'success'=>True,
            'message'=>'Successfully approved',
            'data'=>$roForm->fresh()
        ]);
    }

    public function reject(Request $request, $id)
    {
        $user = $request->attributes->get('user');

        $roForm = RoForm::find($id);

        if(!$roForm){
            return response()->json([
                'success'=>false,
                'message'=> 'Ro form not found'
            ],404);
        }

        $valid = Validator::make($request->all(),[
            'rejection_reason' => 'required|string|max:255'
        ]);

        if($valid->fails()){
            return response()->json([
                'success'=>false,
                'message'=>'Validation error',
                'error'=>$valid->errors()
            ],422 );
        }

        $reason = $valid->validated();

        if(
            $user->id != $roForm->primary_lead_id &&
            $user->id != $roForm->secondary_lead_id
        ){
            return response()->json([
                'success' => false,
                'message' => 'Only assigned leads can reject'
            ],403);
        }

        if($roForm->status != 'pending'){
            return response()->json([
                'success'=>false,
                'message'=>'Only pending forms can rejected'
            ],404);
        }

        $roForm->update([
            'status'=>'rejected',
            'rejected_by'=>$user->id,
            'rejection_reason'=>$reason['rejection_reason'],
            'rejected_at'=> now()
        ]);

        return response()->json([
            'success'=>true,
            'message'=>'Ro form rejected',
            'data'=>$roForm->fresh()
        ],200);
    }

    public function cancel(Request $request, $id)
    {
        $user = $request->attributes->get('user');

        $roForm = RoForm::find($id);

        if(!$roForm){
            return response()->json([
                'success'=>false,
                'message'=>'Ro Form not found'
            ],404);
        }

        $valid = Validator::make($request->all(),[
            'cancellation_reason'=> 'required|string|max:255'
        ]);

        if($valid->fails()){
            return response()->json([
                'success'=>false,
                'message'=>'Validation error',
                'data'=>$valid->errors()
            ],422);
        }

        $reason = $valid->validated();

        if(
            $user->id != $roForm->primary_lead_id &&
            $user->id != $roForm->secondary_lead_id
        ){
            return response()->json([
                'success'=>false,
                'message'=>'Only assigned lead can cancelled'
            ],422);
        }

        if($roForm->status != 'pending' && $roForm->status != 'approved'){
            return response()->json([
                'success'=>false,
                'message'=>'Only pending or approved Ro forms can be cancelled'
            ],404);
        }

        $roForm->update([
            'status'=>'cancelled',
            'cancelled_by'=>$user->id,
            'cancellation_reason'=> $reason['cancellation_reason'],
            'cancelled_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message'=>'Successfully cancel',
            'data'=>$roForm->fresh()
        ],200);
    }

    public function sendEmail($id)
    {
        $roForm = RoForm::with('primaryLead', 'secondaryLead')->find($id);

        if(!$roForm){
            return response()->json([
                'success' =>false,
                'message'=> 'Ro form not found'
            ],404);
        }

        $emails=[];

        if($roForm->primaryLead?->email){
            $emails[] = $roForm->primaryLead->email;
        }

        if($roForm->secondaryLead?->email){
            $emails[] = $roForm->secondaryLead->email;
        }

        if (empty($emails)) {
            return response()->json([
                'success' => false,
                'message' => 'No lead email found.'
            ], 404);
        }

        Mail::to($emails)->send(new RoFormMail($roForm));

        return response()->json([
            'success' => true,
            'message' => 'Email sent successfully.'
        ]);
    }

    public function generatePdf($id)
    {
        $roForm = RoForm::with([
            'createdBy',
            'vendor',
            'primaryLead',
            'secondaryLead'
        ])->find($id);

        if(!$roForm){
            return response()->json([
                'success'=>false,
                'message'=>'Ro form nor found'
            ],404);
        }

        $pdf = Pdf::loadView('pdf.ro-form', compact('roForm'));

        $fileName = $roForm->ro_number . '.pdf';

        $filePath = 'ro/' . $fileName;

        Storage::disk('public')->put(
            $filePath,
            $pdf->output()
        );

        return response()->json([
            'success' => true,
            'file' => asset('storage/' . $filePath)
        ]);
    }

    public function sendToAccounts(Request $request, $id)
    {
        $user = $request->attributes->get('user');

        $roForm = RoForm::with([
            'primaryLead',
            'secondaryLead',
            'createdBy',
            'vendor'
        ])->find($id);

        if (!$roForm) {
            return response()->json([
                'success' => false,
                'message' => 'RO Form not found.'
            ], 404);
        }

        // Only assigned leads can send
        if (
            $user->id != $roForm->primary_lead_id &&
            $user->id != $roForm->secondary_lead_id
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Only assigned leads can send this RO to Accounts.'
            ], 403);
        }

        // RO must be approved
        if ($roForm->status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Only approved RO Forms can be sent to Accounts.'
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | Generate PDF
        |--------------------------------------------------------------------------
        */

        $pdf = Pdf::loadView('pdf.ro-form', compact('roForm'));

        $fileName = $roForm->ro_number . '.pdf';
        $relativePath = 'ro/' . $fileName;

        Storage::disk('public')->put(
            $relativePath,
            $pdf->output()
        );

        $pdfPath = storage_path('app/public/' . $relativePath);

        /*
        |--------------------------------------------------------------------------
        | Get Accounts Team Emails
        |--------------------------------------------------------------------------
        */

        $emails = User::whereHas('departments', function ($query) {
            $query->where('name', 'Accounts');
        })
        ->whereNotNull('email')
        ->pluck('email')
        ->toArray();

        if (empty($emails)) {
            return response()->json([
                'success' => false,
                'message' => 'No Accounts department email found.'
            ], 404);
        }

        /*
        |--------------------------------------------------------------------------
        | Send Email
        |--------------------------------------------------------------------------
        */

        Mail::to($emails)->send(
            new SendRoToAccountsMail($roForm, $pdfPath)
        );

        /*
        |--------------------------------------------------------------------------
        | Optional: Save audit information
        |--------------------------------------------------------------------------
        */

        // Uncomment if these columns exist
        /*
        $roForm->update([
            'sent_to_accounts_at' => now(),
            'sent_to_accounts_by' => $user->id,
        ]);
        */

        return response()->json([
            'success' => true,
            'message' => 'RO sent to Accounts successfully.'
        ]);
    }
}
