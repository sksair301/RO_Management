<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Service Order</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 13px;
            color: #000;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .border {
            border: 1px solid #000;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .mt20 {
            margin-top: 20px;
        }

        td {
            padding: 6px;
            vertical-align: top;
        }
    </style>

</head>

<body>

    <table>

        <tr>

            <td width="25%">

                <img src="{{ storage_path('app/public/logo/anvis-logo.png') }}" width="150">

            </td>

            <td width="75%" class="right">

                <h2>ANVIS DIGITAL PVT. LTD.</h2>

                314 - Parvati Industrial Estate<br>

                New Sun Mill Compound<br>

                Lower Parel, Mumbai - 400013

                <br>

                GST : 27AAMCA3257A1ZX

            </td>

        </tr>

    </table>

    <br>
    <h2 class="center">

        Service Order

    </h2>

    <table class="border">

        <tr>

            <td width="50%">

                <b>Service Order No :</b>

                {{ $roForm->ro_number }}

            </td>

            <td width="50%">

                <b>Date :</b>

                {{ \Carbon\Carbon::parse($roForm->created_at)->format('d-m-Y') }}

            </td>

        </tr>

    </table>

    <br>

    <b>TO,</b>

    <br><br>

    {{ $roForm->vendor_name }}

    <br>

    {{ $roForm->vendor_address }}

    <br>

    GSTIN :

    {{ $roForm->vendor_gst_no }}

    <br>

    Contact :

    {{ $roForm->vendor_contact }}

    <br>

    Email :

    {{ $roForm->vendor_email }}

    <br><br>

    Dear Sir/Madam's,

    <br><br>

    We hereby place an order on you for the following services on the terms & conditions attached with this.

    <br><br>

    <b>Service Description</b>

    <br><br>

    <b>Client Name :</b>

    {{ $roForm->client_name }}

    <br><br>

    <table border="1" cellspacing="0" cellpadding="6">

        <thead>

            <tr style="background:#eeeeee">

                <th>Service</th>

                <th>Ad Type</th>

                <th>Ad Unit</th>

                <th>Deliverables</th>

                <th>Volume</th>

                <th>Bid</th>

                <th>Amount</th>

            </tr>

        </thead>

        <tbody>

            <tr>

                <td>{{ $roForm->service }}</td>

                <td>{{ $roForm->ad_type }}</td>

                <td>{{ $roForm->ad_unit }}</td>

                <td>{{ $roForm->deliverables }}</td>

                <td>{{ $roForm->volume }}</td>

                <td>₹ {{ number_format($roForm->bid, 2) }}</td>

                <td>₹ {{ number_format($roForm->total_amount, 2) }}</td>

            </tr>

        </tbody>

    </table>

    <br>

    <table>

        <tr>

            <td width="50%">

                <b>Buying Price</b>

            </td>

            <td>

                ₹ {{ number_format($roForm->buying_price, 2) }}

            </td>

        </tr>

        <tr>

            <td>

                <b>Selling Price</b>

            </td>

            <td>

                ₹ {{ number_format($roForm->selling_price, 2) }}

            </td>

        </tr>

        <tr>

            <td>

                <b>Commission</b>

            </td>

            <td>

                {{ $roForm->commission_percent }} %

            </td>

        </tr>

    </table>

    <br>

    <b>Delivery / Job Completion Date :</b>

    {{ \Carbon\Carbon::parse($roForm->completion_date)->format('d-m-Y') }}

    <br><br>

    <b>Payment Terms & Conditions :</b>

    <ol style="font-size:12px; line-height:20px;">

        <li>Above price is net to ANVIS DIGITAL PVT. LTD. Exclusive of all applicable Government taxes.</li>

        <li>Payment Terms : Net 60 Days.</li>

        <li>Publisher must provide reporting screenshots or access to the reporting interface.</li>

        <li>RO details should remain confidential between both parties and must not be disclosed to any third party.
        </li>

    </ol>

    <br>

    Please sign and return the duplicate copy of this Service Order as a token of your acceptance.

    <br><br><br>

    <table width="100%">

        <tr>

            <td width="50%" align="left">

                <b>For ANVIS DIGITAL PVT. LTD.</b>

                <br><br><br>

                <img src="{{ storage_path('app/public/signatures/sign.png') }}" width="180" height="70">

                <br>

                <b>Authorized Signatory</b>

            </td>

            <td width="50%" align="center">

                <br><br><br><br><br><br>

                _____________________________

                <br>

                <b>Signature of Seller</b>

            </td>

        </tr>

    </table>

    <br><br>

    <table width="100%">

        <tr>

            <td>

                Created By :
                <b>{{ $roForm->createdBy->first_name ?? '' }} {{ $roForm->createdBy->last_name ?? '' }}</b>

            </td>

            <td align="right">

                Approval Status :

                <b>{{ ucfirst($roForm->status) }}</b>

            </td>

        </tr>

    </table>

</body>

</html>
