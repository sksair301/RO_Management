<!DOCTYPE html>
<html>

<head>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <title>{{ $roForm->ro_number }}</title>

    <style>
        @page {
            margin: 12mm;
            size: a4 portrait;
        }

        body {
            font-family: sans-serif;
            font-size: 12px;
            color: #000;
            margin: 0;
            padding-bottom: 5px;
            border: 1px solid #000;
        }

        table {
            border-collapse: collapse;
        }

        .page {
            width: 100%;
        }

        .page td {
            vertical-align: top;
        }

        .header {
            width: 100%;
        }

        .header td {
            border: none;
        }

        .logo {
            text-align: center;
            padding-top: 10px;
        }

        .company {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            padding-top: 4px;
        }

        .title {
            text-align: center;
            font-size: 12px;
            font-weight: bold;
        }

        .address {
            text-align: center;
            font-size: 12px;
            padding-top: 3px;
        }

        .gst {
            text-align: center;
            font-size: 12px;
            padding-bottom: 10px;
        }

        .content {
            padding-left: 18px;
            padding-right: 18px;
            padding-bottom: 15px;
        }

        .info {
            width: 100%;
        }

        .info td {
            border: none;
            padding: 7px 0;
            line-height: 18px;
        }

        .bold {
            font-weight: bold;
        }

        .service-table {
            border: 2px solid #000;
            width: 100%;
            padding: 10px;
            border-collapse: collapse;
            margin-top: 6px;
            margin-bottom: 8px;
        }

        .service-table th {
            border: 2px solid #000;
            padding: 10px;
            line-height: 18px;
            font-size: 12px;
        }

        .service-table td {
            border: 2px solid #000;
            padding: 10px;
            line-height: 18px;
            font-size: 12px;
        }

        .left {
            text-align: left;
        }

        p {
            margin: 0 0 6px 0;
            font-size: 12px;
            line-height: 18px;
        }

        ol {
            margin-top: 5px;
            margin-left: 18px;
            padding-left: 0;
        }

        ol li {
            margin-bottom: 6px;
            line-height: 18px;
            font-size: 12px;
        }

        img {
            vertical-align: middle;
        }

        br {
            line-height: 10px;
        }

        .footer {
            width: 100%;
            margin-top: 20px;
        }

        .footer td {
            border: none;
            vertical-align: top;
            font-size: 12px;
        }
    </style>

</head>

<body>

    <table class="page">

        <tr>

            <td>

                <table class="header" width="100%">

                    <tr>

                        <td class="logo">

                            <img src="{{ storage_path('app/public/logo/anvis-logo.png') }}" width="165">

                        </td>

                    </tr>

                    <tr>

                        <td class="company">

                            ANVIS DIGITAL PVT.LTD

                        </td>

                    </tr>

                    <tr>

                        <td class="title">

                            Service Order

                        </td>

                    </tr>

                    <tr>

                        <td class="address">

                            314 - Parvati Industrial Estate,
                            New Sun Mill Compound,
                            Lower Parel,
                            Mumbai,
                            INDIA - 400013

                        </td>

                    </tr>

                    <tr>

                        <td class="gst">

                            GST : 27AAMCA3257A1ZX

                        </td>

                    </tr>

                </table>

                <div class="content">

                    <table class="info">

                        <tr>

                            <td width="70%">

                                <b>Service Order No :
                                    {{ $roForm->ro_number }}</b>

                            </td>

                            <td>

                                <b>Date :</b>

                                {{ \Carbon\Carbon::parse($roForm->created_at)->format('M. d, Y') }}

                            </td>

                        </tr>

                    </table>

                    <br>

                    <p>TO,</p>

                    <p class="bold">

                        {{ $roForm->vendor_name }}

                    </p>

                    <p>

                        {{ $roForm->vendor_address }}

                    </p>

                    <p>

                        <b>GSTIN :</b>

                        {{ $roForm->vendor_gst_no }}

                    </p>

                    <p>

                        <b>Contact :</b>

                        {{ $roForm->vendor_contact }}

                    </p>

                    <p>

                        <b>Email id -</b>

                        {{ $roForm->vendor_email }}

                    </p>

                    <br>

                    <p>

                        Dear Sir/s

                    </p>

                    <p>

                        We hereby place an order on you for the following services on the terms &
                        conditions attached with this.

                    </p>

                    <p style="margin-bottom:8px;">
                        <b>Service Description:</b>
                    </p>

                    <p>

                    <p style="margin-bottom:10px;">
                        <b>Client's Name :- {{ $roForm->client_name }}</b>
                    </p>

                    </p>

                    <table class="service-table">

                        <thead>

                            <tr>

                                <th width="22%">Service</th>

                                <th width="12%">Ad Type</th>

                                <th width="12%">Ad Unit</th>

                                <th width="18%">Deliverables</th>

                                <th width="10%">Volume</th>

                                <th width="13%">Bid</th>

                                <th width="13%">Amount</th>

                            </tr>

                        </thead>

                        <tbody>

                            <tr>

                                <td class="left">
                                    {{ $roForm->service }}
                                </td>

                                <td>
                                    {{ $roForm->ad_type }}
                                </td>

                                <td>
                                    {{ $roForm->ad_unit }}
                                </td>

                                <td>
                                    {{ $roForm->deliverables }}
                                </td>

                                <td>
                                    {{ number_format($roForm->volume, 2) }}
                                </td>

                                <td>
                                    {{ number_format($roForm->bid, 2) }}
                                </td>

                                <td>
                                    {{ number_format($roForm->total_amount, 2) }}
                                </td>

                            </tr>

                        </tbody>

                    </table>

                    <br>

                    <table width="100%" style="border:none;">

                        <tr>

                            <td style="border:none;padding:0;">

                                <b>Delivery/job completion date :</b>

                                {{ \Carbon\Carbon::parse($roForm->completion_date)->format('d-m-Y') }}

                            </td>

                        </tr>

                    </table>

                    <br>

                    <p>

                        <b>Payment terms &amp; conditions :</b>

                    </p>

                    <ol>

                        <li>

                            Above price is net to ANVIS DIGITAL PVT.LTD.
                            Exclusive of any Govt. Taxes.

                        </li>

                        <li>

                            Payment Terms : Net 60 Days.

                        </li>

                        <li>

                            Publisher to provide access to reporting interface/screenshots.

                        </li>

                        <li>

                            RO Details should be kept confidential between the two involved parties.
                            It shouldn't be disclosed to any other party.

                        </li>

                    </ol>

                    <br>

                    <p>

                        Please sign &amp; return the duplicate copy of this Service Order
                        as token of your acceptance.

                    </p>

                    <br><br>

                    <table class="footer" width="100%">

                        <tr>

                            <td width="50%" align="left">

                                <b>

                                    For ANVIS Digital Pvt.Ltd.

                                </b>

                                <br><br><br>

                                <img src="{{ storage_path('app/public/signatures/sign.png') }}" width="120">

                                <br><br>

                                <b>

                                    Authorized Signatory

                                </b>

                                <br><br>

                                Created by :

                                {{ $roForm->createdBy->first_name ?? '' }}

                                {{ $roForm->createdBy->last_name ?? '' }}

                                <br>

                                Approval Status :

                                {{ ucfirst($roForm->status) }}

                            </td>

                            <td width="50%" align="right">

                                <b>

                                    Accepted &amp; Confirmed

                                </b>

                                <br><br><br><br><br><br><br>

                                _____________________

                                <br><br>

                                <b>

                                    Signature of Seller

                                </b>

                            </td>

                        </tr>

                    </table>

                </div>

            </td>

        </tr>

    </table>

</body>

</html>
