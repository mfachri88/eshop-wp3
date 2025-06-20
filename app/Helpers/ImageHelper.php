<?php
namespace App\Helpers;

class ImageHelper
{
    public static function uploadAndResize($file, $directory, $fileName, $width = null, $height = null)
    {
        $destinationPath = public_path($directory);
        
        // ✅ SOLUSI: Cek apakah direktori ada, jika tidak, buat direktori tersebut
        // Ini akan membuat folder 'storage/img-customer' atau 'storage/img-produk' secara otomatis.
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0775, true);
        }

        $extension = strtolower($file->getClientOriginalExtension());
        $image = null;

        // Tentukan metode pembuatan gambar berdasarkan ekstensi file
        switch ($extension) {
            case 'jpeg':
            case 'jpg':
                $image = imagecreatefromjpeg($file->getRealPath());
                break;
            case 'png':
                $image = imagecreatefrompng($file->getRealPath());
                break;
            case 'gif':
                $image = imagecreatefromgif($file->getRealPath());
                break;
            default:
                throw new \Exception('Unsupported image type');
        }

        // Resize gambar jika lebar diset
        if ($width) {
            $oldWidth = imagesx($image);
            $oldHeight = imagesy($image);
            $aspectRatio = $oldWidth / $oldHeight;
            if (!$height) {
                $height = $width / $aspectRatio; // Hitung tinggi dengan mempertahankan aspek rasio
            }
            $newImage = imagecreatetruecolor($width, $height);
            imagecopyresampled($newImage, $image, 0, 0, 0, 0, $width, $height, $oldWidth, $oldHeight);
            imagedestroy($image);
            $image = $newImage;
        }

        // ✅ PERBAIKAN: Gunakan DIRECTORY_SEPARATOR untuk path yang konsisten untuk menghindari slash ganda
        $fullPath = rtrim($destinationPath, '/\\') . DIRECTORY_SEPARATOR . $fileName;

        // Simpan gambar dengan kualitas asli
        switch ($extension) {
            case 'jpeg':
            case 'jpg':
                imagejpeg($image, $fullPath);
                break;
            case 'png':
                imagepng($image, $fullPath);
                break;
            case 'gif':
                imagegif($image, $fullPath);
                break;
        }

        imagedestroy($image);
        return $fileName;
    }
}