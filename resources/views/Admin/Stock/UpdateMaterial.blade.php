<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <title>Updating Storage</title>
</head>

<body>
    <h1>Updating Storage</h1>
    <form action="javascript:void(0)" method="POST" id="UpdateMaterial">
        @csrf
        @method('POST')
        <input type="hidden" name="id" value="{{ $item->id }}">
        <input type="text" name="id_material" placeholder="Material ID" value="{{ $item->id_material }}" readonly><br>
        <input type="text" name="material" placeholder="Material Name" value="{{ $item->material }}"><br>
        <input type="number" name="qty" placeholder="QTY" value="{{ $item->qty }}"><br>
        <input type="text" name="satuan" placeholder="Satuan" value="{{ $item->satuan }}"><br>
        <textarea name="keterangan" cols="30" rows="10">{{ $item->keterangan }}</textarea>
        <button type="submit">Update</button>
    </form>
    

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $(document).ready(function() {
            $('#UpdateMaterial').on('submit', function(event) {
                event.preventDefault();
                var formData = new FormData(this);

                $.ajax({
                    url: "{{ route('UpdateProcess') }}",
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(result) {
                        if (result.success) {
                            // Jika berhasil, redirect ke StorageView
                            window.location.href =
                            "{{ route('StorageView') }}"; // Redirecting to the storage view
                        } else {
                            // Jika gagal, tampilkan pesan kesalahan
                            alert(result.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText); // Debugging error
                        alert('An error occurred while updating the material.');
                    }
                });
            });

        });
    </script>

</body>

</html>
