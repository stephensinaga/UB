<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 10px;
            width: 80mm;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            padding: 5px;
            text-align: left;
        }
    </style>
</head>
<body>
    <h5>No. Invoice: {{ $invoice->id }}</h5>
    <p>Tanggal: {{ $invoice->created_at }}</p>
    <p>Kasir: {{ $invoice->cashier }}</p>
    <p>Pelanggan: {{ $invoice->customer }}</p>

    <h6>Detail Pemesanan</h6>
    <table>
        <thead>
            <tr>
                <th>Nama Produk</th>
                <th>Kode</th>
                <th>Jumlah</th>
                <th>Harga</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            <tr>
                <td>{{ $order->product_name }}</td>
                <td>{{ $order->product_code }}</td>
                <td>{{ $order->qty }}</td>
                <td>{{ number_format($order->product_price, 0) }}</td>
                <td>{{ number_format($order->qty * $order->product_price, 0) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h6>Total: {{ number_format($invoice->grandtotal, 0) }}</h6>
    <p>Metode Pembayaran: {{ $invoice->payment }}</p>
    <p>Uang Dibayar: {{ number_format($invoice->cash, 0) }}</p>
    <p>Kembalian: {{ number_format($invoice->changes, 0) }}</p>
</body>
</html>
