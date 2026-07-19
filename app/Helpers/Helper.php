<?php

namespace App\Helpers;

class Helper
{
    /**
     * Format tanggal ke Bahasa Indonesia
     */
    public static function formatTanggal(string $date): string
    {
        $bulan = [
            1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'Mei',6=>'Jun',
            7=>'Jul',8=>'Ags',9=>'Sep',10=>'Okt',11=>'Nov',12=>'Des'
        ];
        $ts = strtotime($date);
        return date('d', $ts) . ' ' . $bulan[(int)date('n', $ts)] . ' ' . date('Y', $ts);
    }

    /**
     * Format tanggal ke Bulan Tahun (mis. Des 2024)
     */
    public static function formatBulanTahun(string $date): string
    {
        $bulan = [
            1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'Mei',6=>'Jun',
            7=>'Jul',8=>'Agu',9=>'Sep',10=>'Okt',11=>'Nov',12=>'Des'
        ];
        $ts = strtotime($date);
        return $bulan[(int)date('n', $ts)] . ' ' . date('Y', $ts);
    }

    /**
     * Format ukuran file
     */
    public static function formatFileSize(int $bytes): string
    {
        if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024) return round($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }
}

