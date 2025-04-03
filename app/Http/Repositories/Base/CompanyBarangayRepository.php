<?php

namespace App\Http\Repositories\Base;

use Illuminate\Support\Facades\Auth;

use App\Models\CompanyBarangay;

use App\Traits\FileStorage;

class CompanyBarangayRepository
{
    use FileStorage;

    public function new($data, $company)
    {
        return new CompanyBarangay([
            'province_id' => $data['province_id'],
            'city_id' => $data['city_id'],
            'barangay_id' => $data['barangay_id'],
            'province_name' => $data['province_name'],
            'city_name' => $data['city_name'],
            'barangay_name' => $data['barangay_name'],
            'city_logo' => null,
            'barangay_logo' => null,
            'status' => 1,
            'created_by' => Auth::id() ?: 1
        ]);
    }

    public function update($data)
    {
        return [
            'province_id' => $data['province_id'],
            'city_id' => $data['city_id'],
            'barangay_id' => $data['barangay_id'],
            'province_name' => strtoupper($data['province_name']),
            'city_name' => strtoupper($data['city_name']),
            'barangay_name' => strtoupper($data['barangay_name']),
            'updated_by' => Auth::id() ?: 1
        ];
    }

    public function uploadLogo($currentPath, $data, $folderDir = 'barangay/logo')
    {
        if($currentPath):
            $this->deleteFile($currentPath);
        endif;

        $logo = $data['logo'];

        $fileName = randomFileName();

        $filePath = randomFilePath($folderDir, $fileName);

        $this->saveFile($filePath, self::base64ToFile($logo), true);
        
        return $filePath;
    }
}
?>