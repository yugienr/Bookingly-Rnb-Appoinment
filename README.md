# Bookingly - Appointment Booking For WooCommerce & RnB

Bookingly adalah plugin pemesanan janji temu yang ramah pengguna untuk WooCommerce, dibuat sebagai ekstensi dari RnB. Ekstensi yang kuat ini memungkinkan Anda untuk mengintegrasikan sistem pemesanan ke situs web WordPress Anda.

## Fitur
- Pemesanan janji temu
- Integrasi dengan WooCommerce
- Dukungan untuk berbagai jenis layanan

## Instalasi
1. Unduh plugin ini dari [GitHub](https://github.com/yugienr/Bookingly-Rnb-Appoinment).
2. Unggah ke direktori `wp-content/plugins` di situs WordPress Anda.
3. Aktifkan plugin melalui menu 'Plugins' di WordPress.

## Penggunaan
1. Buat produk baru di WooCommerce.
2. Pilih opsi pemesanan dari pengaturan produk.
3. Atur jadwal dan slot waktu yang tersedia.

## Kontribusi
Kontribusi selalu diterima! Silakan buat pull request atau buka masalah untuk perbaikan atau fitur baru.

## Lisensi
Plugin ini dilisensikan di bawah GPL v2 atau yang lebih baru.

## Kontak
Untuk pertanyaan lebih lanjut, hubungi [redqteam](https://redq.io).


CREATE TABLE IF NOT EXISTS `rnb_availability` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `inventory_id` int(11) NOT NULL,
  `pickup_datetime` datetime NOT NULL,
  `return_datetime` datetime NOT NULL,
  `check_in_date` date DEFAULT NULL,
  `check_out_date` date DEFAULT NULL,
  `room_type` varchar(255) DEFAULT NULL,
  `block_by` varchar(50) DEFAULT NULL,
  `delete_status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Add any additional tables or columns as needed
