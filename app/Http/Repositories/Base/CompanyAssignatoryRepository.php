<?php

namespace App\Http\Repositories\Base;

use Illuminate\Support\Facades\Auth;

use App\Models\CompanyAssignatory;

use App\Traits\FileStorage;

class CompanyAssignatoryRepository
{
    use FileStorage;

    public function new($data, $company)
    {
        $orderNo = 1;

        $checking = $company->assignatories()->orderBy('order_no', 'desc')->latest()->first();

        if ($checking) $orderNo = $checking->order_no + 1;

        return new CompanyAssignatory([
            'order_no' => $orderNo,
            'name' => strtoupper($data['name']),
            'position' => strtoupper($data['position']),
            'enabled' => 1,
            'created_by' => Auth::id() ?: 1
        ]);
    }

    public function update($data)
    {
        return [
            'name' => strtoupper($data['name']),
            'position' => strtoupper($data['position']),
            'updated_by' => Auth::id() ?: 1
        ];
    }

    public function refreshOrderNo($company)
    {
        $assignatories = $company->assignatories()->orderBy('order_no', 'asc')->get();
        $orderNo = 1;

        foreach($assignatories as $assignatory):

            $assignatory->update([
                'order_no' => $orderNo,
                'updated_by' => Auth::id() ?: 1
            ]);

            $orderNo++;

        endforeach;
    }

    public function uploadSignature($currentPath, $data, $folderDir)
    {
        if($currentPath):
            $this->deleteFile($currentPath);
        endif;
        
        $photo = $data['photo'];

        $fileName = randomFileName();

        $filePath = randomFilePath($folderDir, $fileName);

        $this->saveFile($filePath, self::base64ToFile($photo), true);
        
        return $filePath;
    }

    public function isAllowedToDelete($assignatory)
    {
        // if ($assignatory->members->count()):
        //     return abort(403, 'Forbidden. Classification has related Member records. Kindly delete it first before deleting Classification.');
        // endif;
    }
}
?>