-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 23, 2025 at 05:49 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `PTUD_Final`
--
CREATE DATABASE IF NOT EXISTS `PTUD_Final` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `PTUD_Final`;

-- --------------------------------------------------------

--
-- Table structure for table `anh_san_pham`
--

DROP TABLE IF EXISTS `anh_san_pham`;
CREATE TABLE `anh_san_pham` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `san_pham_id` bigint(20) UNSIGNED NOT NULL,
  `url_anh` varchar(500) NOT NULL,
  `thu_tu_hien_thi` int(11) NOT NULL DEFAULT 0,
  `tao_luc` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `anh_san_pham`
--

INSERT INTO `anh_san_pham` (`id`, `san_pham_id`, `url_anh`, `thu_tu_hien_thi`, `tao_luc`) VALUES
(1004, 19, 'PTUD_Final/images/ao-thun/ao-thun-basic-cotton/den.png', 0, '2025-12-20 17:16:31'),
(1005, 19, 'PTUD_Final/images/ao-thun/ao-thun-basic-cotton/trang.png', 1, '2025-12-20 17:16:31'),
(1006, 19, 'PTUD_Final/images/ao-thun/ao-thun-basic-cotton/be.png', 2, '2025-12-20 17:16:31'),
(1007, 19, 'PTUD_Final/images/ao-thun/ao-thun-basic-cotton/xam.png', 2, '2025-12-20 17:16:31'),
(1008, 19, 'PTUD_Final/images/ao-thun/ao-thun-basic-cotton/xanh.png', 2, '2025-12-20 17:16:31'),
(1009, 20, 'PTUD_Final/images/ao-thun/ao-thun-oversize-street/den.png', 0, '2025-12-20 17:16:31'),
(1010, 20, 'PTUD_Final/images/ao-thun/ao-thun-oversize-street/trang.png', 1, '2025-12-20 17:16:31'),
(1011, 20, 'PTUD_Final/images/ao-thun/ao-thun-oversize-street/be.png', 2, '2025-12-20 17:16:31'),
(1012, 20, 'PTUD_Final/images/ao-thun/ao-thun-oversize-street/xam.png', 2, '2025-12-20 17:16:31'),
(1013, 21, 'PTUD_Final/images/ao-thun/ao-thun-polo-minimal/den.png', 0, '2025-12-20 17:16:31'),
(1014, 21, 'PTUD_Final/images/ao-thun/ao-thun-polo-minimal/trang.png', 1, '2025-12-20 17:16:31'),
(1015, 21, 'PTUD_Final/images/ao-thun/ao-thun-polo-minimal/be.png', 2, '2025-12-20 17:16:31'),
(1016, 22, 'PTUD_Final/images/ao-so-mi/so-mi-oxford/trang.png', 0, '2025-12-23 09:13:09'),
(1018, 23, 'PTUD_Final/images/ao-so-mi/so-mi-ke-caro/trang.png', 1, '2025-12-20 17:16:31'),
(1019, 24, 'PTUD_Final/images/quan/quan-jeans-slim-fit/quan-jeans-slim-fit.png', 0, '2025-12-20 17:16:31'),
(1020, 25, 'PTUD_Final/images/quan/quan-kaki-regular/quan-kaki-regular.png', 0, '2025-12-20 17:16:31'),
(1021, 26, 'PTUD_Final/images/quan/quan-short-linen/trang.png', 1, '2025-12-20 17:16:31'),
(1022, 26, 'PTUD_Final/images/quan/quan-short-linen/be.png', 2, '2025-12-20 17:16:31'),
(1023, 27, 'PTUD_Final/images/ao-khoac/ao-khoac-bomber/hong.png', 0, '2025-12-20 17:16:31'),
(1024, 27, 'PTUD_Final/images/ao-khoac/ao-khoac-bomber/xanh.png', 1, '2025-12-20 17:16:31'),
(1025, 28, 'PTUD_Final/images/ao-khoac/ao-khoac-denim-jacket/ao-khoac-denim-jacket.png', 0, '2025-12-20 17:16:31'),
(1026, 29, 'PTUD_Final/images/phu-kien/non-luoi-trai-classic/xanh.png', 0, '2025-12-20 17:16:31'),
(1027, 29, 'PTUD_Final/images/phu-kien/non-luoi-trai-classic/trang.png', 1, '2025-12-20 17:16:31'),
(1028, 29, 'PTUD_Final/images/phu-kien/non-luoi-trai-classic/hong.png', 2, '2025-12-20 17:16:31'),
(1029, 30, 'PTUD_Final/images/phu-kien/tui-tote-canvas/trang.png', 1, '2025-12-20 17:16:31'),
(1030, 30, 'PTUD_Final/images/phu-kien/tui-tote-canvas/xam.png', 2, '2025-12-20 17:16:31'),
(1031, 31, 'PTUD_Final/images/hoodie/hoodie-oversize-basic/trang.png', 1, '2025-12-20 17:16:31'),
(1032, 31, 'PTUD_Final/images/hoodie/hoodie-oversize-basic/be.png', 2, '2025-12-20 17:16:31'),
(1033, 31, 'PTUD_Final/images/hoodie/hoodie-oversize-basic/hong.png', 2, '2025-12-20 17:16:31'),
(1034, 31, 'PTUD_Final/images/hoodie/hoodie-oversize-basic/xam.png', 2, '2025-12-20 17:16:31'),
(1035, 31, 'PTUD_Final/images/hoodie/hoodie-oversize-basic/xanh.png', 2, '2025-12-20 17:16:31'),
(1036, 32, 'PTUD_Final/images/hoodie/hoodie-zip-street/trang.png', 1, '2025-12-20 17:16:31');

-- --------------------------------------------------------

--
-- Table structure for table `banners`
--

DROP TABLE IF EXISTS `banners`;
CREATE TABLE `banners` (
  `id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `banners`
--

INSERT INTO `banners` (`id`, `image`, `title`, `content`, `status`) VALUES
(1, 'PTUD_Final/images/banner.jpg', 'Banner Chào Mừng', NULL, 0),
(2, 'PTUD_Final/images/banner1.jpg', 'THỜI TRANG & PHONG CÁCH', 'Bộ sưu tập mùa hè mới nhất 2025', 1);

-- --------------------------------------------------------

--
-- Table structure for table `chi_tiet_don_hang`
--

DROP TABLE IF EXISTS `chi_tiet_don_hang`;
CREATE TABLE `chi_tiet_don_hang` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `don_hang_id` bigint(20) UNSIGNED NOT NULL,
  `san_pham_id` bigint(20) UNSIGNED NOT NULL,
  `sku_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ten_san_pham` varchar(255) NOT NULL,
  `don_gia` decimal(12,2) NOT NULL,
  `so_luong` int(11) NOT NULL,
  `thanh_tien` decimal(12,2) NOT NULL,
  `tao_luc` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chi_tiet_don_hang`
--

INSERT INTO `chi_tiet_don_hang` (`id`, `don_hang_id`, `san_pham_id`, `sku_id`, `ten_san_pham`, `don_gia`, `so_luong`, `thanh_tien`, `tao_luc`) VALUES
(1, 1, 31, NULL, 'Hoodie Oversize Basic (SP31-M-DEN)', 399000.00, 1, 399000.00, '2025-12-20 09:08:46'),
(2, 2, 31, NULL, 'Hoodie Oversize Basic (SP31-M-TRANG)', 399000.00, 4, 1596000.00, '2025-12-19 09:13:30'),
(3, 3, 32, NULL, 'Hoodie Zip Street (SP32-S1-C2)', 459000.00, 5, 2295000.00, '2025-12-20 22:12:30'),
(4, 3, 26, NULL, 'Quần Short Linen (SP26-S1-C2)', 249000.00, 1, 249000.00, '2025-12-20 22:12:30'),
(5, 4, 27, NULL, 'Áo khoác Bomber (SP27-S-XANH)', 699000.00, 1, 699000.00, '2025-12-21 11:04:34'),
(6, 4, 32, NULL, 'Hoodie Zip Street (SP32-S2-C2)', 459000.00, 1, 459000.00, '2025-12-21 11:04:34'),
(7, 4, 30, NULL, 'Túi Tote Canvas (SP30-S3-C3)', 189000.00, 1, 189000.00, '2025-12-21 11:04:34'),
(8, 5, 32, NULL, 'Hoodie Zip Street (SP32-S4-C2)', 459000.00, 1, 459000.00, '2025-12-21 11:53:30'),
(9, 11, 32, 2146, 'Hoodie Zip Street (SP32-S2-C2)', 459000.00, 3, 1377000.00, '2025-12-22 06:30:54'),
(10, 12, 31, 2023, 'Hoodie Oversize Basic (SP31-S-DEN)', 399000.00, 1, 399000.00, '2025-12-22 08:58:53'),
(11, 12, 19, 2044, 'Áo thun Basic Cotton (SP19-S3-C4)', 199000.00, 1, 199000.00, '2025-12-22 08:58:53'),
(12, 13, 32, 2146, 'Hoodie Zip Street (SP32-S2-C2)', 459000.00, 4, 1836000.00, '2025-12-22 10:14:53'),
(13, 13, 31, 2023, 'Hoodie Oversize Basic (SP31-S-DEN)', 399000.00, 1, 399000.00, '2025-12-22 10:14:53'),
(14, 14, 31, 2023, 'Hoodie Oversize Basic (SP31-S-DEN)', 399000.00, 4, 1596000.00, '2025-12-22 10:57:53'),
(15, 15, 31, 2025, 'Hoodie Oversize Basic (SP31-M-DEN)', 399000.00, 1, 399000.00, '2025-12-22 10:57:53'),
(16, 16, 27, 2036, 'Áo khoác Bomber (Size: L | Màu: Xanh pastel) [SP27-L-XANH]', 699000.00, 4, 2796000.00, '2025-12-22 11:18:46');

-- --------------------------------------------------------

--
-- Table structure for table `chi_tiet_gio_hang`
--

DROP TABLE IF EXISTS `chi_tiet_gio_hang`;
CREATE TABLE `chi_tiet_gio_hang` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `gio_hang_id` bigint(20) UNSIGNED NOT NULL,
  `san_pham_id` bigint(20) UNSIGNED NOT NULL,
  `sku_id` bigint(20) UNSIGNED DEFAULT NULL,
  `so_luong` int(11) NOT NULL DEFAULT 1,
  `don_gia` decimal(12,2) NOT NULL,
  `tao_luc` datetime NOT NULL DEFAULT current_timestamp(),
  `cap_nhat_luc` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chi_tiet_gio_hang`
--

INSERT INTO `chi_tiet_gio_hang` (`id`, `gio_hang_id`, `san_pham_id`, `sku_id`, `so_luong`, `don_gia`, `tao_luc`, `cap_nhat_luc`) VALUES
(21, 3, 31, 2023, 1, 399000.00, '2025-12-22 11:22:32', '2025-12-22 11:22:32'),
(22, 3, 28, 2309, 1, 749000.00, '2025-12-23 08:56:53', '2025-12-23 08:56:53'),
(25, 3, 32, 2145, 1, 459000.00, '2025-12-23 13:18:43', '2025-12-23 13:18:43');

-- --------------------------------------------------------

--
-- Table structure for table `danh_muc_san_pham`
--

DROP TABLE IF EXISTS `danh_muc_san_pham`;
CREATE TABLE `danh_muc_san_pham` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ten_danh_muc` varchar(255) NOT NULL,
  `duong_dan` varchar(255) NOT NULL,
  `mo_ta` varchar(500) DEFAULT NULL,
  `trang_thai` enum('HOAT_DONG','NGUNG_HOAT_DONG') NOT NULL DEFAULT 'HOAT_DONG',
  `tao_luc` datetime NOT NULL DEFAULT current_timestamp(),
  `cap_nhat_luc` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `danh_muc_san_pham`
--

INSERT INTO `danh_muc_san_pham` (`id`, `ten_danh_muc`, `duong_dan`, `mo_ta`, `trang_thai`, `tao_luc`, `cap_nhat_luc`) VALUES
(1, 'Áo thun', 'ao-thun', 'Các loại áo thun nam nữ', 'HOAT_DONG', '2025-12-15 15:25:08', '2025-12-15 15:25:08'),
(2, 'Hoodie', 'hoodie', 'Áo hoodie', 'HOAT_DONG', '2025-12-15 15:25:08', '2025-12-18 10:19:23'),
(3, 'Quần', 'quan', 'Quần jeans, quần tây, quần short', 'HOAT_DONG', '2025-12-15 15:25:08', '2025-12-15 15:25:08'),
(4, 'Áo khoác', 'ao-khoac', 'Áo khoác thời trang', 'HOAT_DONG', '2025-12-15 15:25:08', '2025-12-15 15:25:08'),
(5, 'Áo sơ mi', 'ao-so-mi', 'Áo sơ mi công sở và casual', 'HOAT_DONG', '2025-12-16 10:35:07', '2025-12-18 11:10:15'),
(6, 'Phụ kiện', 'phu-kien', 'Phụ kiện thời trang', 'NGUNG_HOAT_DONG', '2025-12-15 15:25:08', '2025-12-22 13:45:12');

--
-- Triggers `danh_muc_san_pham`
--
DROP TRIGGER IF EXISTS `trg_danh_muc_ngung_hoat_dong`;
DELIMITER $$
CREATE TRIGGER `trg_danh_muc_ngung_hoat_dong` AFTER UPDATE ON `danh_muc_san_pham` FOR EACH ROW BEGIN
    -- Chỉ xử lý khi trạng thái đổi sang NGUNG_HOAT_DONG
    IF OLD.trang_thai <> 'NGUNG_HOAT_DONG'
       AND NEW.trang_thai = 'NGUNG_HOAT_DONG' THEN

        UPDATE san_pham
        SET trang_thai = 'NGUNG_BAN'
        WHERE danh_muc_id = NEW.id;

    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `dia_chi`
--

DROP TABLE IF EXISTS `dia_chi`;
CREATE TABLE `dia_chi` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nguoi_dung_id` bigint(20) UNSIGNED NOT NULL,
  `ten_nguoi_nhan` varchar(100) NOT NULL,
  `so_dien_thoai` varchar(20) NOT NULL,
  `tinh_thanh` varchar(100) NOT NULL,
  `quan_huyen` varchar(100) NOT NULL,
  `phuong_xa` varchar(100) NOT NULL,
  `dia_chi_cu_the` text NOT NULL,
  `mac_dinh` tinyint(1) DEFAULT 0,
  `ngay_tao` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dia_chi`
--

INSERT INTO `dia_chi` (`id`, `nguoi_dung_id`, `ten_nguoi_nhan`, `so_dien_thoai`, `tinh_thanh`, `quan_huyen`, `phuong_xa`, `dia_chi_cu_the`, `mac_dinh`, `ngay_tao`) VALUES
(1, 3, 'Bùi Lê Minh Phát', '0909191189', 'Thành phố Hà Nội', 'Quận Hoàn Kiếm', 'Phường Hàng Mã', '241 Trần Hưng Đạo', 1, '2025-12-21 11:52:20');

-- --------------------------------------------------------

--
-- Table structure for table `don_hang`
--

DROP TABLE IF EXISTS `don_hang`;
CREATE TABLE `don_hang` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ma_don_hang` varchar(50) NOT NULL,
  `nguoi_dung_id` bigint(20) UNSIGNED NOT NULL,
  `ma_khuyen_mai` varchar(50) DEFAULT NULL COMMENT 'Mã voucher khách đã dùng',
  `trang_thai` enum('CHO_XU_LY','DANG_XU_LY','HOAN_TAT','HUY','YEU_CAU_HUY') NOT NULL DEFAULT 'CHO_XU_LY',
  `phuong_thuc_thanh_toan` enum('COD') NOT NULL DEFAULT 'COD',
  `trang_thai_thanh_toan` enum('CHUA_THANH_TOAN','DA_THANH_TOAN') NOT NULL DEFAULT 'CHUA_THANH_TOAN',
  `tam_tinh` decimal(12,2) NOT NULL,
  `phi_van_chuyen` decimal(12,2) NOT NULL DEFAULT 0.00,
  `giam_gia` decimal(12,2) NOT NULL DEFAULT 0.00,
  `tong_tien` decimal(12,2) NOT NULL,
  `nguoi_nhan` varchar(255) NOT NULL,
  `sdt_nguoi_nhan` varchar(50) NOT NULL,
  `dia_chi_giao_hang` varchar(500) NOT NULL,
  `ghi_chu` varchar(500) DEFAULT NULL,
  `ly_do_huy` text DEFAULT NULL,
  `tao_luc` datetime NOT NULL DEFAULT current_timestamp(),
  `cap_nhat_luc` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `don_hang`
--

INSERT INTO `don_hang` (`id`, `ma_don_hang`, `nguoi_dung_id`, `ma_khuyen_mai`, `trang_thai`, `phuong_thuc_thanh_toan`, `trang_thai_thanh_toan`, `tam_tinh`, `phi_van_chuyen`, `giam_gia`, `tong_tien`, `nguoi_nhan`, `sdt_nguoi_nhan`, `dia_chi_giao_hang`, `ghi_chu`, `ly_do_huy`, `tao_luc`, `cap_nhat_luc`) VALUES
(1, 'DH20251220-129EBB', 3, NULL, 'CHO_XU_LY', 'COD', 'CHUA_THANH_TOAN', 399000.00, 30000.00, 0.00, 429000.00, 'Bùi Lê Minh Phát', '0909119189', '1231231 abc, Phường Sông Bằng, Thành phố Cao Bằng, Tỉnh Cao Bằng', NULL, NULL, '2025-12-20 09:08:46', '2025-12-20 09:08:46'),
(2, 'DH20251220-A5A6D6', 3, NULL, 'HOAN_TAT', 'COD', 'DA_THANH_TOAN', 1596000.00, 30000.00, 0.00, 1626000.00, 'BUI LE MINH PHAT', '0909119189', '241 Trần Hưng Đạo, p.Phước Nguyên, tp Bà Rịa, tỉnh Bà Rịa - Vũng Tàu, Xã Lũng Cú, Huyện Đồng Văn, Tỉnh Hà Giang', '123 abc', NULL, '2025-12-19 09:13:30', '2025-12-23 09:30:23'),
(3, 'DH20251220-FB4A71', 3, NULL, 'CHO_XU_LY', 'COD', 'CHUA_THANH_TOAN', 2544000.00, 30000.00, 0.00, 2574000.00, 'BUI LE MINH PHAT', '09090909', '241 Trần Hưng Đạo, p.Phước Nguyên, tp Bà Rịa, tỉnh Bà Rịa - Vũng Tàu, Xã Bảo Toàn, Huyện Bảo Lạc, Tỉnh Cao Bằng', NULL, NULL, '2025-12-20 22:12:30', '2025-12-20 22:12:30'),
(4, 'DH20251221-6EF38A', 3, NULL, 'CHO_XU_LY', 'COD', 'CHUA_THANH_TOAN', 1347000.00, 30000.00, 0.00, 1377000.00, 'BUI LE MINH PHAT', '0909909090', '241 Trần Hưng Đạo, p.Phước Nguyên, tp Bà Rịa, tỉnh Bà Rịa - Vũng Tàu, Xã Cô Ba, Huyện Bảo Lạc, Tỉnh Cao Bằng', NULL, NULL, '2025-12-21 11:04:34', '2025-12-21 11:04:34'),
(5, 'DH20251221-EAC83A', 3, NULL, 'HOAN_TAT', 'COD', 'DA_THANH_TOAN', 459000.00, 30000.00, 10000.00, 479000.00, 'Bùi Lê Minh Phát', '0909191189', '241 Trần Hưng Đạo, Phường Hàng Mã, Quận Hoàn Kiếm, Thành phố Hà Nội', NULL, NULL, '2025-12-21 11:53:30', '2025-12-21 21:36:45'),
(11, 'DH20251222-05A0B9', 3, NULL, 'HUY', 'COD', 'CHUA_THANH_TOAN', 1377000.00, 30000.00, 0.00, 1407000.00, 'Bùi Lê Minh Phát', '0909191189', '241 Trần Hưng Đạo, Phường Hàng Mã, Quận Hoàn Kiếm, Thành phố Hà Nội', NULL, 'Áo đẹp, nhưng tên brand xấu quắc', '2025-12-22 06:30:54', '2025-12-22 10:07:20'),
(12, 'DH20251222-D2450F', 3, NULL, 'CHO_XU_LY', 'COD', 'CHUA_THANH_TOAN', 598000.00, 30000.00, 0.00, 628000.00, 'Bùi Lê Minh Phát', '0909191189', '241 Trần Hưng Đạo, Phường Hàng Mã, Quận Hoàn Kiếm, Thành phố Hà Nội', NULL, NULL, '2025-12-22 08:58:53', '2025-12-22 08:58:53'),
(13, 'DH20251222-D0AFF1', 3, NULL, 'CHO_XU_LY', 'COD', 'CHUA_THANH_TOAN', 2235000.00, 30000.00, 20000.00, 2245000.00, 'Phát Bùi', '0909119189', '124 Đạo Trần Hưng, Xã Pha Long, Huyện Mường Khương, Tỉnh Lào Cai', NULL, NULL, '2025-12-22 10:14:53', '2025-12-22 10:14:53'),
(14, 'DH20251222-EC7516', 3, NULL, 'CHO_XU_LY', 'COD', 'CHUA_THANH_TOAN', 1596000.00, 30000.00, 0.00, 1626000.00, 'Bùi Lê Minh Phát', '0909191189', '241 Trần Hưng Đạo, Phường Hàng Mã, Quận Hoàn Kiếm, Thành phố Hà Nội', NULL, NULL, '2025-12-22 10:57:53', '2025-12-22 10:57:53'),
(15, 'DH20251222-1F2B47', 3, NULL, 'YEU_CAU_HUY', 'COD', 'CHUA_THANH_TOAN', 399000.00, 30000.00, 0.00, 429000.00, 'Bùi Lê Minh Phát', '0909191189', '241 Trần Hưng Đạo, Phường Hàng Mã, Quận Hoàn Kiếm, Thành phố Hà Nội', NULL, 'Test hehe', '2025-12-22 10:57:53', '2025-12-22 10:58:23'),
(16, 'DH20251222-B03F07', 3, NULL, 'CHO_XU_LY', 'COD', 'CHUA_THANH_TOAN', 2796000.00, 30000.00, 0.00, 2826000.00, 'Bùi Lê Minh Phát', '0909191189', '241 Trần Hưng Đạo, Phường Hàng Mã, Quận Hoàn Kiếm, Thành phố Hà Nội', NULL, NULL, '2025-12-22 11:18:46', '2025-12-22 11:18:46');

-- --------------------------------------------------------

--
-- Table structure for table `gio_hang`
--

DROP TABLE IF EXISTS `gio_hang`;
CREATE TABLE `gio_hang` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nguoi_dung_id` bigint(20) UNSIGNED NOT NULL,
  `tao_luc` datetime NOT NULL DEFAULT current_timestamp(),
  `cap_nhat_luc` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gio_hang`
--

INSERT INTO `gio_hang` (`id`, `nguoi_dung_id`, `tao_luc`, `cap_nhat_luc`) VALUES
(2, 5, '2025-12-18 17:25:04', '2025-12-18 17:25:04'),
(3, 3, '2025-12-20 09:08:17', '2025-12-20 09:08:17');

-- --------------------------------------------------------

--
-- Table structure for table `khuyen_mai`
--

DROP TABLE IF EXISTS `khuyen_mai`;
CREATE TABLE `khuyen_mai` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ma_khuyen_mai` varchar(50) NOT NULL COMMENT 'Mã code (duy nhất)',
  `ten_chuong_trinh` varchar(255) NOT NULL COMMENT 'Tên hiển thị',
  `loai_hinh` enum('san-pham','voucher') NOT NULL DEFAULT 'voucher',
  `loai_giam_gia` enum('percent','fixed') NOT NULL DEFAULT 'percent' COMMENT 'percent: Phần trăm, fixed: Tiền mặt',
  `gia_tri_giam` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Số % hoặc Số tiền',
  `giam_toi_da` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Mức giảm tối đa (cho loại %)',
  `don_toi_thieu` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Đơn hàng tối thiểu để áp dụng',
  `ngay_bat_dau` date NOT NULL,
  `gio_bat_dau` time NOT NULL,
  `ngay_ket_thuc` date NOT NULL,
  `gio_ket_thuc` time NOT NULL,
  `trang_thai` enum('active','disabled') NOT NULL DEFAULT 'active',
  `tao_luc` datetime NOT NULL DEFAULT current_timestamp(),
  `cap_nhat_luc` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `khuyen_mai`
--

INSERT INTO `khuyen_mai` (`id`, `ma_khuyen_mai`, `ten_chuong_trinh`, `loai_hinh`, `loai_giam_gia`, `gia_tri_giam`, `giam_toi_da`, `don_toi_thieu`, `ngay_bat_dau`, `gio_bat_dau`, `ngay_ket_thuc`, `gio_ket_thuc`, `trang_thai`, `tao_luc`, `cap_nhat_luc`) VALUES
(1, 'SALE10', 'Siêu sale tháng 5', 'voucher', 'percent', 10.00, 10000.00, 200000.00, '2025-12-20', '00:00:00', '2025-12-27', '23:59:59', 'active', '2025-12-20 22:42:01', '2025-12-20 22:42:01'),
(2, 'FREESHIP20', 'Hỗ trợ phí ship', 'voucher', 'fixed', 20000.00, 0.00, 99000.00, '2025-12-20', '00:00:00', '2026-01-19', '23:59:59', 'active', '2025-12-20 22:42:01', '2025-12-20 22:42:01'),
(3, 'CHRISTMAS79', 'Giáng Sinh An Lành', 'voucher', 'fixed', 79000.00, 0.00, 150000.00, '2025-10-22', '10:00:00', '2025-12-25', '23:59:00', 'active', '2025-12-22 13:40:33', '2025-12-22 13:40:33');

-- --------------------------------------------------------

--
-- Table structure for table `kich_co`
--

DROP TABLE IF EXISTS `kich_co`;
CREATE TABLE `kich_co` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ten_kich_co` varchar(50) NOT NULL,
  `duong_dan` varchar(255) NOT NULL,
  `thu_tu` int(11) NOT NULL DEFAULT 0,
  `trang_thai` enum('HOAT_DONG','NGUNG') NOT NULL DEFAULT 'HOAT_DONG'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kich_co`
--

INSERT INTO `kich_co` (`id`, `ten_kich_co`, `duong_dan`, `thu_tu`, `trang_thai`) VALUES
(1, 'S', 's', 1, 'HOAT_DONG'),
(2, 'M', 'm', 2, 'HOAT_DONG'),
(3, 'L', 'l', 3, 'HOAT_DONG'),
(4, 'XL', 'xl', 4, 'HOAT_DONG');

-- --------------------------------------------------------

--
-- Table structure for table `lich_su_trang_thai_don_hang`
--

DROP TABLE IF EXISTS `lich_su_trang_thai_don_hang`;
CREATE TABLE `lich_su_trang_thai_don_hang` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `don_hang_id` bigint(20) UNSIGNED NOT NULL,
  `tu_trang_thai` enum('CHO_XU_LY','DANG_XU_LY','HOAN_TAT','HUY','YEU_CAU_HUY') DEFAULT NULL,
  `den_trang_thai` enum('CHO_XU_LY','DANG_XU_LY','HOAN_TAT','HUY','YEU_CAU_HUY') NOT NULL,
  `nguoi_thay_doi_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ghi_chu` varchar(500) DEFAULT NULL,
  `tao_luc` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lich_su_trang_thai_don_hang`
--

INSERT INTO `lich_su_trang_thai_don_hang` (`id`, `don_hang_id`, `tu_trang_thai`, `den_trang_thai`, `nguoi_thay_doi_id`, `ghi_chu`, `tao_luc`) VALUES
(1, 5, 'CHO_XU_LY', 'HOAN_TAT', 4, NULL, '2025-12-21 21:36:45'),
(2, 11, 'CHO_XU_LY', 'YEU_CAU_HUY', 3, 'Khách yêu cầu hủy: Áo đẹp, nhưng tên brand xấu quắc', '2025-12-22 06:31:30'),
(3, 11, 'YEU_CAU_HUY', 'HUY', 4, 'Admin chấp thuận yêu cầu hủy', '2025-12-22 10:07:20'),
(4, 15, 'CHO_XU_LY', 'YEU_CAU_HUY', 3, 'Khách yêu cầu hủy: Test hehe', '2025-12-22 10:58:23'),
(5, 2, 'CHO_XU_LY', 'HOAN_TAT', 4, NULL, '2025-12-23 09:30:23');

-- --------------------------------------------------------

--
-- Table structure for table `mau_sac`
--

DROP TABLE IF EXISTS `mau_sac`;
CREATE TABLE `mau_sac` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ten_mau` varchar(100) NOT NULL,
  `ma_mau` varchar(20) DEFAULT NULL,
  `duong_dan` varchar(255) NOT NULL,
  `trang_thai` enum('HOAT_DONG','NGUNG') NOT NULL DEFAULT 'HOAT_DONG'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mau_sac`
--

INSERT INTO `mau_sac` (`id`, `ten_mau`, `ma_mau`, `duong_dan`, `trang_thai`) VALUES
(1, 'Đen', '#000000', 'den', 'HOAT_DONG'),
(2, 'Trắng', '#ffffff', 'trang', 'HOAT_DONG'),
(3, 'Xám', '#d3d3d3', 'xam', 'HOAT_DONG'),
(4, 'Be', '#f5e6d3', 'be', 'HOAT_DONG'),
(5, 'Xanh pastel', '#6b8ca9', 'xanh-pastel', 'HOAT_DONG'),
(6, 'Hồng pastel', '#f5c6cb', 'hong-pastel', 'HOAT_DONG');

-- --------------------------------------------------------

--
-- Table structure for table `nguoi_dung`
--

DROP TABLE IF EXISTS `nguoi_dung`;
CREATE TABLE `nguoi_dung` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(255) NOT NULL,
  `mat_khau_bam` varchar(255) NOT NULL,
  `ho_ten` varchar(255) DEFAULT NULL,
  `ngay_sinh` date DEFAULT NULL,
  `so_dien_thoai` varchar(50) DEFAULT NULL,
  `vai_tro` enum('NGUOI_DUNG','QUAN_TRI') NOT NULL DEFAULT 'NGUOI_DUNG',
  `trang_thai` enum('HOAT_DONG','KHOA','NGUNG_HOAT_DONG') NOT NULL DEFAULT 'HOAT_DONG',
  `lan_dang_nhap_gan_nhat` datetime DEFAULT NULL,
  `tao_luc` datetime NOT NULL DEFAULT current_timestamp(),
  `cap_nhat_luc` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `nguoi_dung`
--

INSERT INTO `nguoi_dung` (`id`, `email`, `mat_khau_bam`, `ho_ten`, `ngay_sinh`, `so_dien_thoai`, `vai_tro`, `trang_thai`, `lan_dang_nhap_gan_nhat`, `tao_luc`, `cap_nhat_luc`) VALUES
(2, 'pbui01@gmail.com', '$2y$10$Afa70cZ9MwsV2SidzsaLE.P0SDO3XZ0jojl8kclLlhRCcxrWDLTRy', 'Bùi Phát', NULL, '0909119189', 'NGUOI_DUNG', 'HOAT_DONG', NULL, '2025-12-15 16:38:01', '2025-12-15 16:38:01'),
(3, 'pbui02@gmail.com', '$2y$10$j.ZLIZPoTKMhKhOVyog7g./CUYwoJPJG5hFU8cVYladvOz0Y6ZM1S', 'Bùi Phát', '2005-10-12', '0909119189', 'NGUOI_DUNG', 'HOAT_DONG', '2025-12-23 20:45:57', '2025-12-16 08:56:05', '2025-12-23 20:45:57'),
(4, 'admin@gmail.com', '$2y$10$FAtxBGu4hVxCOd2ugYCXV.bJEhJj8zpxvm/6caH73zN6wlC/I2qBe', 'Nhi', NULL, '', 'QUAN_TRI', 'HOAT_DONG', '2025-12-23 22:53:49', '2025-12-17 23:58:04', '2025-12-23 22:53:49'),
(5, 'nhi@gmail.com', '$2y$10$K2mhnsvMzfs5nqAjvEX0m.D5JTeSkH2qUCmrGsFdPx4hRqLQ6u5Sm', '', '2000-01-01', '', 'NGUOI_DUNG', 'HOAT_DONG', '2025-12-19 11:18:06', '2025-12-18 17:06:05', '2025-12-19 11:18:06'),
(8, 'phatbui.31231023065@st.ueh.edu.vn', '$2y$10$jdpAVQNw.J6ZxuaRLal4qendhi1Qn7C6Za9L7Ou32LrqhPx191ij2', 'Bùi Phát', '2005-10-12', '0909119189', 'NGUOI_DUNG', 'HOAT_DONG', NULL, '2025-12-23 20:42:37', '2025-12-23 20:42:37');

-- --------------------------------------------------------

--
-- Table structure for table `phien_dang_nhap`
--

DROP TABLE IF EXISTS `phien_dang_nhap`;
CREATE TABLE `phien_dang_nhap` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nguoi_dung_id` bigint(20) UNSIGNED NOT NULL,
  `token` varchar(255) NOT NULL,
  `het_han_luc` datetime NOT NULL,
  `tao_luc` datetime NOT NULL DEFAULT current_timestamp(),
  `thu_hoi_luc` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `phien_dang_nhap`
--

INSERT INTO `phien_dang_nhap` (`id`, `nguoi_dung_id`, `token`, `het_han_luc`, `tao_luc`, `thu_hoi_luc`) VALUES
(1, 5, 'fc65e0397e77db9331515de13a490b92ddcd5e6418e3bb4f19b3bdc3ef9b413d', '2025-12-25 17:24:38', '2025-12-18 17:24:38', NULL),
(2, 5, 'ed02cf512db273b26beb113cafc0f2e7e0426c5545597f89e0ec372862a57e76', '2025-12-25 17:24:38', '2025-12-18 17:24:38', NULL),
(3, 5, 'dbbfa39075bfa0a2a2b856c6df388af8ad2fdf8204a18237b1a7a698d4a57464', '2025-12-25 17:58:12', '2025-12-18 17:58:12', NULL),
(4, 5, '540af7cbe191bb77c91075fe72531d7761598fbac394618ea4a8c45e6604818b', '2025-12-25 17:58:12', '2025-12-18 17:58:12', NULL),
(5, 5, 'c194f402642662fc24c8c7fe990368d02fd0aa7f82c650ded2c824d2d532790f', '2025-12-25 17:58:19', '2025-12-18 17:58:19', NULL),
(6, 5, '706e0b878c4423a036d22ffc7dcd17781ebabbc9aff86a17b017026db8723835', '2025-12-25 17:58:19', '2025-12-18 17:58:19', NULL),
(7, 5, 'ad573ea67a1fc1a4999c6c335aa5c17892774ddea813144350696f2fadb8a118', '2025-12-25 18:01:23', '2025-12-18 18:01:23', '2025-12-18 18:06:14'),
(8, 5, 'ed7c60509f1393225e53859555a4ac7b0a007740a5af08f98f273fd90fedd7e1', '2025-12-25 18:01:23', '2025-12-18 18:01:23', NULL),
(9, 5, '6952357d5d3c9d41d1948c148298113ddb1eaf0cac05ead5ca056e137a79816d', '2025-12-25 18:06:20', '2025-12-18 18:06:20', '2025-12-18 18:08:15'),
(10, 5, '1b5b8d9e55b3d81e547284d9b2f1098b618daefb90c4610836f49c7758d2cc95', '2025-12-25 18:06:20', '2025-12-18 18:06:20', NULL),
(11, 5, '2b6fdef4dcb4006b6b68edc708803d47e08e487262d4674ffbb91ca8a2df9ea2', '2025-12-25 18:08:18', '2025-12-18 18:08:18', NULL),
(12, 5, '7fd418dbf094ab9209b8210377c61687c8a676dbd395d7224979e54271d34a7c', '2025-12-25 18:08:18', '2025-12-18 18:08:18', NULL),
(13, 5, '08699e2e0250e6fd71ef915198a701293e2aaa41bc9d62f328e11931be62f8a2', '2025-12-25 22:14:07', '2025-12-18 22:14:07', '2025-12-18 22:39:28'),
(14, 5, '595e5ad2e03a9d27f7bf390fbb6637e53e34e421b4d1b01012d53cede4836fb6', '2025-12-25 22:14:07', '2025-12-18 22:14:07', NULL),
(15, 5, '3637251ec5870af5186d48c8cecb0e97e9600a51f0770b103c2c34089ceed905', '2025-12-26 09:57:34', '2025-12-19 09:57:34', NULL),
(16, 5, '60ef15a4f86f234724d81f1e9fc2e8dc2010666ff8b7e25eb3f66897dc5f16b2', '2025-12-26 09:57:34', '2025-12-19 09:57:34', NULL),
(17, 5, 'c2c8f43265ca6429d7c3316f3d04ccc457cfd6fdb9ed6983ef48c79e4e27b0f7', '2025-12-26 11:18:06', '2025-12-19 11:18:06', NULL),
(18, 5, '9242ef8c9ec47b08ba37ddece4d5baeef0988e5a68ba1303a5682c5ba6896c37', '2025-12-26 11:18:06', '2025-12-19 11:18:06', NULL),
(19, 3, '8909ede4ea2f858f4bd101d69e6d73bd26e07ffaa6a501970405c423ed282357', '2025-12-27 09:07:58', '2025-12-20 09:07:58', '2025-12-21 11:55:22'),
(20, 3, 'bd1c045132ed3a324c56e560752f57a8df57b1ef55ec076498bbd5ed982a35ee', '2025-12-27 09:07:58', '2025-12-20 09:07:58', NULL),
(21, 3, 'b42359b07f6fd3ab65619c1763d5aa44f79a6068ffd5bc39942030a13ae79030', '2025-12-28 11:55:32', '2025-12-21 11:55:32', NULL),
(22, 3, '205854f4e2512ca7578022d1b9168161c5f191a6405f4f8b033594dd59ce4744', '2025-12-28 11:55:37', '2025-12-21 11:55:37', NULL),
(23, 3, '6c08a8490eefbb89917255fdd49ecaf8d83fe23537a5b3fdaa7e591cde9f3ff7', '2025-12-29 09:56:27', '2025-12-22 09:56:27', NULL),
(24, 3, '8a8cb2d46f211de30a975eadb6d3348a874b3e35292d1b6646c81a5432310d1e', '2025-12-30 08:37:53', '2025-12-23 08:37:53', '2025-12-23 12:44:12'),
(26, 3, 'd5429e858bdf5a7ad064d7a0f515dd6a433609445fee98d79a7fd20fb8948109', '2025-12-30 12:59:40', '2025-12-23 12:59:40', NULL),
(27, 3, '428997729c08a2ef1323f1714666d55ef05be53dc1b83586b1efe6ac22ba14b1', '2025-12-30 20:45:57', '2025-12-23 20:45:57', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `san_pham`
--

DROP TABLE IF EXISTS `san_pham`;
CREATE TABLE `san_pham` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `danh_muc_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ten_san_pham` varchar(255) NOT NULL,
  `duong_dan` varchar(255) NOT NULL,
  `mo_ta` text DEFAULT NULL,
  `gia_ban` decimal(12,2) NOT NULL,
  `so_luong_ton` int(11) NOT NULL DEFAULT 0,
  `trang_thai` enum('DANG_BAN','NGUNG_BAN','DA_GO') NOT NULL DEFAULT 'DANG_BAN',
  `anh_dai_dien_url` varchar(500) DEFAULT NULL,
  `tao_luc` datetime NOT NULL DEFAULT current_timestamp(),
  `cap_nhat_luc` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `san_pham`
--

INSERT INTO `san_pham` (`id`, `danh_muc_id`, `ten_san_pham`, `duong_dan`, `mo_ta`, `gia_ban`, `so_luong_ton`, `trang_thai`, `anh_dai_dien_url`, `tao_luc`, `cap_nhat_luc`) VALUES
(19, 1, 'Áo thun Basic Cotton', 'ao-thun-basic-cotton', 'Áo thun cotton mềm, form basic dễ phối.', 199000.00, 99, 'DANG_BAN', '1004', '2025-12-16 10:13:20', '2025-12-22 14:28:47'),
(20, 1, 'Áo thun Oversize Street', 'ao-thun-oversize-street', 'Áo thun oversize phong cách streetwear.', 259000.00, 80, 'DANG_BAN', '1009', '2025-12-16 10:13:20', '2025-12-21 07:29:37'),
(21, 1, 'Áo thun Polo Minimal', 'ao-thun-polo-minimal', 'Polo tối giản, lịch sự, dễ mặc.', 299000.00, 60, 'DANG_BAN', '1013', '2025-12-16 10:13:20', '2025-12-21 07:29:37'),
(22, 5, 'Sơ mi Oxford', 'so-mi-oxford', 'Sơ mi Oxford, thoáng, đứng form.', 349000.00, 20, 'DANG_BAN', '1016', '2025-12-16 10:13:20', '2025-12-23 09:03:55'),
(23, 5, 'Sơ mi Kẻ Caro', 'so-mi-ke-caro', 'Sơ mi caro casual, dễ phối quần jeans.', 319000.00, 20, 'DANG_BAN', '1018', '2025-12-16 10:13:20', '2025-12-22 08:57:09'),
(24, 3, 'Quần Jeans Slim Fit', 'quan-jeans-slim-fit', 'Jeans slim fit co giãn nhẹ, tôn dáng.', 499000.00, 120, 'DANG_BAN', '1019', '2025-12-16 10:13:20', '2025-12-23 08:55:54'),
(25, 3, 'Quần Kaki Regular', 'quan-kaki-regular', 'Kaki regular, lịch sự cho đi làm.', 429000.00, 120, 'DANG_BAN', '1020', '2025-12-16 10:13:20', '2025-12-23 08:55:54'),
(26, 3, 'Quần Short Linen', 'quan-short-linen', 'Short linen mát, hợp đi chơi/du lịch.', 249000.00, 39, 'DANG_BAN', '1021', '2025-12-16 10:13:20', '2025-12-22 08:57:09'),
(27, 4, 'Áo khoác Bomber', 'ao-khoac-bomber', 'Bomber basic, giữ ấm vừa phải.', 699000.00, 64, 'DANG_BAN', '1023', '2025-12-16 10:13:20', '2025-12-22 14:28:47'),
(28, 4, 'Áo khoác Denim Jacket', 'ao-khoac-denim-jacket', 'Denim jacket cá tính, phối nhiều style.', 749000.00, 120, 'DANG_BAN', '1025', '2025-12-16 10:13:20', '2025-12-23 08:55:54'),
(29, 6, 'Nón lưỡi trai Classic', 'non-luoi-trai-classic', 'Nón classic, form chuẩn, dễ phối.', 159000.00, 60, 'NGUNG_BAN', '1026', '2025-12-16 10:13:20', '2025-12-22 14:28:47'),
(30, 6, 'Túi Tote Canvas', 'tui-tote-canvas', 'Tote canvas dày dặn, đựng laptop 13-14\".', 189000.00, 39, 'NGUNG_BAN', '1029', '2025-12-16 10:13:20', '2025-12-22 13:45:12'),
(31, 2, 'Hoodie Oversize Basic', 'hoodie-oversize-basic', 'Hoodie nỉ ấm, form rộng dễ phối.', 399000.00, 109, 'DANG_BAN', '1031', '2025-12-16 10:36:11', '2025-12-22 14:28:47'),
(32, 2, 'Hoodie Zip Street', 'hoodie-zip-street', 'Hoodie khoá kéo phong cách streetwear.', 459000.00, 14, 'DANG_BAN', '1036', '2025-12-16 10:36:11', '2025-12-22 10:14:53');

-- --------------------------------------------------------

--
-- Table structure for table `sku_san_pham`
--

DROP TABLE IF EXISTS `sku_san_pham`;
CREATE TABLE `sku_san_pham` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `san_pham_id` bigint(20) UNSIGNED NOT NULL,
  `ma_sku` varchar(80) NOT NULL,
  `kich_co_id` bigint(20) UNSIGNED DEFAULT NULL,
  `mau_sac_id` bigint(20) UNSIGNED DEFAULT NULL,
  `anh_url` varchar(255) DEFAULT NULL,
  `gia_ban` decimal(12,2) NOT NULL,
  `so_luong_ton` int(11) NOT NULL DEFAULT 0,
  `trang_thai` enum('DANG_BAN','NGUNG_BAN','DA_GO') NOT NULL DEFAULT 'DANG_BAN',
  `tao_luc` datetime NOT NULL DEFAULT current_timestamp(),
  `cap_nhat_luc` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sku_san_pham`
--

INSERT INTO `sku_san_pham` (`id`, `san_pham_id`, `ma_sku`, `kich_co_id`, `mau_sac_id`, `anh_url`, `gia_ban`, `so_luong_ton`, `trang_thai`, `tao_luc`, `cap_nhat_luc`) VALUES
(2019, 19, 'SP19-S-DEN', 1, 1, 'PTUD_Final/images/ao-thun/ao-thun-basic-cotton/den.png', 199000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2020, 19, 'SP19-M-DEN', 2, 1, 'PTUD_Final/images/ao-thun/ao-thun-basic-cotton/den.png', 199000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2021, 19, 'SP19-L-DEN', 3, 1, 'PTUD_Final/images/ao-thun/ao-thun-basic-cotton/den.png', 199000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2022, 19, 'SP19-M-TRANG', 2, 2, 'PTUD_Final/images/ao-thun/ao-thun-basic-cotton/trang.png', 199000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2023, 31, 'SP31-S1-C1', 1, 1, 'PTUD_Final/images/hoodie/hoodie-oversize-basic/den.png', 399000.00, 4, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-22 14:08:34'),
(2024, 31, 'SP31-S1-C2', 1, 2, 'PTUD_Final/images/hoodie/hoodie-oversize-basic/trang.png', 399000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-22 14:08:34'),
(2025, 31, 'SP31-S2-C1', 2, 1, 'PTUD_Final/images/hoodie/hoodie-oversize-basic/den.png', 399000.00, 6, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-22 14:08:34'),
(2026, 31, 'SP31-S2-C2', 2, 2, 'PTUD_Final/images/hoodie/hoodie-oversize-basic/trang.png', 399000.00, 2, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-22 14:08:34'),
(2027, 31, 'SP31-S2-C4', 2, 4, 'PTUD_Final/images/hoodie/hoodie-oversize-basic/be.png', 419000.00, 7, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-22 14:09:27'),
(2028, 31, 'SP31-S3-C1', 3, 1, 'PTUD_Final/images/hoodie/hoodie-oversize-basic/den.png', 399000.00, 4, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-22 14:09:27'),
(2029, 31, 'SP31-S3-C4', 3, 4, 'PTUD_Final/images/hoodie/hoodie-oversize-basic/be.png', 419000.00, 3, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-22 14:09:27'),
(2030, 27, 'SP27-S-HONG', 1, 6, 'PTUD_Final/images/ao-khoac/ao-khoac-bomber/hong.png', 699000.00, 20, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2031, 27, 'SP27-M-HONG', 2, 6, 'PTUD_Final/images/ao-khoac/ao-khoac-bomber/hong.png', 699000.00, 10, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2032, 27, 'SP27-L-HONG', 3, 6, 'PTUD_Final/images/ao-khoac/ao-khoac-bomber/hong.png', 699000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2033, 27, 'SP27-XL-HONG', 4, 6, 'PTUD_Final/images/ao-khoac/ao-khoac-bomber/hong.png', 699000.00, 2, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2034, 27, 'SP27-S-XANH', 1, 5, 'PTUD_Final/images/ao-khoac/ao-khoac-bomber/xanh.png', 699000.00, 9, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-21 11:04:34'),
(2035, 27, 'SP27-M-XANH', 2, 5, 'PTUD_Final/images/ao-khoac/ao-khoac-bomber/xanh.png', 699000.00, 10, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-21 09:13:18'),
(2036, 27, 'SP27-L-XANH', 3, 5, 'PTUD_Final/images/ao-khoac/ao-khoac-bomber/xanh.png', 699000.00, 6, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-22 11:18:46'),
(2037, 19, 'SP19-S1-C4', 1, 4, 'PTUD_Final/images/ao-thun/ao-thun-basic-cotton/be.png', 199000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2038, 19, 'SP19-S1-C2', 1, 2, 'PTUD_Final/images/ao-thun/ao-thun-basic-cotton/trang.png', 199000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2039, 19, 'SP19-S1-C3', 1, 3, 'PTUD_Final/images/ao-thun/ao-thun-basic-cotton/xam.png', 199000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2041, 19, 'SP19-S2-C4', 2, 4, 'PTUD_Final/images/ao-thun/ao-thun-basic-cotton/be.png', 199000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2042, 19, 'SP19-S2-C3', 2, 3, 'PTUD_Final/images/ao-thun/ao-thun-basic-cotton/xam.png', 199000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2044, 19, 'SP19-S3-C4', 3, 4, 'PTUD_Final/images/ao-thun/ao-thun-basic-cotton/be.png', 199000.00, 4, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-22 08:58:53'),
(2045, 19, 'SP19-S3-C2', 3, 2, 'PTUD_Final/images/ao-thun/ao-thun-basic-cotton/trang.png', 199000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2046, 19, 'SP19-S3-C3', 3, 3, 'PTUD_Final/images/ao-thun/ao-thun-basic-cotton/xam.png', 199000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2048, 19, 'SP19-S4-C4', 4, 4, 'PTUD_Final/images/ao-thun/ao-thun-basic-cotton/be.png', 199000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2049, 19, 'SP19-S4-C1', 4, 1, 'PTUD_Final/images/ao-thun/ao-thun-basic-cotton/den.png', 199000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2050, 19, 'SP19-S4-C2', 4, 2, 'PTUD_Final/images/ao-thun/ao-thun-basic-cotton/trang.png', 199000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2051, 19, 'SP19-S4-C3', 4, 3, 'PTUD_Final/images/ao-thun/ao-thun-basic-cotton/xam.png', 199000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2053, 20, 'SP20-S1-C4', 1, 4, 'PTUD_Final/images/ao-thun/ao-thun-oversize-street/be.png', 259000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2054, 20, 'SP20-S1-C1', 1, 1, 'PTUD_Final/images/ao-thun/ao-thun-oversize-street/den.png', 259000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2055, 20, 'SP20-S1-C2', 1, 2, 'PTUD_Final/images/ao-thun/ao-thun-oversize-street/trang.png', 259000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2056, 20, 'SP20-S1-C3', 1, 3, 'PTUD_Final/images/ao-thun/ao-thun-oversize-street/xam.png', 259000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2057, 20, 'SP20-S2-C4', 2, 4, 'PTUD_Final/images/ao-thun/ao-thun-oversize-street/be.png', 259000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2058, 20, 'SP20-S2-C1', 2, 1, 'PTUD_Final/images/ao-thun/ao-thun-oversize-street/den.png', 259000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2059, 20, 'SP20-S2-C2', 2, 2, 'PTUD_Final/images/ao-thun/ao-thun-oversize-street/trang.png', 259000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2060, 20, 'SP20-S2-C3', 2, 3, 'PTUD_Final/images/ao-thun/ao-thun-oversize-street/xam.png', 259000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2061, 20, 'SP20-S3-C4', 3, 4, 'PTUD_Final/images/ao-thun/ao-thun-oversize-street/be.png', 259000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2062, 20, 'SP20-S3-C1', 3, 1, 'PTUD_Final/images/ao-thun/ao-thun-oversize-street/den.png', 259000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2063, 20, 'SP20-S3-C2', 3, 2, 'PTUD_Final/images/ao-thun/ao-thun-oversize-street/trang.png', 259000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2064, 20, 'SP20-S3-C3', 3, 3, 'PTUD_Final/images/ao-thun/ao-thun-oversize-street/xam.png', 259000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2065, 20, 'SP20-S4-C4', 4, 4, 'PTUD_Final/images/ao-thun/ao-thun-oversize-street/be.png', 259000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2066, 20, 'SP20-S4-C1', 4, 1, 'PTUD_Final/images/ao-thun/ao-thun-oversize-street/den.png', 259000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2067, 20, 'SP20-S4-C2', 4, 2, 'PTUD_Final/images/ao-thun/ao-thun-oversize-street/trang.png', 259000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2068, 20, 'SP20-S4-C3', 4, 3, 'PTUD_Final/images/ao-thun/ao-thun-oversize-street/xam.png', 259000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2069, 21, 'SP21-S1-C4', 1, 4, 'PTUD_Final/images/ao-thun/ao-thun-polo-minimal/be.png', 299000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2070, 21, 'SP21-S1-C1', 1, 1, 'PTUD_Final/images/ao-thun/ao-thun-polo-minimal/den.png', 299000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2071, 21, 'SP21-S1-C2', 1, 2, 'PTUD_Final/images/ao-thun/ao-thun-polo-minimal/trang.png', 299000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2072, 21, 'SP21-S2-C4', 2, 4, 'PTUD_Final/images/ao-thun/ao-thun-polo-minimal/be.png', 299000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2073, 21, 'SP21-S2-C1', 2, 1, 'PTUD_Final/images/ao-thun/ao-thun-polo-minimal/den.png', 299000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2074, 21, 'SP21-S2-C2', 2, 2, 'PTUD_Final/images/ao-thun/ao-thun-polo-minimal/trang.png', 299000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2075, 21, 'SP21-S3-C4', 3, 4, 'PTUD_Final/images/ao-thun/ao-thun-polo-minimal/be.png', 299000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2076, 21, 'SP21-S3-C1', 3, 1, 'PTUD_Final/images/ao-thun/ao-thun-polo-minimal/den.png', 299000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2077, 21, 'SP21-S3-C2', 3, 2, 'PTUD_Final/images/ao-thun/ao-thun-polo-minimal/trang.png', 299000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2078, 21, 'SP21-S4-C4', 4, 4, 'PTUD_Final/images/ao-thun/ao-thun-polo-minimal/be.png', 299000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2079, 21, 'SP21-S4-C1', 4, 1, 'PTUD_Final/images/ao-thun/ao-thun-polo-minimal/den.png', 299000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2080, 21, 'SP21-S4-C2', 4, 2, 'PTUD_Final/images/ao-thun/ao-thun-polo-minimal/trang.png', 299000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2081, 22, 'SP22-S1-C2', 1, 2, 'PTUD_Final/images/ao-so-mi/so-mi-oxford/trang.png', 349000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2083, 22, 'SP22-S2-C2', 2, 2, 'PTUD_Final/images/ao-so-mi/so-mi-oxford/trang.png', 349000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2085, 22, 'SP22-S3-C2', 3, 2, 'PTUD_Final/images/ao-so-mi/so-mi-oxford/trang.png', 349000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2087, 22, 'SP22-S4-C2', 4, 2, 'PTUD_Final/images/ao-so-mi/so-mi-oxford/trang.png', 349000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2089, 23, 'SP23-S1-C2', 1, 2, 'PTUD_Final/images/ao-so-mi/so-mi-ke-caro/trang.png', 319000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2090, 23, 'SP23-S2-C2', 2, 2, 'PTUD_Final/images/ao-so-mi/so-mi-ke-caro/trang.png', 319000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2091, 23, 'SP23-S3-C2', 3, 2, 'PTUD_Final/images/ao-so-mi/so-mi-ke-caro/trang.png', 319000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2092, 23, 'SP23-S4-C2', 4, 2, 'PTUD_Final/images/ao-so-mi/so-mi-ke-caro/trang.png', 319000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2093, 26, 'SP26-S1-C4', 1, 4, 'PTUD_Final/images/quan/quan-short-linen/be.png', 249000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2094, 26, 'SP26-S1-C2', 1, 2, 'PTUD_Final/images/quan/quan-short-linen/trang.png', 249000.00, 4, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 22:12:30'),
(2095, 26, 'SP26-S2-C4', 2, 4, 'PTUD_Final/images/quan/quan-short-linen/be.png', 249000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2096, 26, 'SP26-S2-C2', 2, 2, 'PTUD_Final/images/quan/quan-short-linen/trang.png', 249000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2097, 26, 'SP26-S3-C4', 3, 4, 'PTUD_Final/images/quan/quan-short-linen/be.png', 249000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2098, 26, 'SP26-S3-C2', 3, 2, 'PTUD_Final/images/quan/quan-short-linen/trang.png', 249000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2099, 26, 'SP26-S4-C4', 4, 4, 'PTUD_Final/images/quan/quan-short-linen/be.png', 249000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2100, 26, 'SP26-S4-C2', 4, 2, 'PTUD_Final/images/quan/quan-short-linen/trang.png', 249000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2110, 29, 'SP29-S1-C2', 1, 2, 'PTUD_Final/images/phu-kien/non-luoi-trai-classic/trang.png', 159000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2113, 29, 'SP29-S2-C2', 2, 2, 'PTUD_Final/images/phu-kien/non-luoi-trai-classic/trang.png', 159000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2116, 29, 'SP29-S3-C2', 3, 2, 'PTUD_Final/images/phu-kien/non-luoi-trai-classic/trang.png', 159000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2119, 29, 'SP29-S4-C2', 4, 2, 'PTUD_Final/images/phu-kien/non-luoi-trai-classic/trang.png', 159000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2121, 30, 'SP30-S1-C2', 1, 2, 'PTUD_Final/images/phu-kien/tui-tote-canvas/trang.png', 189000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2122, 30, 'SP30-S1-C3', 1, 3, 'PTUD_Final/images/phu-kien/tui-tote-canvas/xam.png', 189000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2123, 30, 'SP30-S2-C2', 2, 2, 'PTUD_Final/images/phu-kien/tui-tote-canvas/trang.png', 189000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2124, 30, 'SP30-S2-C3', 2, 3, 'PTUD_Final/images/phu-kien/tui-tote-canvas/xam.png', 189000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2125, 30, 'SP30-S3-C2', 3, 2, 'PTUD_Final/images/phu-kien/tui-tote-canvas/trang.png', 189000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2126, 30, 'SP30-S3-C3', 3, 3, 'PTUD_Final/images/phu-kien/tui-tote-canvas/xam.png', 189000.00, 4, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-21 11:04:34'),
(2127, 30, 'SP30-S4-C2', 4, 2, 'PTUD_Final/images/phu-kien/tui-tote-canvas/trang.png', 189000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2128, 30, 'SP30-S4-C3', 4, 3, 'PTUD_Final/images/phu-kien/tui-tote-canvas/xam.png', 189000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2129, 31, 'SP31-S1-C4', 1, 4, 'PTUD_Final/images/hoodie/hoodie-oversize-basic/be.png', 399000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2131, 31, 'SP31-S1-C3', 1, 3, 'PTUD_Final/images/hoodie/hoodie-oversize-basic/xam.png', 399000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2134, 31, 'SP31-S2-C3', 2, 3, 'PTUD_Final/images/hoodie/hoodie-oversize-basic/xam.png', 399000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2137, 31, 'SP31-S3-C2', 3, 2, 'PTUD_Final/images/hoodie/hoodie-oversize-basic/trang.png', 399000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2138, 31, 'SP31-S3-C3', 3, 3, 'PTUD_Final/images/hoodie/hoodie-oversize-basic/xam.png', 399000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2140, 31, 'SP31-S4-C4', 4, 4, 'PTUD_Final/images/hoodie/hoodie-oversize-basic/be.png', 399000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2142, 31, 'SP31-S4-C2', 4, 2, 'PTUD_Final/images/hoodie/hoodie-oversize-basic/trang.png', 399000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2143, 31, 'SP31-S4-C3', 4, 3, 'PTUD_Final/images/hoodie/hoodie-oversize-basic/xam.png', 399000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2145, 32, 'SP32-S1-C2', 1, 2, 'PTUD_Final/images/hoodie/hoodie-zip-street/trang.png', 459000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-21 11:57:45'),
(2146, 32, 'SP32-S2-C2', 2, 2, 'PTUD_Final/images/hoodie/hoodie-zip-street/trang.png', 459000.00, 0, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-22 10:14:53'),
(2147, 32, 'SP32-S3-C2', 3, 2, 'PTUD_Final/images/hoodie/hoodie-zip-street/trang.png', 459000.00, 5, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-20 21:32:04'),
(2148, 32, 'SP32-S4-C2', 4, 2, 'PTUD_Final/images/hoodie/hoodie-zip-street/trang.png', 459000.00, 4, 'DANG_BAN', '2025-12-20 17:30:48', '2025-12-21 11:53:30'),
(2284, 19, 'SP19-S4-C5', 4, 5, 'PTUD_Final/images/ao-thun/ao-thun-basic-cotton/xanh.png', 199000.00, 5, 'DANG_BAN', '2025-12-22 14:28:47', '2025-12-22 14:28:47'),
(2285, 19, 'SP19-S3-C5', 3, 5, 'PTUD_Final/images/ao-thun/ao-thun-basic-cotton/xanh.png', 199000.00, 5, 'DANG_BAN', '2025-12-22 14:28:47', '2025-12-22 14:28:47'),
(2286, 19, 'SP19-S2-C5', 2, 5, 'PTUD_Final/images/ao-thun/ao-thun-basic-cotton/xanh.png', 199000.00, 5, 'DANG_BAN', '2025-12-22 14:28:47', '2025-12-22 14:28:47'),
(2287, 19, 'SP19-S1-C5', 1, 5, 'PTUD_Final/images/ao-thun/ao-thun-basic-cotton/xanh.png', 199000.00, 5, 'DANG_BAN', '2025-12-22 14:28:47', '2025-12-22 14:28:47'),
(2288, 27, 'SP27-S4-C5', 4, 5, 'PTUD_Final/images/ao-khoac/ao-khoac-bomber/xanh.png', 699000.00, 2, 'DANG_BAN', '2025-12-22 14:28:47', '2025-12-22 14:28:47'),
(2289, 29, 'SP29-S4-C5', 4, 5, 'PTUD_Final/images/phu-kien/non-luoi-trai-classic/xanh.png', 159000.00, 5, 'DANG_BAN', '2025-12-22 14:28:47', '2025-12-22 14:28:47'),
(2290, 29, 'SP29-S3-C5', 3, 5, 'PTUD_Final/images/phu-kien/non-luoi-trai-classic/xanh.png', 159000.00, 5, 'DANG_BAN', '2025-12-22 14:28:47', '2025-12-22 14:28:47'),
(2291, 29, 'SP29-S2-C5', 2, 5, 'PTUD_Final/images/phu-kien/non-luoi-trai-classic/xanh.png', 159000.00, 5, 'DANG_BAN', '2025-12-22 14:28:47', '2025-12-22 14:28:47'),
(2292, 29, 'SP29-S1-C5', 1, 5, 'PTUD_Final/images/phu-kien/non-luoi-trai-classic/xanh.png', 159000.00, 5, 'DANG_BAN', '2025-12-22 14:28:47', '2025-12-22 14:28:47'),
(2293, 29, 'SP29-S4-C6', 4, 6, 'PTUD_Final/images/phu-kien/non-luoi-trai-classic/hong.png', 159000.00, 5, 'DANG_BAN', '2025-12-22 14:28:47', '2025-12-22 14:28:47'),
(2294, 29, 'SP29-S3-C6', 3, 6, 'PTUD_Final/images/phu-kien/non-luoi-trai-classic/hong.png', 159000.00, 5, 'DANG_BAN', '2025-12-22 14:28:47', '2025-12-22 14:28:47'),
(2295, 29, 'SP29-S2-C6', 2, 6, 'PTUD_Final/images/phu-kien/non-luoi-trai-classic/hong.png', 159000.00, 5, 'DANG_BAN', '2025-12-22 14:28:47', '2025-12-22 14:28:47'),
(2296, 29, 'SP29-S1-C6', 1, 6, 'PTUD_Final/images/phu-kien/non-luoi-trai-classic/hong.png', 159000.00, 5, 'DANG_BAN', '2025-12-22 14:28:47', '2025-12-22 14:28:47'),
(2297, 31, 'SP31-S4-C5', 4, 5, 'PTUD_Final/images/hoodie/hoodie-oversize-basic/xanh.png', 399000.00, 5, 'DANG_BAN', '2025-12-22 14:28:47', '2025-12-22 14:28:47'),
(2298, 31, 'SP31-S3-C5', 3, 5, 'PTUD_Final/images/hoodie/hoodie-oversize-basic/xanh.png', 399000.00, 4, 'DANG_BAN', '2025-12-22 14:28:47', '2025-12-22 14:28:47'),
(2299, 31, 'SP31-S2-C5', 2, 5, 'PTUD_Final/images/hoodie/hoodie-oversize-basic/xanh.png', 399000.00, 6, 'DANG_BAN', '2025-12-22 14:28:47', '2025-12-22 14:28:47'),
(2300, 31, 'SP31-S1-C5', 1, 5, 'PTUD_Final/images/hoodie/hoodie-oversize-basic/xanh.png', 399000.00, 4, 'DANG_BAN', '2025-12-22 14:28:47', '2025-12-22 14:28:47'),
(2301, 31, 'SP31-S4-C6', 4, 6, 'PTUD_Final/images/hoodie/hoodie-oversize-basic/hong.png', 399000.00, 5, 'DANG_BAN', '2025-12-22 14:28:47', '2025-12-22 14:28:47'),
(2302, 31, 'SP31-S3-C6', 3, 6, 'PTUD_Final/images/hoodie/hoodie-oversize-basic/hong.png', 399000.00, 4, 'DANG_BAN', '2025-12-22 14:28:47', '2025-12-22 14:28:47'),
(2303, 31, 'SP31-S2-C6', 2, 6, 'PTUD_Final/images/hoodie/hoodie-oversize-basic/hong.png', 399000.00, 6, 'DANG_BAN', '2025-12-22 14:28:47', '2025-12-22 14:28:47'),
(2304, 31, 'SP31-S1-C6', 1, 6, 'PTUD_Final/images/hoodie/hoodie-oversize-basic/hong.png', 399000.00, 4, 'DANG_BAN', '2025-12-22 14:28:47', '2025-12-22 14:28:47'),
(2307, 24, 'SP24-S3-C0', 3, NULL, 'PTUD_Final/images/quan/quan-jeans-slim-fit/quan-jeans-slim-fit.png', 499000.00, 30, 'DANG_BAN', '2025-12-23 08:55:54', '2025-12-23 09:01:15'),
(2308, 25, 'SP25-S3-C0', 3, NULL, 'PTUD_Final/images/quan/quan-kaki-regular/quan-kaki-regular.png', 429000.00, 30, 'DANG_BAN', '2025-12-23 08:55:54', '2025-12-23 09:01:15'),
(2309, 28, 'SP28-S3-C0', 3, NULL, 'PTUD_Final/images/ao-khoac/ao-khoac-denim-jacket/ao-khoac-denim-jacket.png', 749000.00, 30, 'DANG_BAN', '2025-12-23 08:55:54', '2025-12-23 09:01:15'),
(2311, 24, 'SP24-S2-C0', 2, NULL, 'PTUD_Final/images/quan/quan-jeans-slim-fit/quan-jeans-slim-fit.png', 499000.00, 30, 'DANG_BAN', '2025-12-23 08:55:54', '2025-12-23 09:01:15'),
(2312, 25, 'SP25-S2-C0', 2, NULL, 'PTUD_Final/images/quan/quan-kaki-regular/quan-kaki-regular.png', 429000.00, 30, 'DANG_BAN', '2025-12-23 08:55:54', '2025-12-23 09:01:15'),
(2313, 28, 'SP28-S2-C0', 2, NULL, 'PTUD_Final/images/ao-khoac/ao-khoac-denim-jacket/ao-khoac-denim-jacket.png', 749000.00, 30, 'DANG_BAN', '2025-12-23 08:55:54', '2025-12-23 09:01:15'),
(2315, 24, 'SP24-S1-C0', 1, NULL, 'PTUD_Final/images/quan/quan-jeans-slim-fit/quan-jeans-slim-fit.png', 499000.00, 30, 'DANG_BAN', '2025-12-23 08:55:54', '2025-12-23 09:01:15'),
(2316, 25, 'SP25-S1-C0', 1, NULL, 'PTUD_Final/images/quan/quan-kaki-regular/quan-kaki-regular.png', 429000.00, 30, 'DANG_BAN', '2025-12-23 08:55:54', '2025-12-23 09:01:15'),
(2317, 28, 'SP28-S1-C0', 1, NULL, 'PTUD_Final/images/ao-khoac/ao-khoac-denim-jacket/ao-khoac-denim-jacket.png', 749000.00, 30, 'DANG_BAN', '2025-12-23 08:55:54', '2025-12-23 09:01:15'),
(2319, 24, 'SP24-S4-C0', 4, NULL, 'PTUD_Final/images/quan/quan-jeans-slim-fit/quan-jeans-slim-fit.png', 499000.00, 30, 'DANG_BAN', '2025-12-23 08:55:54', '2025-12-23 09:01:15'),
(2320, 25, 'SP25-S4-C0', 4, NULL, 'PTUD_Final/images/quan/quan-kaki-regular/quan-kaki-regular.png', 429000.00, 30, 'DANG_BAN', '2025-12-23 08:55:54', '2025-12-23 09:01:15'),
(2321, 28, 'SP28-S4-C0', 4, NULL, 'PTUD_Final/images/ao-khoac/ao-khoac-denim-jacket/ao-khoac-denim-jacket.png', 749000.00, 30, 'DANG_BAN', '2025-12-23 08:55:54', '2025-12-23 09:01:15');

--
-- Triggers `sku_san_pham`
--
DROP TRIGGER IF EXISTS `after_sku_delete`;
DELIMITER $$
CREATE TRIGGER `after_sku_delete` AFTER DELETE ON `sku_san_pham` FOR EACH ROW BEGIN
    UPDATE san_pham sp
    SET sp.so_luong_ton = (
        SELECT COALESCE(SUM(so_luong_ton), 0)
        FROM sku_san_pham 
        WHERE san_pham_id = OLD.san_pham_id
        AND trang_thai = 'DANG_BAN'
    )
    WHERE sp.id = OLD.san_pham_id;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `after_sku_insert`;
DELIMITER $$
CREATE TRIGGER `after_sku_insert` AFTER INSERT ON `sku_san_pham` FOR EACH ROW BEGIN
    UPDATE san_pham sp
    SET sp.so_luong_ton = (
        SELECT COALESCE(SUM(so_luong_ton), 0)
        FROM sku_san_pham 
        WHERE san_pham_id = NEW.san_pham_id
        AND trang_thai = 'DANG_BAN'
    )
    WHERE sp.id = NEW.san_pham_id;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `after_sku_update`;
DELIMITER $$
CREATE TRIGGER `after_sku_update` AFTER UPDATE ON `sku_san_pham` FOR EACH ROW BEGIN
    -- Tính tổng số lượng tồn của tất cả SKU thuộc sản phẩm
    UPDATE san_pham sp
    SET sp.so_luong_ton = (
        SELECT COALESCE(SUM(so_luong_ton), 0)
        FROM sku_san_pham 
        WHERE san_pham_id = NEW.san_pham_id
        AND trang_thai = 'DANG_BAN'
    )
    WHERE sp.id = NEW.san_pham_id;
END
$$
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `anh_san_pham`
--
ALTER TABLE `anh_san_pham`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_anh_san_pham_san_pham` (`san_pham_id`);

--
-- Indexes for table `banners`
--
ALTER TABLE `banners`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `chi_tiet_don_hang`
--
ALTER TABLE `chi_tiet_don_hang`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ctdh_don_hang` (`don_hang_id`),
  ADD KEY `idx_ctdh_san_pham` (`san_pham_id`),
  ADD KEY `idx_chi_tiet_don_hang_sku` (`sku_id`);

--
-- Indexes for table `chi_tiet_gio_hang`
--
ALTER TABLE `chi_tiet_gio_hang`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_ctgh_gio_san_pham` (`gio_hang_id`,`san_pham_id`),
  ADD KEY `idx_ctgh_gio_hang` (`gio_hang_id`),
  ADD KEY `idx_ctgh_san_pham` (`san_pham_id`),
  ADD KEY `fk_ctgh_sku` (`sku_id`);

--
-- Indexes for table `danh_muc_san_pham`
--
ALTER TABLE `danh_muc_san_pham`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_danh_muc_duong_dan` (`duong_dan`),
  ADD KEY `idx_danh_muc_trang_thai` (`trang_thai`);

--
-- Indexes for table `dia_chi`
--
ALTER TABLE `dia_chi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_dia_chi_nguoi_dung` (`nguoi_dung_id`);

--
-- Indexes for table `don_hang`
--
ALTER TABLE `don_hang`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_don_hang_ma` (`ma_don_hang`),
  ADD KEY `idx_don_hang_nguoi_dung_tao_luc` (`nguoi_dung_id`,`tao_luc`),
  ADD KEY `idx_don_hang_trang_thai` (`trang_thai`);

--
-- Indexes for table `gio_hang`
--
ALTER TABLE `gio_hang`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_gio_hang_nguoi_dung` (`nguoi_dung_id`);

--
-- Indexes for table `khuyen_mai`
--
ALTER TABLE `khuyen_mai`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kich_co`
--
ALTER TABLE `kich_co`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_size_duong_dan` (`duong_dan`);

--
-- Indexes for table `lich_su_trang_thai_don_hang`
--
ALTER TABLE `lich_su_trang_thai_don_hang`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ls_don_hang` (`don_hang_id`),
  ADD KEY `idx_ls_nguoi_thay_doi` (`nguoi_thay_doi_id`);

--
-- Indexes for table `mau_sac`
--
ALTER TABLE `mau_sac`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_mau_duong_dan` (`duong_dan`);

--
-- Indexes for table `nguoi_dung`
--
ALTER TABLE `nguoi_dung`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_nguoi_dung_email` (`email`),
  ADD KEY `idx_nguoi_dung_vai_tro` (`vai_tro`),
  ADD KEY `idx_nguoi_dung_trang_thai` (`trang_thai`);

--
-- Indexes for table `phien_dang_nhap`
--
ALTER TABLE `phien_dang_nhap`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_phien_token` (`token`),
  ADD KEY `idx_phien_nguoi_dung` (`nguoi_dung_id`);

--
-- Indexes for table `san_pham`
--
ALTER TABLE `san_pham`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_san_pham_duong_dan` (`duong_dan`),
  ADD KEY `idx_san_pham_trang_thai` (`trang_thai`),
  ADD KEY `idx_san_pham_tao_luc` (`tao_luc`),
  ADD KEY `idx_san_pham_ten` (`ten_san_pham`),
  ADD KEY `idx_san_pham_danh_muc` (`danh_muc_id`);

--
-- Indexes for table `sku_san_pham`
--
ALTER TABLE `sku_san_pham`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_sku_code` (`ma_sku`),
  ADD UNIQUE KEY `uq_sp_size_color` (`san_pham_id`,`kich_co_id`,`mau_sac_id`),
  ADD KEY `idx_sku_sp` (`san_pham_id`),
  ADD KEY `idx_sku_size` (`kich_co_id`),
  ADD KEY `idx_sku_color` (`mau_sac_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `anh_san_pham`
--
ALTER TABLE `anh_san_pham`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1037;

--
-- AUTO_INCREMENT for table `banners`
--
ALTER TABLE `banners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `chi_tiet_don_hang`
--
ALTER TABLE `chi_tiet_don_hang`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `chi_tiet_gio_hang`
--
ALTER TABLE `chi_tiet_gio_hang`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `danh_muc_san_pham`
--
ALTER TABLE `danh_muc_san_pham`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `dia_chi`
--
ALTER TABLE `dia_chi`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `don_hang`
--
ALTER TABLE `don_hang`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `gio_hang`
--
ALTER TABLE `gio_hang`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `khuyen_mai`
--
ALTER TABLE `khuyen_mai`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `kich_co`
--
ALTER TABLE `kich_co`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `lich_su_trang_thai_don_hang`
--
ALTER TABLE `lich_su_trang_thai_don_hang`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `mau_sac`
--
ALTER TABLE `mau_sac`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `nguoi_dung`
--
ALTER TABLE `nguoi_dung`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `phien_dang_nhap`
--
ALTER TABLE `phien_dang_nhap`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `san_pham`
--
ALTER TABLE `san_pham`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `sku_san_pham`
--
ALTER TABLE `sku_san_pham`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2337;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `anh_san_pham`
--
ALTER TABLE `anh_san_pham`
  ADD CONSTRAINT `fk_anh_san_pham_san_pham` FOREIGN KEY (`san_pham_id`) REFERENCES `san_pham` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `chi_tiet_don_hang`
--
ALTER TABLE `chi_tiet_don_hang`
  ADD CONSTRAINT `fk_chi_tiet_don_hang_sku` FOREIGN KEY (`sku_id`) REFERENCES `sku_san_pham` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ctdh_don_hang` FOREIGN KEY (`don_hang_id`) REFERENCES `don_hang` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ctdh_san_pham` FOREIGN KEY (`san_pham_id`) REFERENCES `san_pham` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `chi_tiet_gio_hang`
--
ALTER TABLE `chi_tiet_gio_hang`
  ADD CONSTRAINT `fk_ctgh_gio_hang` FOREIGN KEY (`gio_hang_id`) REFERENCES `gio_hang` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ctgh_san_pham` FOREIGN KEY (`san_pham_id`) REFERENCES `san_pham` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ctgh_sku` FOREIGN KEY (`sku_id`) REFERENCES `sku_san_pham` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `dia_chi`
--
ALTER TABLE `dia_chi`
  ADD CONSTRAINT `fk_dia_chi_nguoi_dung` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `don_hang`
--
ALTER TABLE `don_hang`
  ADD CONSTRAINT `fk_don_hang_nguoi_dung` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `gio_hang`
--
ALTER TABLE `gio_hang`
  ADD CONSTRAINT `fk_gio_hang_nguoi_dung` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `lich_su_trang_thai_don_hang`
--
ALTER TABLE `lich_su_trang_thai_don_hang`
  ADD CONSTRAINT `fk_ls_don_hang` FOREIGN KEY (`don_hang_id`) REFERENCES `don_hang` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ls_nguoi_thay_doi` FOREIGN KEY (`nguoi_thay_doi_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `phien_dang_nhap`
--
ALTER TABLE `phien_dang_nhap`
  ADD CONSTRAINT `fk_phien_nguoi_dung` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `san_pham`
--
ALTER TABLE `san_pham`
  ADD CONSTRAINT `fk_san_pham_danh_muc` FOREIGN KEY (`danh_muc_id`) REFERENCES `danh_muc_san_pham` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `sku_san_pham`
--
ALTER TABLE `sku_san_pham`
  ADD CONSTRAINT `fk_sku_color` FOREIGN KEY (`mau_sac_id`) REFERENCES `mau_sac` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_sku_size` FOREIGN KEY (`kich_co_id`) REFERENCES `kich_co` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_sku_sp` FOREIGN KEY (`san_pham_id`) REFERENCES `san_pham` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
