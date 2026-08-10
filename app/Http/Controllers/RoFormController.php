<?php

namespace App\Http\Controllers;

use App\Models\RoForm;
use App\Models\Vendor;
use App\Services\AmountCalculator;
use App\Services\RoNumberGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoFormController extends Controller
{
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
            'department', 'primaryLead', 'secondaryLead',
            'createdBy', 'updatedBy', 'rejectedBy', 'cancelledBy');

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
        $totalAmount = $this->amountCalculator->calculate($data['buy_type'], $data['deliverables'], $data['volume'], $data['bid']);

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

        $roForm = RoForm::with('createdBy', 'updatedBy', 'rejectedBy', 'cancelledBy')->find($id);

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

        $totalAmount = $this->amountCalculator->calculate($buy_type, $deliverables, $volume, $bid);

        $data['total_amount'] = $totalAmount;
        $data['updated_by'] = $user->id;

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
}
