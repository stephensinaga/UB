<aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

        <li class="nav-item">
            <a class="nav-link " href="{{ route('Dashboard') }}">
                <i class="bi bi-grid"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link " href="{{ route('CreateProductView') }}">
                <i class="bi bi-box-fill"></i>
                <span>Barang</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link " href="{{ route('CashierView') }}">
                <i class="bi bi-box-fill"></i>
                <span>Order Product</span>
            </a>
        </li>


        <li class="nav-item">
            <a class="nav-link " href="{{ route('HistoryPenjualanCashier') }}">
                <i class="fa-solid fa-file-pen"></i>
                <span>History Penjualan</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="{{ route('LaporanPenjualan') }}">
                <i class="bi bi-box-fill"></i>
                <span>Laporan Penjualan</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="">
                <i class="bi bi-box-fill"></i>
                <span>Hutang - Piutang</span>
            </a>
        </li>
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
