<!DOCTYPE html>
<html>

<head>
    <title>Sales Report</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 10px;
            text-align: left;
        }
    </style>
</head>

<body>
    <h1>Sales Report</h1>

    @foreach ($mainOrders as $mainOrder)
        <h2>Main Order #{{ $mainOrder->id }}</h2>
        <p>Cashier: {{ $mainOrder->cashier }}</p>
        <p>Customer: {{ $mainOrder->customer }}</p>
        <p>Grand Total: {{ $mainOrder->grandtotal }}</p>
        @if ($mainOrder->payment == 'cash')
            <p>Cash: {{ $mainOrder->cash }}</p>
            <p>Change: {{ $mainOrder->changes }}</p>
        @endif
        <p>Payment Method: {{ ucfirst($mainOrder->payment) }}</p>

        <table>
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Product Code</th>
                    <th>Category</th>
                    <th>Quantity</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($mainOrder->orders as $order)
                    <tr>
                        <td>{{ $order->product_name }}</td>
                        <td>{{ $order->product_code }}</td>
                        <td>{{ $order->product_category }}</td>
                        <td>{{ $order->qty }}</td>
                        <td>{{ $order->product_price }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <hr>
    @endforeach
</body>

</html>
