<!DOCTYPE html>
<html>

<body>

    <p>Dear Accounts Team,</p>

    <p>

        Please find attached the Service Order.

    </p>

    <table>

        <tr>
            <td><b>RO Number</b></td>
            <td>{{ $roForm->ro_number }}</td>
        </tr>

        <tr>
            <td><b>Vendor</b></td>
            <td>{{ $roForm->vendor_name }}</td>
        </tr>

        <tr>
            <td><b>Client</b></td>
            <td>{{ $roForm->client_name }}</td>
        </tr>

        <tr>
            <td><b>Total Amount</b></td>
            <td>₹ {{ number_format($roForm->total_amount, 2) }}</td>
        </tr>

    </table>

    <br>

    Regards,

    <br>

    <b>ANVIS DIGITAL PVT. LTD.</b>

</body>

</html>
