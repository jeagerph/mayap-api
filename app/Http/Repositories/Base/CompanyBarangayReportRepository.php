<?php

namespace App\Http\Repositories\Base;

use Illuminate\Support\Facades\Auth;

use App\Models\CompanyBarangayReport;

class CompanyBarangayReportRepository
{
    public function new($data, $company)
    {
        return new CompanyBarangayReport([
            'province_id' => $data['province_id'],
            'city_id' => $data['city_id'],
            'barangay_id' => $data['barangay_id'],
            'beneficiaries' => isset($data['beneficiaries'])
                ? $data['beneficiaries']
                : 0,
            'officers' => isset($data['officers'])
                ? $data['officers']
                : 0,
            'household' => isset($data['household'])
                ? $data['household']
                : 0,
            'priorities' => isset($data['priorities'])
                ? $data['priorities']
                : 0,
            'networks' => isset($data['networks'])
                ? $data['networks']
                : 0,
            'incentives' => isset($data['incentives'])
                ? $data['incentives']
                : 0,
            'patients' => isset($data['patients'])
                ? $data['patients']
                : 0,
            'assistances' => isset($data['assistances'])
                ? $data['assistances']
                : 0,
            'requested' => isset($data['requested'])
                ? $data['requested']
                : 0,
            'assisted' => isset($data['assisted'])
                ? $data['assisted']
                : 0,
            'created_by' => Auth::id() ?: 1
        ]);
    }

    public function updateReport($field, $count, $barangayId, $company, $mode = 'add')
    {
        $checking = $company->barangayReports()->where('barangay_id', $barangayId)->first();

        if ($checking):

            if ($mode == 'add'):
                $currentTotal = $checking->{$field} + $count;
            else:
                $currentTotal = $checking->{$field} - $count;
            endif;
            
            $form[$field] = $currentTotal;
            $form['updated_by'] = 1;

            $checking->update($form);
        
        else:

            $barangay = \App\Models\Barangay::find($barangayId);

            $form = [
                'province_id' => $barangay->prov_code,
                'city_id' => $barangay->city_code,
                'barangay_id' => $barangay->id,
            ];

            $form[$field] = $count;
            $form['updated_by'] = 1;

            $company->barangayReports()->save(
                self::new($form, $company)
            );

            $company->update([
                'barangay_report_updated_at' => now()->format('Y-m-d H:i:s'),
                'updated_by' => 1,
            ]);
        endif;
    }
}
?>