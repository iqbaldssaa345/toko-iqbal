<?php
$conn = mysqli_connect(
"localhost",
"root",
"12345678",
"db_toko_iqbal2");

if(!$conn){
    die("Koneksi gagal");
}

// Migrasi otomatis: tambahkan kolom bukti_pembayaran jika belum ada
$cek_kolom = mysqli_query($conn, "SHOW COLUMNS FROM pembayaran LIKE 'bukti_pembayaran'");
if ($cek_kolom && mysqli_num_rows($cek_kolom) == 0) {
    mysqli_query($conn, "ALTER TABLE pembayaran ADD COLUMN bukti_pembayaran VARCHAR(255) DEFAULT NULL");
}

// Migrasi otomatis: ganti kolom stok dengan pre_order jika masih ada
$cek_stok = mysqli_query($conn, "SHOW COLUMNS FROM produk LIKE 'stok'");
if ($cek_stok && mysqli_num_rows($cek_stok) > 0) {
    mysqli_query($conn, "ALTER TABLE produk DROP COLUMN stok");
}
$cek_preorder = mysqli_query($conn, "SHOW COLUMNS FROM produk LIKE 'pre_order'");
if ($cek_preorder && mysqli_num_rows($cek_preorder) == 0) {
    mysqli_query($conn, "ALTER TABLE produk ADD COLUMN pre_order VARCHAR(100) DEFAULT '1 Hari'");
}

// Migrasi otomatis: tambahkan kolom estimasi ke tabel ongkir jika belum ada
$cek_estimasi = mysqli_query($conn, "SHOW COLUMNS FROM ongkir LIKE 'estimasi'");
if ($cek_estimasi && mysqli_num_rows($cek_estimasi) == 0) {
    mysqli_query($conn, "ALTER TABLE ongkir ADD COLUMN estimasi VARCHAR(50) DEFAULT '1-2 Hari'");
}

// Migrasi otomatis: tambahkan kolom status_pengiriman ke tabel pesanan jika belum ada
$cek_status_pengiriman = mysqli_query($conn, "SHOW COLUMNS FROM pesanan LIKE 'status_pengiriman'");
if ($cek_status_pengiriman && mysqli_num_rows($cek_status_pengiriman) == 0) {
    mysqli_query($conn, "ALTER TABLE pesanan ADD COLUMN status_pengiriman VARCHAR(50) DEFAULT 'Proses (Estimasi)'");
}

// Migrasi otomatis: perbarui data ongkir dengan data asli & realistik berdasarkan kurir, jarak & estimasi
$cek_dummy_ongkir = mysqli_query($conn, "SELECT id FROM ongkir WHERE nama_jasa IN ('jne', 'antareja', 'GoFood', 'GrabFood')");
if ($cek_dummy_ongkir && mysqli_num_rows($cek_dummy_ongkir) > 0) {
    mysqli_query($conn, "UPDATE ongkir SET nama_jasa='Kurir Toko (Jarak < 5 km)', biaya=10000, estimasi='Hari Ini (1-2 Jam)' WHERE id=1");
    mysqli_query($conn, "UPDATE ongkir SET nama_jasa='GoSend Instant (Jarak < 20 km)', biaya=18000, estimasi='1-2 Jam' WHERE id=2");
    mysqli_query($conn, "UPDATE ongkir SET nama_jasa='JNE REG (Jabodetabek)', biaya=11000, estimasi='1-2 Hari' WHERE id=4");
    mysqli_query($conn, "UPDATE ongkir SET nama_jasa='GrabExpress Sameday', biaya=20000, estimasi='3-5 Jam' WHERE id=6");
    
    $cek_exist = mysqli_query($conn, "SELECT id FROM ongkir WHERE nama_jasa LIKE '%JNE YES%'");
    if($cek_exist && mysqli_num_rows($cek_exist) == 0){
        mysqli_query($conn, "INSERT INTO ongkir (nama_jasa, biaya, estimasi) VALUES 
            ('Kurir Toko (Jarak 5-15 km)', 18000, 'Hari Ini (2-4 Jam)'),
            ('JNE YES (Yakin Esok Sampai)', 22000, '1 Hari'),
            ('J&T Express (Regular)', 12000, '1-2 Hari'),
            ('SiCepat BEST (Besok Sampai)', 21000, '1 Hari'),
            ('Mobil Catering Special (Bulk Order)', 45000, 'Hari Ini (Jam Acara)')
        ");
    }
}

// Migrasi otomatis: buat tabel testimoni jika belum ada
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS testimoni (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    bintang INT DEFAULT 5,
    isi_testimoni TEXT NOT NULL,
    tanggal TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
?>