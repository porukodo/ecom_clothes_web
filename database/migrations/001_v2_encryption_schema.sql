-- V2 branch only: widen columns for AES-256-GCM ciphertext (Tier A + B).
-- Run against ecom_clothes_web after backup. Does NOT encrypt don_hang (Tier C).

USE `ecom_clothes_web`;

-- Tier A: nguoi_dung (email stays plaintext for login)
ALTER TABLE `nguoi_dung`
  MODIFY `ho_ten` TEXT DEFAULT NULL,
  MODIFY `so_dien_thoai` TEXT DEFAULT NULL,
  MODIFY `ngay_sinh` TEXT DEFAULT NULL;

-- Tier B: dia_chi
ALTER TABLE `dia_chi`
  MODIFY `ten_nguoi_nhan` TEXT NOT NULL,
  MODIFY `so_dien_thoai` TEXT NOT NULL,
  MODIFY `tinh_thanh` TEXT NOT NULL,
  MODIFY `quan_huyen` TEXT NOT NULL,
  MODIFY `phuong_xa` TEXT NOT NULL,
  MODIFY `dia_chi_cu_the` TEXT NOT NULL;
