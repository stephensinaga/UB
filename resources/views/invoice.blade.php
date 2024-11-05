<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $mainOrder->id }}</title>
    <style>
        @page {
            size: 80mm 80mm; /* Ukuran kertas 80mm x 80mm */
            margin: 0; /* Menghilangkan margin */
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px; /* Ukuran font untuk tampilan */
            margin: 0;
            padding: 5mm; /* Padding untuk isi konten */
        }

        h1, h2 {
            text-align: center;
        }

        .separator {
            border-top: 1px solid #000;
            margin: 10px 0;
        }

        .product {
            display: flex;
            justify-content: space-between;
        }

        .total {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1>Invoice No: {{ $mainOrder->id }}</h1>
    <p>Date: {{ $mainOrder->created_at->format('d/m/Y') }}</p>
    <p>Cashier: {{ $mainOrder->cashier }}</p>
    <p>Customer: {{ $mainOrder->customer }}</p>

    @if (isset($mainOrder->table_number))
        <p>Table No: {{ $mainOrder->table_number }}</p>
    @endif

    <p>Payment Method: {{ ucfirst($mainOrder->payment) }}</p>
    @if ($mainOrder->payment === 'cash')
        <p>Paid: Rp{{ number_format($mainOrder->cash, 0, ',', '.') }}</p>
    @else
        <p>Transfer Proof: See Attached</p>
    @endif

    <div class="separator"></div>
    <h2>Order Details:</h2>
    @foreach($mainOrder->orders as $order)
        <div class="product">
            <span>{{ $order->product_name }}</span>
            <span>Rp{{ number_format($order->product_price, 0, ',', '.') }} * {{ $order->qty }}</span>
        </div>
    @endforeach

    <div class="separator"></div>
    <div class="total">
        <p>Total: Rp{{ number_format($mainOrder->grandtotal, 0, ',', '.') }}</p>
    </div>

    @if ($mainOrder->payment === 'cash')
        <p>Cash Paid: Rp{{ number_format($mainOrder->cash, 0, ',', '.') }}</p>
        <p>Change: Rp{{ number_format($mainOrder->changes, 0, ',', '.') }}</p>
    @endif

    <div class="separator"></div>
    <p>Thank you for your purchase!</p>
</body>
</html>
