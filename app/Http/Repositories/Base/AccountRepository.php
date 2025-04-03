<?php

namespace App\Http\Repositories\Base;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Str;

use App\Models\Account;
use App\Models\Module;

use App\Http\Repositories\Base\AccountPermissionRepository;

use App\Traits\FileStorage;

class AccountRepository
{
    use FileStorage;

    public function __construct()
    {
        $this->permissionRepository = new AccountPermissionRepository;
    }

    public function new($data)
    {
        return new Account([
            'account_type' => $data['account_type'],
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'mobile_number' => $data['mobile_number'],
            'photo' => null,
            'created_by' => Auth::id()
        ]);
    }

    public function update($data)
    {
        return [
            'full_name' => strtoupper($data['full_name']),
            'email' => $data['email'],
            'mobile_number' => $data['mobile_number'],
            'updated_by' => Auth::id()
        ];
    }

    public function updatePhoto($currentPath, $data)
    {
        if($currentPath):
            $this->deleteFile($currentPath);
        endif;
        
        $photo = $data['photo'];

        $fileName = randomFileName();

        $filePath = randomFilePath('account/photo', $fileName);

        $this->saveFile($filePath, self::base64ToFile($photo), true);
        
        return [
            'photo' => $filePath,
            'updated_by' => Auth::id()
        ];
    }

    public function setCompanyPermissions($account)
    {
        $modules = Module::where('is_admin', 0)->get();

        foreach($modules as $module):

            $account->permissions()->save(
                $this->permissionRepository->new([
                    'module_id' => $module->id,
                    'access' => 1,
                    'index' => 1,
                    'store' => 1,
                    'update' => 1,
                    'destroy' => 1,
                ])
            );

        endforeach;
    }
}
?>