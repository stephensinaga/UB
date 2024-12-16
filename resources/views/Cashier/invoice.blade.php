<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            width: 80mm; /* Sesuaikan ukuran dengan thermal printer */
        }
        .invoice {
            padding: 10px;
            line-height: 1.6;
        }
        .center {
            text-align: center;
            margin-bottom: 10px;
        }
        .line {
            border-top: 1px dashed #000;
            margin: 10px 0;
        }
        .details p, .total, table {
            font-size: 16px; /* Ukuran font lebih besar */
            margin: 5px 0;
        }
        .details p strong {
            font-weight: bold;
        }
        .details p {
            font-size: 14px;
            margin: 2px 0; /* Mengurangi margin atas dan bawah */
        }
        .total {
            text-align: right;
            font-size: 16px; /* Ukuran font lebih besar untuk total */
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 7px;
        }
        table td {
            font-size: 16px; /* Ukuran font lebih besar di tabel */
        }
        table td:first-child {
            width: 50%;
        }
        table td:nth-child(2) {
            text-align: center;
        }
        table td:last-child {
            text-align: right;
        }
        img {
            max-width: 80px;
            /* margin-bottom: 2px; */
        }
        .center p {
            font-size: 14px; /* Ukuran font lebih besar untuk pesan penutup */
        }
    </style>
</head>
<body onload="window.print();">
    <div class="invoice">
        <!-- Logo -->
        <div class="center">
            <img src="{{ asset('assets/img/dapur_negeri.png') }}" alt="Logo Dapur Negeri">
        </div>
        <!-- Garis -->
        <div class="line"></div>
        <!-- Detail Invoice -->
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
        <!-- Garis -->
        <div class="line"></div>
        <!-- Detail Pesanan -->
        <table>
            @foreach($mainOrder->orders as $order)
                <tr>
                    <td>{{ $order->product_name }}</td>
                    <td>{{ $order->qty }} x Rp{{ number_format($order->product_price, 0, ',', '.') }}</td>
                    {{-- <td>Rp{{ number_format($order->qty * $order->product_price, 0, ',', '.') }}</td> --}}
                </tr>
            @endforeach
        </table>
        <!-- Total -->
        <div class="line"></div>
        <p class="total">Total: Rp{{ number_format($mainOrder->grandtotal, 0, ',', '.') }}</p>
        @if($mainOrder->payment === 'cash')
            <p style="font-size: 14px;">Cash Paid: Rp{{ number_format($mainOrder->cash, 0, ',', '.') }}</p>
            <p style="font-size: 14px;">Change: Rp{{ number_format($mainOrder->changes, 0, ',', '.') }}</p>
        @endif
        <!-- Garis -->
        <div class="line"></div>
        <!-- Pesan Penutup -->
        <div class="center">
            <p>Thank you for your purchase!</p>
        </div>
    </div>
</body>
</html>
<script>
    window.onload = function() {
        window.print();

        // Redirect ke halaman cashier setelah selesai
        setTimeout(function() {
            window.opener.location.href = '/cashier/view'; // Ganti '/cashier' dengan URL cashier Anda
            window.close(); // Tutup tab cetak
        }, 1000);
    }
</script>
