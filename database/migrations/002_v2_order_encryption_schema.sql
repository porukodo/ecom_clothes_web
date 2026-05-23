-- Tier C: widen don_hang shipping PII columns to TEXT to hold AES-256-GCM ciphertext.
-- Run before setting ENCRYPTION_ENABLED=true and before running the migration script.
-- ghi_chu and ly_do_huy are intentionally left as-is (plaintext, not in Tier C scope).

ALTER TABLE `don_hang`
    MODIFY `nguoi_nhan`       TEXT         NOT NULL,
    MODIFY `sdt_nguoi_nhan`   TEXT         NOT NULL,
    MODIFY `dia_chi_giao_hang` TEXT        NOT NULL;
