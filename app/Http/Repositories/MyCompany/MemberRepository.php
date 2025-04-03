<?php

namespace App\Http\Repositories\MyCompany;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use App\Models\Slug;
use App\Models\Member;
use App\Models\Company;
// use App\Models\MemberRelative;
// use App\Models\BarangayAttachment;

use App\Http\Repositories\Base\MemberRepository as Repository;
use App\Http\Repositories\Base\SlugRepository;
use App\Http\Repositories\Base\PDFRepository;
use App\Http\Repositories\Base\CompanyRepository;
// use App\Http\Repositories\Base\MemberRelativeRepository;
// use App\Http\Repositories\Base\BarangayIdentificationRepository;
// use App\Http\Repositories\Base\BarangayAttachmentRepository;

class MemberRepository
{
    public function __construct()
    {
        $this->baseRepository = new Repository;
        $this->pdfRepository = new PDFRepository;
        $this->slugRepository = new SlugRepository;
        $this->companyRepository = new CompanyRepository;
        // $this->identificationRepository = new BarangayIdentificationRepository;
        // $this->relativeRepository = new ResidentRelativeRepository;
        // $this->attachmentRepository = new BarangayAttachmentRepository;
    }

    public function store($request)
	{
        $company = Auth::user()->company();

        $newMember = $this->baseRepository->store($request, $company);

        $this->baseRepository->updateAddress($newMember);

        if($request->input('photo')):
            $newMember->update(
                $this->baseRepository->uploadPhoto(
                    $newMember->photo,
                    $request
                )
            );
        endif;
        
        if($request->input('left_thumbmark')):
            $newMember->update(
                $this->baseRepository->uploadThumbmark(
                    $newMember->left_thumbmark,
                    $request->input('left_thumbmark'),
                    'left_thumbmark'
                )
            );
        endif;

        if($request->input('right_thumbmark')):
            $newMember->update(
                $this->baseRepository->uploadThumbmark(
                    $newMember->right_thumbmark,
                    $request->input('right_thumbmark'),
                    'right_thumbmark'
                )
            );
        endif;

        // $this->baseRepository->registrationNotification($newMember);

        // $this->baseRepository->updateBarangayRecordCount($newResident);

        return $newMember;
    }

    public function import($request)
    {
        \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\MembersImport, $request->file('file'));

        return response([
            'message' => 'Data has been imported successfully.'
        ], 200);
    }

    public function check($request)
    {
        $company = Auth::user()->company();

        $model = new Member;

        $member = $model->where('company_id', $company->id)
                        ->where('first_name', $request->input('first_name'))
                        ->where('last_name', $request->input('last_name'))
                        ->where('gender', $request->input('gender'))
                        ->where('date_of_birth', $request->input('date_of_birth'))
                        ->first();

        if($member) return $member->toArrayMemberCheckingRelated();

        return $member;
    }

    public function show($request, $code)
    {
        $company = Auth::user()->company();

        $model = Slug::findCodeOrDie($code);

        $member = $model->slug;

        $this->companyRepository->isMemberRelated($company, $member->id);

        return $member->toArrayMembersRelated();
    }

    public function edit($request, $code)
    {
        $company = Auth::user()->company();

        $model = Slug::findCodeOrDie($code);

        $member = $model->slug;

        $this->companyRepository->isMemberRelated($company, $member->id);

        return $member->toArrayEdit();
    }

    public function update($request, $code)
    {
        $company = Auth::user()->company();

        $model = Slug::findCodeOrDie($code);

        $member = $model->slug;

        $this->companyRepository->isMemberRelated($company, $member->id);

        $member->update(
            $this->baseRepository->update($request)
        );

        if($request->has('photo_for_upload') && $request->input('photo_for_upload')):
            $member->update(
                $this->baseRepository->uploadPhoto(
                    $member->photo,
                    [
                        'photo' => $request->input('photo_for_upload')
                    ]
                )
            );
        endif;

        $this->baseRepository->updateAddress($member);

        // $this->baseRepository->updateBarangayRecordCount($resident);

        return (Member::find($member->id))->toArrayMembersRelated();
    }

    public function updatePhoto($request, $code)
    {
        $company = Auth::user()->company();

        $model = Slug::findCodeOrDie($code);

        $member = $model->slug;

        $this->companyRepository->isMemberRelated($company, $member->id);

        $member->update(
            $this->baseRepository->uploadPhoto(
                $member->photo,
                $request
            )
        );

        return (Member::find($member->id))->toArrayMembersRelated();
    }

    public function updateLeftThumbmark($request, $code)
    {
        $company = Auth::user()->company();

        $model = Slug::findCodeOrDie($code);

        $member = $model->slug;

        $this->companyRepository->isMemberRelated($company, $member->id);

        $member->update(
            $this->baseRepository->uploadThumbmark(
                $member->left_thumbmark,
                $request->input('thumbmark'),
                'left_thumbmark'
            )
        );

        return (Member::find($member->id))->toArrayMembersRelated();
    }

    public function updateRightThumbmark($request, $code)
    {
        $company = Auth::user()->company();

        $model = Slug::findCodeOrDie($code);

        $member = $model->slug;

        $this->companyRepository->isMemberRelated($company, $member->id);

        $member->update(
            $this->baseRepository->uploadThumbmark(
                $member->right_thumbmark,
                $request->input('thumbmark'),
                'right_thumbmark'
            )
        );

        return (Member::find($member->id))->toArrayMembersRelated();
    }

    public function showProfile($request, $code)
    {
        $company = Auth::user()->company();

        $model = Slug::findCodeOrDie($code);

        $member = $model->slug;

        $this->companyRepository->isMemberRelated($company, $member->id);

        return $member->toArrayMemberProfileRelated();
    }

    public function showContact($request, $code)
    {
        $company = Auth::user()->company();

        $model = Slug::findCodeOrDie($code);

        $member = $model->slug;

        $this->companyRepository->isMemberRelated($company, $member->id);

        return [
            'member_name' => $member->fullName('F M L'),
            'member_mobile_number' => $member->contact_no,
            'emergency_name' => $member->emergency_contact_name,
            'emergency_mobile_number' => $member->emergency_contact_no,
        ];
    }

    public function destroy($request, $code)
    {
        $company = Auth::user()->company();

        $model = Slug::findCodeOrDie($code);

        $member = $model->slug;

        $this->companyRepository->isMemberRelated($company, $member->id);

        $this->baseRepository->isAllowedToDestroy($member);

        $member->delete();

        return response([
            'message' => 'Data deleted successfully.'
        ], 200);
    }

    public function customDownloadIdentification($request, $code)
    {
        $company = Auth::user()->company();

        $model = Slug::findCodeOrDie($code);

        $member = $model->slug;

        $this->companyRepository->isMemberRelated($company, $member->id);

        $now = now()->format('Y-m-d');

        $filename = $this->pdfRepository->fileName(
            $now,
            $now,
            'membership-' . $member->code . '-' . $request->query('templateCode'),
            '.pdf');

        $pdf = \PDF::loadView(
            "identifications.sample.index",
            [
                'member' => $member,
                'template' => $request->query('templateCode')
            ]
        )
        ->setOption('margin-bottom', '0mm')
        ->setOption('margin-top', '0mm')
        ->setOption('margin-right', '0mm')
        ->setOption('margin-left', '0mm');

        return response([
            'path' => $this->pdfRepository->export(
                $pdf->output(),
                $filename,
                'pdf/identifications/sample'
            )
        ],
        200);
    }
}
?>