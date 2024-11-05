@php
$user = Auth::user();
@endphp

<aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

        <li class="nav-item">
            <a class="nav-link " href="{{ route('Dashboard') }}">
                <i class="bi bi-grid"></i>
                <span>Dashboard</span>
            </a>
        </li>
        @if ($user->role === 'admin')
        <li class="nav-item">
            <a class="nav-link " href="{{ route('CreateProductView') }}">
                <i class="bi bi-box-seam"></i>
                <span>Product</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link " href="{{ route('StorageView') }}">
                <i class="bi bi-boxes"></i>
                <span>Material Stock</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link " href="{{ route('WeeklyReceiptsView') }}">
                <i class="bi bi-receipt"></i>
                <span>Weekly Receipts</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link " href="{{ route('PoBlade') }}">
                <i class="bi bi-bag-check"></i>
                <span>Pre Order</span>
            </a>
        </li>
        @endif

        @if ($user->role === 'cashier')
        <li class="nav-item">
            <a class="nav-link " href="{{ route('CashierView') }}">
                <i class="bi bi-minecart"></i>
                <span>Cashier</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link " href="{{ route('ListOrder') }}">
                <i class="bi bi-file-text"></i>
                <span>Pending Order</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link " href="{{ route('HistoryPenjualanCashier') }}">
                <i class="bi bi-calendar-check"></i>
                <span>Sales History</span>
            </a>
        </li>
        @endif

        @if ($user->role === 'admin')
        <li class="nav-item">
            <a class="nav-link" href="{{ route('LaporanPenjualan') }}">
                <i class="bi bi-journal-text"></i>
                <span>Sales Report</span>
            </a>
        </li>
        @endif

    </ul>

</aside>
<style>
    .sidebar {
        width: 60px;
        transition: width 0.3s;
    }

    .sidebar-nav .nav-link {
        justify-content: center;
        padding: 10px;
        text-align: center;
    }

    .sidebar-nav .nav-link span {
        display: none;
    }

    .sidebar-nav .nav-link i {
        font-size: 26px;
    }

    .sidebar:hover {
        width: 200px;
    }

    .sidebar:hover .nav-link span {
        display: inline;
    }
</style>
