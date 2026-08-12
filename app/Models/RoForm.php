<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class RoForm extends Model
{
    protected $appends = ['invoice_generated'];

    protected $fillable = [
        'ro_number',

        'departments_id',
        'vendor_id',
        'vendor_name',
        'vendor_address',
        'vendor_gst_no',
        'vendor_contact',
        'vendor_email',

        'client_name',
        'service',
        'ad_type',
        'ad_unit',
        'buy_type',
        'deliverables',
        'volume',
        'bid',
        'completion_date',
        'buying_price',
        'selling_price',
        'total_amount',
        'commission_percent',
        'status',

        'created_by',
        'updated_by',
        'revision_count',

        'primary_lead_id',
        'secondary_lead_id',

        'approved_by',
        'approved_at',

        'rejected_by',
        'rejected_at',

        'cancelled_by',
        'cancelled_at',

        'rejection_reason',
        'cancellation_reason',
    ];

    /**
     * An RO form has at most one account/invoice entry.
     */
    public function account()
    {
        return $this->hasOne(Accounts::class, 'ro_form_id');
    }

    protected function invoiceGenerated(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->relationLoaded('account')
                ? !is_null($this->account)
                : $this->account()->exists(),
        );
    }

    public function vendor(){

        return $this->belongsTo(Vendor::class);
    }

    public function createdBy(){

        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(){

        return $this->belongsTo(User::class, 'updated_by');
    }

    public function approvedBy(){

        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejectedBy(){

        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function cancelledBy(){

        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function department()
    {
        return $this->belongsTo(Departments::class, 'departments_id');
    }

    public function primaryLead()
    {
        return $this->belongsTo(User::class, 'primary_lead_id');
    }

    public function secondaryLead()
    {
        return $this->belongsTo(User::class, 'secondary_lead_id');
    }
}
