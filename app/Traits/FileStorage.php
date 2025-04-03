<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait FileStorage
{
    public function manageAttachment($file, $folder)
    {
        $fileName = Str::random(16) . time() . '.' . $file->getClientOriginalExtension();

        $folderPath = $folder . '/';
        $folderPath .= now()->format('Y') . '/';
        $folderPath .= now()->format('F') . '/';
        $folderPath .= now()->format('d') . '/';
        $folderPath .= $fileName;

        $this->saveAttachment($folderPath, $file);

        return [
            'path' => $folderPath,
            'new_name' => $fileName
        ];
    }
    
    public function manageImage($image, $folder)
		{
            $fileName = Str::random(16) . time() . '.' . $image->getClientOriginalExtension();

            $folderPath = $folder . '/';
            $folderPath .= now()->format('Y') . '/';
            $folderPath .= now()->format('F') . '/';
            $folderPath .= now()->format('d');

			$initImage = \Image::make($image)->orientate();

			// Saves compress size
            $compressPath = $folderPath . '/compress/' . $fileName;
            $compressImage = $initImage->encode('jpg', 50);
            
			$this->saveFile(
                $compressPath,
                $compressImage,
                true
            );

			// Saves thumbnail size
            $thumbPath = $folderPath . '/thumbnail/' . $fileName;
            $thumbImage = $initImage->fit(100,100)->encode('jpg', 75);

			$this->saveFile(
                $thumbPath,
                $thumbImage,
                true
            );

            return [
                'compress_path' => $compressPath, 
                'thumbnail_path' => $thumbPath, 
                'new_name' => $fileName
            ];
		}

    public function saveFile($path, $file, $isBase64=false)
    {
        if(env('STORAGE_DISK') == 's3'):
            return Storage::disk('s3')->put(
                'storage/' . $path,
                (!$isBase64?file_get_contents($file):$file)
            );
        else:
            return Storage::disk('public')->put(
                $path,
                (!$isBase64?file_get_contents($file):$file)
            );
        endif;
    }

    public function savePDF($path, $file)
    {
        if(env('STORAGE_DISK') == 's3'):
            return Storage::disk('s3')->put(
                'storage/' . $path,
                $file
            );
        else:
            return Storage::disk('public')->put(
                $path,
                $file
            );
        endif;
    }

    public function saveAttachment($path, $file)
    {
        if(env('STORAGE_DISK') == 's3'):
            return Storage::disk('s3')->put(
                'storage/' . $path,
                file_get_contents($file)
            );
        else:
            return Storage::disk('public')->put(
                $path,
                file_get_contents($file)
            );
        endif;
    }

    public function deleteFile($path)
    {
        if(env('STORAGE_DISK') == 's3'):
            if (!Storage::disk('s3')->exists($path)) return false;

            return Storage::disk('s3')->delete(
                'storage/' . $path
            );
        else:
            if (!Storage::disk('public')->exists($path)) return false;

            return Storage::disk('public')->delete(
                $path
            );
        endif;
    }

    public static function base64ToFile($base64File)
    {
        list($type, $file) = explode(';', $base64File);
        list(, $file) = explode(',', $file);
        return base64_decode($file);
    }
}

?>