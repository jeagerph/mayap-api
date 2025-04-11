<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BatchPrintSDNDataResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "code" => $this->code,
            "view" => $this->view,
            "options" => $this->options,
            "beneficiary" => [
                "full_name" => $this->beneficiary->last_name . ', ' . $this->beneficiary->first_name . ' ' . $this->beneficiary->middle_name,
                "first_name" => $this->beneficiary->first_name,
                "middle_name" => $this->beneficiary->middle_name,
                "last_name" => $this->beneficiary->last_name,
                "verify_voter" => $this->beneficiary->verify_voter,
                "voter_details" => $this->beneficiary->voter_details,
                "code" => self::formatCode($this->beneficiary->code, $this->beneficiary->voter_details ? $this->beneficiary->voter_details->precinct_no : null),
                "photo" => $this->beneficiary->photo,
            ],
            "company_id" => $this->company_id,
            "content" => $this->content,
            "approvals" => $this->approvals,
            "identification_date" => $this->identification_date,
            "name" => $this->name,
            "companyIdSetting" => $this->company->idSetting,
            "company" => [
                "name" => $this->company->name,
                "address" => $this->company->address,
                "contact" => $this->company->contact,
                "logo" => $this->company->logo,
                "sub_logo" => $this->company->sub_logo,
                "status" => $this->company->status
            ],
            "description" => $this->description,
        ];
    }

    public function formatCode($beneficiary_code, $precinct_no)
    {
        $parts = explode('-', $beneficiary_code);

        if ($precinct_no !== null) {
            return "{$parts[0]}-{$parts[1]}-{$precinct_no}-{$parts[2]}";
        }

        return $beneficiary_code;
    }
}
