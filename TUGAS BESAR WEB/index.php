<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rental Kendaraan</title>
    <link rel="stylesheet" href="assets/css/style.css">  <!-- Path CSS -->
    <style>
        /* Reset CSS */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            color: #333;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        header {
            background-color: #343a40;
            width: 100%;
            color: white;
            padding: 20px;
            text-align: center;
        }

        header h1 {
            font-size: 2.5rem;
        }

        nav {
            margin-top: 10px;
        }

        nav a {
            color: white;
            margin: 0 10px;
            text-decoration: none;
            font-size: 1.2rem;
        }

        nav a:hover {
            text-decoration: underline;
        }

        .hero {
            padding: 40px;
            text-align: center;
            margin-top: 20px;
            width: 80%;
            border-radius: 8px;
        }

        .hero h1 {
            font-size: 2.8rem;
        }

        .hero p {
            font-size: 1.3rem;
        }

        .content {
            text-align: center;
            margin-top: 40px;
            width: 80%;
        }

        .content h2 {
            font-size: 2rem;
            margin-bottom: 20px;
            color: #333;
        }

        /* Style for Vehicle List */
        .vehicle-list {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            margin-top: 20px;
        }

        .vehicle-item {
            width: 250px;
            background-color: #fff;
            padding: 20px;
            border: 2px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: all 0.3s ease;
        }

        .vehicle-item:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .vehicle-item img {
            width: 100%;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .vehicle-item h3 {
            font-size: 1.6rem;
            color: #333;
            margin-bottom: 10px;
        }

        .vehicle-item p {
            font-size: 1.1rem;
            color: #777;
            margin-bottom: 10px;
        }

        .vehicle-item .price {
            font-size: 1.2rem;
            color: #28a745;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .vehicle-item .btn-rent {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .vehicle-item .btn-rent:hover {
            background-color: #0056b3;
        }

        footer {
            margin-top: 40px;
            background-color: #343a40;
            color: white;
            text-align: center;
            padding: 20px;
            width: 100%;
        }

        footer p {
            font-size: 1.1rem;
        }

    </style>
</head>
<body>
    <header><br><br><br><br><br><br><br><br><br><br><br><br><br>
        <h1>Rental Kendaraan</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="login.php">Login</a>
        </nav>
    </header>

    <section class="hero">
        <h1>Selamat Datang di Rental Kendaraan</h1>
        <p>Solusi terbaik untuk kebutuhan transportasi Anda!</p>
    </section>

    <section class="content">
        <h2>Layanan Kami</h2>
        <p>Kami menyediakan berbagai jenis kendaraan dengan harga terjangkau.</p>
    </section>
    
    <section class="hero">
        <!-- Daftar Mobil yang Tersedia -->
        <h2>Mobil yang Tersedia</h2>
        <div class="vehicle-list">
            <div class="vehicle-item">
                <img src="assets/img/download (1).jpg" alt="Mobil 1">
                <h3>Honda Civic</h3>
                <p>Mobil sedan nyaman dan efisien untuk perjalanan jauh.</p>
                <p class="price">Rp 500.000/hari</p>
                <button class="btn-rent">Sewa Sekarang</button>
            </div>

            <div class="vehicle-item">
                <img src="assets/img/download.jpg" alt="Mobil 2">
                <h3>Toyota Avanza</h3>
                <p>Mobil keluarga yang ideal untuk perjalanan dengan kenyamanan ekstra.</p>
                <p class="price">Rp 450.000/hari</p>
                <button class="btn-rent">Sewa Sekarang</button>
            </div>

            <div class="vehicle-item">
                <img src="assets/img/download (2).jpg" alt="Mobil 3">
                <h3>Mitsubishi Xpander</h3>
                <p>MPV dengan desain modern dan ruang kabin yang luas.</p>
                <p class="price">Rp 600.000/hari</p>
                <button class="btn-rent">Sewa Sekarang</button>
            </div>

            <div class="vehicle-item">
                <img src="assets/img/download (3).jpg" alt="Mobil 4">
                <h3>BMW X5</h3>
                <p>Mobil mewah dengan fitur lengkap dan performa luar biasa.</p>
                <p class="price">Rp 1.200.000/hari</p>
                <button class="btn-rent">Sewa Sekarang</button>
            </div>
        </div>
    </section>

    <footer>
        <p>&copy; 2024 Rental Kendaraan. Semua Hak Dilindungi.</p>
    </footer>
</body>
</html>
