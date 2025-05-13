<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .invoice-box {
            width: 100%;
            padding: 20px;
            border: 1px solid #ddd;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .company-header {
            display: flex;
            align-items: center;
        }

        .company-header img {
            max-width: 200px;
            margin-right: 10px;
        }

        .company-details {
            font-size: 14px;
        }

        .invoice-details {
            text-align: right;
        }

        .title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
            /* Light grey border */
        }

        th,
        td {
            padding: 3px;
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        p {
            margin: 3px !important;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <div class="invoice-box">
        <!-- Header Section -->
        <div class="header">
            <!-- Company Logo & Address (Left) -->
            <div class="company-header" style="float: left">
                <img src="{{ public_path('images/branding/logo.png') }}" alt="Beyond BeanCounters Logo">
                <div class="company-details">
                    <strong>Beyond BeanCounters</strong><br>
                    CPA Centre, Ruaraka,<br>
                    Thika Superhighway, Nairobi, Kenya
                </div>
            </div>

            <!-- Invoice Title, Date & Customer Name (Right) -->
            <div class="invoice-details" style="float: right">
                <h2 class="title">INVOICE</h2>
                <p><strong>Invoice Date:</strong> {{ now()->format('Y-m-d') }}</p>
                <p><strong>Customer Name:</strong> {{ $lead->title }}</p>
                <p><strong>Ticket No:</strong> #{{ $lead->id }}</p>
            </div>

            <div style="clear: both"></div>
        </div>

        <!-- Transaction Table -->
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Description</th>
                    <th>Amount</th>
                    {{-- <th>Date</th> --}}
                </tr>
            </thead>
            <tbody>

                <tr>
                    <td>1</td>
                    <td>Amount</td>
                    <td>{{$lead->currency_name}} {{ number_format($lead->amount, 2) }}</td>
                </tr>
                @foreach ($transactions as $key=> $transaction)
                <tr>
                    <td>{{ $key+1+1 }}</td>
                    <td>{{ $transaction->description }}</td>
                    <td>{{$lead->currency_name}} {{ number_format($transaction->amount, 2) }}</td>
                    {{-- <td>{{ $transaction->created_at->format('Y-m-d') }}</td> --}}
                </tr>
                @endforeach


                <tr>
                    <td colspan="2"><strong>TOTAL AMOUNT</strong></td>
                    <td><strong>{{$lead->currency_name}} {{ number_format($transactions->sum('amount')+$lead->amount, 2)
                            }}</strong>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</body>

</html>