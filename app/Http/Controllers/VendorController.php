<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VendorController extends Controller
{

    public function index()
    {
        $vendor = Vendor::latest()->get();

        return response()->json([
            'success'=>True,
            'message'=>'Successfully fetched',
            'data'=>$vendor
        ],200);
    }

    public function store(Request $request)
    {
        $valid = Validator::make($request->all(),[
            'vendor_name'=> 'required|string',
            'vendor_address'=>'required|string',
            'vendor_gst_no'=>'required|string',
            'vendor_contact'=>'required|string',
            'vendor_email'=>'required|email'
        ]);

        if($valid->fails()){
            return response()->json([
                'success'=>false,
                'message'=>'Validation error',
                'error'=>$valid->errors()
            ],422);
        }

        $data = $valid->validated();

        $vendor = Vendor::create([
            'vendor_name'=> $data['vendor_name'],
            'vendor_address'=> $data['vendor_address'],
            'vendor_gst_no'=> $data['vendor_gst_no'],
            'vendor_contact'=> $data['vendor_contact'],
            'vendor_email'=> $data['vendor_email']
        ]);

        return response()->json([
            'success'=>true,
            'message'=>'Vendor created successfully',
            'data'=>$vendor
        ],201);
    }

    public function show($id)
    {
        $vendor = Vendor::find($id);

        if(!$vendor){
            return response()->json([
                'success'=>False,
                'message'=>'Vendor not found'
            ],404);
        }

        return response()->json([
            'success'=>True,
            'message'=>'Successfully fetched',
            'data'=>$vendor
        ],200);
    }


    public function update(Request $request, $id)
    {
        $vendor = Vendor::find($id);

        if (!$vendor) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor not found'
            ], 404);
        }

        $valid = Validator::make($request->all(),[
            'vendor_name'=> 'sometimes|string',
            'vendor_address'=>'sometimes|string',
            'vendor_gst_no'=>'sometimes|string',
            'vendor_contact'=>'sometimes|string',
            'vendor_email'=>'sometimes|email'
        ]);

        if ($valid->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $valid->errors()
            ], 422);
        }

        $data = $valid->Validated();

        $vendor->update($data);

        return response()->json([
            'success'=>True,
            'message'=>'Data update successfully',
            'data'=>$vendor->fresh()
        ],200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vendor $vendor)
    {
        //
    }
}
