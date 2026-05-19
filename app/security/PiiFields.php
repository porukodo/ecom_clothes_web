<?php
declare(strict_types=1);

/** Column lists for Tier A (nguoi_dung) and Tier B (dia_chi) encryption. */
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
}
