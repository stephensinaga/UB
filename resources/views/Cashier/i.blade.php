@extends('layout.masterFile')
@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Surat Izin</h1>
                    </div>
                    <section class="content mt-4">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header d-flex flex-wrap align-items-center">
                                            @if (session('username') == 'Guru Piket')
                                            @else
                                                <a href="#" class="btn btn-primary mr-2" data-bs-toggle="modal"
                                                    data-bs-target="#exampleModal">
                                                    <i class="fa fa-plus"></i> Buat Surat
                                                </a>
                                            @endif
                                        </div>
                                        <div class="container">
                                            <div class="row">
                                                <div class="col-sm">
                                                    <form action="{{ route('suratizin.cetak') }}" class="p-3"
                                                        method="POST">
                                                        @csrf
                                                        <div class="form-group">
                                                            <label for="nomor">Nomor Surat</label>
                                                            <input type="text" class="form-control" id="nomor"
                                                                name="nomor" placeholder="Nomor Surat" required>
                                                        </div>
                                                        <div class="form-group mb-3">
                                                            <label for="perihal">Perihal</label>
                                                            <select class="form-control" id="perihal" name="perihal" required>
                                                                <option value="" disabled selected>Pilih Perihal</option>
                                                                <option value="Surat Izin Keluar Lingkungan Sekolah/Kelas">Surat Izin Keluar Lingkungan Sekolah/Kelas</option>
                                                                <option value="Surat Izin Masuk">Surat Izin Masuk</option>
                                                                <option value="Surat Izin Pulang">Surat Izin Pulang</option>
                                                            </select>
                                                        </div>

                                                        <div class="form-group mb-3">
                                                            <label for="nis">Nama Siswa</label>
                                                            <input type="text" class="form-control" name="nis"
                                                                id="nis" placeholder="Nama Siswa" required>
                                                            <small id="nisAlert" class="text-danger"
                                                                style="display: none;">Nama siswa tidak ada.</small>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="jam_pelajaran">Jam Pelajaran</label>
                                                            <input type="number" class="form-control" id="jam_pelajaran"
                                                                name="jam_pelajaran" placeholder="Jam Pelajaran"
                                                                min="1" max="10" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="keterangan">Alasan</label>
                                                            <input type="text" class="form-control" id="keterangan"
                                                                name="keterangan" placeholder="Alasan" required>
                                                        </div>
                                                        <button type="submit" class="btn btn-primary">Buat</button>
                                                        <script>
                                                            function submitAndPrint() {
                                                                // Ambil nilai dari form
                                                                const formData = {
                                                                    nomor: document.getElementById('nomor').value,
                                                                    perihal: document.getElementById('perihal').value,
                                                                    nama: document.getElementById('nama').value,
                                                                    jam_pelajaran: document.getElementById('jam_pelajaran').value,
                                                                    keterangan: document.getElementById('keterangan').value,
                                                                    _token: '{{ csrf_token() }}'
                                                                };

                                                                // Kirim data menggunakan AJAX
                                                                fetch("{{ route('suratizin.cetak') }}", {
                                                                        method: "POST",
                                                                        headers: {
                                                                            "Content-Type": "application/json",
                                                                            "X-CSRF-Token": formData._token
                                                                        },
                                                                        body: JSON.stringify(formData)
                                                                    })
                                                                    .then(response => response.json())
                                                                    .then(data => {
                                                                        if (data.success) {
                                                                            window.print();
                                                                        } else {
                                                                            alert("Gagal mencetak surat izin.");
                                                                        }
                                                                    })
                                                                    .catch(error => console.error("Error:", error));
                                                            }
                                                        </script>
                                                    </form>
                                                </div>
                                                <div class="col-sm p-2">

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
@endsection
