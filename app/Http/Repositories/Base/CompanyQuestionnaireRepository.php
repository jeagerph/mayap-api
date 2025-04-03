<?php

namespace App\Http\Repositories\Base;

use Illuminate\Support\Facades\Auth;

use App\Models\CompanyQuestionnaire;

class CompanyQuestionnaireRepository
{
    public function new($data, $company)
    {
        $orderNo = 1;

        $checking = $company->questionnaires()->orderBy('order_no', 'desc')->latest()->first();

        if ($checking) $orderNo = $checking->order_no + 1;

        return new CompanyQuestionnaire([
            'order_no' => $orderNo,
            'question' => $data['question'],
            'description' => $data['description'],
            'enabled' => 1,
            'created_by' => Auth::id() ?: 1
        ]);
    }

    public function update($data)
    {
        return [
            'question' => $data['question'],
            'description' => $data['description'],
            'updated_by' => Auth::id() ?: 1
        ];
    }

    public function refreshOrderNo($company)
    {
        $questionnaires = $company->questionnaires()->orderBy('order_no', 'asc')->get();
        $orderNo = 1;

        foreach($questionnaires as $questionnaire):

            $questionnaire->update([
                'order_no' => $orderNo,
                'updated_by' => Auth::id() ?: 1
            ]);

            $orderNo++;

        endforeach;
    }

    public function isAllowedToDelete($classification)
    {
        // if ($classification->members->count()):
        //     return abort(403, 'Forbidden. Classification has related Member records. Kindly delete it first before deleting Classification.');
        // endif;
    }
}
?>