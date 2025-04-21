<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Sales Transaction</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container">
    <h1 class="mt-4 text-success">Halaman Transaksi POS</h1>
    <p>Silakan masukkan data transaksi penjualan.</p>

    <form>
        <div class="mb-3">
            <label for="product" class="form-label">Nama Produk</label>
            <input type="text" class="form-control" id="product" placeholder="Masukkan nama produk">
        </div>
        <div class="mb-3">
            <label for="quantity" class="form-label">Jumlah</label>
            <input type="number" class="form-control" id="quantity" placeholder="Masukkan jumlah">
        </div>
        <div class="mb-3">
            <label for="price" class="form-label">Harga</label>
            <input type="text" class="form-control" id="price" placeholder="Masukkan harga">
        </div>
        <button type="submit" class="btn btn-primary">Proses Transaksi</button>
    </form>

    <a href="/" class="btn btn-secondary mt-3">Kembali ke Home</a>
</body>
</html>
