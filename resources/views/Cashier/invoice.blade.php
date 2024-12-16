<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $mainOrder->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
            width: 80mm; /* Lebar kertas thermal 80mm */
        }
        .invoice {
            padding: 10px;
        }
        .center {
            text-align: center;
        }
        .line {
            border-bottom: 1px dashed #000;
            margin: 10px 0;
        }
        .details {
            margin-bottom: 10px;
        }
        .details th, .details td {
            text-align: left;
        }
        .total {
            font-weight: bold;
            margin-top: 10px;
        }
    </style>
</head>
<body onload="window.print();">
    <div class="invoice">
        <div class="center">
            <h2>Dapur Negeri</h2>
            <p>Jl. Contoh Alamat, Kota</p>
        </div>
        <div class="line"></div>
        <div class="details">
            <p><strong>Invoice:</strong> {{ $mainOrder->id }}</p>
            <p><strong>Date:</strong> {{ $mainOrder->created_at->format('d/m/Y') }}</p>
            <p><strong>Cashier:</strong> {{ $mainOrder->cashier }}</p>
            <p><strong>Customer:</strong> {{ $mainOrder->customer }}</p>
            @if(isset($mainOrder->table_number))
                <p><strong>Table No:</strong> {{ $mainOrder->table_number }}</p>
            @endif
            <p><strong>Payment Method:</strong> {{ ucfirst($mainOrder->payment) }}</p>
        </div>
        <div class="line"></div>
        <table width="100%">
            @foreach($mainOrder->orders as $order)
                <tr>
                    <td>{{ $order->product_name }}</td>
                    <td>{{ $order->qty }} x Rp{{ number_format($order->product_price, 0, ',', '.') }}</td>
                    <td>Rp{{ number_format($order->qty * $order->product_price, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </table>
        <div class="line"></div>
        <p class="total">Total: Rp{{ number_format($mainOrder->grandtotal, 0, ',', '.') }}</p>
        @if($mainOrder->payment === 'cash')
            <p>Cash Paid: Rp{{ number_format($mainOrder->cash, 0, ',', '.') }}</p>
            <p>Change: Rp{{ number_format($mainOrder->changes, 0, ',', '.') }}</p>
        @endif
        <div class="line"></div>
        <div class="center">
            <p>Thank you for your purchase!</p>
        </div>
    </div>
</body>
</html>
