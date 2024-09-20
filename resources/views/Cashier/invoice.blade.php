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
            margin: auto;
            padding: 20px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
        }
        .invoice-header {
            text-align: center;
        }
        .invoice-details, .order-details {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        .invoice-details td, .order-details td, .order-details th {
            padding: 8px;
            border: 1px solid #ddd;
        }
        .order-details th {
            background-color: #f2f2f2;
        }
        .text-right {
            text-align: right;
        }
    </style>
</head>
<body>

<div class="invoice-box">
    <h2 class="invoice-header">Invoice</h2>

    <table class="invoice-details">
        <tr>
            <td><strong>Invoice No:</strong> {{ $mainOrder->id }}</td>
            <td class="text-right"><strong>Date:</strong> {{ $mainOrder->created_at->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td><strong>Cashier:</strong> {{ $mainOrder->cashier }}</td>
            <td class="text-right"><strong>Customer:</strong> {{ $mainOrder->customer }}</td>
        </tr>
        <tr>
            <td><strong>Payment Method:</strong> {{ ucfirst($mainOrder->payment) }}</td>
            @if($mainOrder->payment === 'cash')
                <td class="text-right"><strong>Paid:</strong> Rp{{ number_format($mainOrder->cash, 0, ',', '.') }}</td>
            @else
                <td class="text-right"><strong>Transfer Proof:</strong> See Attached</td>
            @endif
        </tr>
    </table>

    <table class="order-details">
        <thead>
            <tr>
                <th>Product</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($mainOrder->orders as $order)
                <tr>
                    <td>{{ $order->product_name }}</td>
                    <td>{{ $order->qty }}</td>
                    <td>Rp{{ number_format($order->product_price, 0, ',', '.') }}</td>
                    <td>Rp{{ number_format($order->qty * $order->product_price, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-right"><strong>Total:</strong></td>
                <td><strong>Rp{{ number_format($mainOrder->grandtotal, 0, ',', '.') }}</strong></td>
            </tr>
            @if($mainOrder->payment === 'cash')
                <tr>
                    <td colspan="3" class="text-right"><strong>Cash Paid:</strong></td>
                    <td><strong>Rp{{ number_format($mainOrder->cash, 0, ',', '.') }}</strong></td>
                </tr>
                <tr>
                    <td colspan="3" class="text-right"><strong>Change:</strong></td>
                    <td><strong>Rp{{ number_format($mainOrder->changes, 0, ',', '.') }}</strong></td>
                </tr>
            @endif
        </tfoot>
    </table>

    <p class="text-right">Thank you for your purchase!</p>
</div>

</body>
</html>
