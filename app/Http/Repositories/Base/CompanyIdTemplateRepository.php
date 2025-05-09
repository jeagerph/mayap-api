<?php

namespace App\Http\Repositories\Base;

use Illuminate\Support\Facades\Auth;

use App\Models\CompanyIdTemplate;

use App\Traits\FileStorage;

class CompanyIdTemplateRepository
{
    use FileStorage;

    public function new($data, $company)
    {
        return new CompanyIdTemplate([
            'code' => self::generateCode($company),
            'name' => strtoupper($data['name']),
            'description' => $data['description'],
            'view' => $data['view']
                ? json_encode($data['view'])
                : null,
            'options' => $data['options']
                ? json_encode($data['options'])
                : null,
            'content' => $data['content']
                ? json_encode($data['content'])
                : null,
            'approvals' => $data['approvals']
                ? json_encode($data['approvals'])
                : null,
            'created_by' => Auth::id() ?: 1
        ]);
    }

    public function update($data)
    {
        return [
            'name' => strtoupper($data['name']),
            'description' => $data['description'],
            'view' => $data['view']
                ? json_encode($data['view'])
                : null,
            'options' => $data['options']
                ? json_encode($data['options'])
                : null,
            'content' => $data['content']
                ? json_encode($data['content'])
                : null,
            'approvals' => $data['approvals']
                ? json_encode($data['approvals'])
                : null,
            'updated_by' => Auth::id() ?: 1
        ];
    }

    public function uploadSignature($currentPath, $photo, $input, $folderDir = 'id-template/signature')
    {
        // if($currentPath):
        //     $this->deleteFile($currentPath);
        // endif;

        $fileName = randomFileName();

        $filePath = randomFilePath($folderDir, $fileName);

        $this->saveFile($filePath, self::base64ToFile($photo), true);
        
        return [
            $input => $filePath,
            'updated_by' => Auth::id() ?: 1
        ];
    }

    public function generateCode($company)
    {
        $count = CompanyIdTemplate::where('company_id', $company->id)->count();

        return 'IDTEMP-' . $company->id . '-' . leadingZeros($count+1);
    }

    public function isAllowedToDelete($template)
    {
        // if($template->documents->count())
        //     return abort(403, 'Forbidden: ID Template has related documents.');
    }
}
?>