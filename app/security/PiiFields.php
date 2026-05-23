<?php
declare(strict_types=1);

/** Column lists for Tier A (nguoi_dung), Tier B (dia_chi), and Tier C (don_hang) encryption. */
final class PiiFields
{
    /** @var list<string> */
    public const USER = ['ho_ten', 'so_dien_thoai', 'ngay_sinh'];

    /** @var list<string> */
    public const ADDRESS = [
        'ten_nguoi_nhan',
        'so_dien_thoai',
        'tinh_thanh',
        'quan_huyen',
        'phuong_xa',
        'dia_chi_cu_the',
    ];

    /**
     * Tier C: shipping snapshot on don_hang.
     * ghi_chu and ly_do_huy are intentionally excluded — they are free-text admin/user
     * notes, not structured PII, and must remain searchable for order management.
     *
     * @var list<string>
     */
    public const ORDER = [
        'nguoi_nhan',
        'sdt_nguoi_nhan',
        'dia_chi_giao_hang',
    ];
}
