<div class="ap-content-content">

    <div class="ap-content-watermark">
        @if ($company->logo)
            <img src="{{ env('CDN_URL', '') . '/storage/' . $company->logo }}">
        @endif
    </div>

    {{-- <p class="ap-content-title">
        @if(property_exists($content, 'title') && $content->title)
            {{strtoupper($content->title)}}
        @endif
    </p>

    <p class="ap-content-salutation">
        @if(property_exists($content, 'salutation') && $content->salutation)
            {{$content->salutation}}
        @endif
    </p> --}}

    <div class="ap-content-body">
        @php

            $modifiedContentBody = str_replace(
                '{{content_title}}',
                $content->title,
                $content->body
            );

            $modifiedContentBody = str_replace(
                '{{content_salutation}}',
                $content->salutation,
                $modifiedContentBody
            );

            $modifiedContentBody = str_replace(
                '{{box_checked}}',
                '<span class="bx bx-checkbox-checked" style="font-size: 60px; line-height: 38px; vertical-align: middle;"></span>',
                $modifiedContentBody
            );

            $modifiedContentBody = str_replace(
                '{{box_unchecked}}',
                '<span class="bx bx-checkbox" style="font-size: 60px; line-height: 38px; vertical-align: middle;"></span>',
                $modifiedContentBody
            );

            $modifiedContentBody = str_replace(
                '{{beneficiary_full_name}}',
                $beneficiary->fullName(),
                $modifiedContentBody
            );

            $modifiedContentBody = str_replace(
                '{{beneficiary_age}}',
                (new \Carbon\Carbon($beneficiary->date_of_birth))->age,
                $modifiedContentBody
            );

            $modifiedContentBody = str_replace(
                '{{beneficiary_date_of_birth}}',
                strtoupper((new \Carbon\Carbon($beneficiary->date_of_birth))->format('F d, Y')),
                $modifiedContentBody
            );

            $modifiedContentBody = str_replace(
                '{{beneficiary_place_of_birth}}',
                strtoupper($beneficiary->place_of_birth),
                $modifiedContentBody
            );

            $modifiedContentBody = str_replace(
                '{{beneficiary_address}}',
                strtoupper($beneficiary->address),
                $modifiedContentBody
            );

            $modifiedContentBody = str_replace(
                '{{beneficiary_gender}}',
                strtoupper($beneficiary->gender == 1 ? 'MALE':'FEMALE'),
                $modifiedContentBody
            );

            $modifiedContentBody = str_replace(
                '{{beneficiary_civil_status}}',
                $beneficiary->civil_status
                    ? strtoupper($beneficiary->civil_status)
                    : '',
                $modifiedContentBody
            );

            $modifiedContentBody = str_replace(
                '{{beneficiary_gender_pronoun_title_case}}',
                $beneficiary->gender == 1 ? 'He':'She',
                $modifiedContentBody
            );

            $modifiedContentBody = str_replace(
                '{{beneficiary_gender_pronoun_lower_case}}',
                $beneficiary->gender == 1 ? 'he':'she',
                $modifiedContentBody
            );

            $modifiedContentBody = str_replace(
                '{{beneficiary_gender_possessive_title_case}}',
                $beneficiary->gender == 1 ? 'His':'Her',
                $modifiedContentBody
            );

            $modifiedContentBody = str_replace(
                '{{beneficiary_gender_possessive_lower_case}}',
                $beneficiary->gender == 1 ? 'his':'her',
                $modifiedContentBody
            );

            $modifiedContentBody = str_replace(
                '{{beneficiary_gender_possessive_pronoun_title_case}}',
                $beneficiary->gender == 1 ? 'Him':'Her',
                $modifiedContentBody
            );

            $modifiedContentBody = str_replace(
                '{{beneficiary_gender_possessive_pronoun_lower_case}}',
                $beneficiary->gender == 1 ? 'him':'her',
                $modifiedContentBody
            );

            if(count($inputs)):
                foreach($inputs as $input):

                $modifiedContentBody = str_replace(
                    '{{' . $input->key . '}}',
                    $input->value,
                    $modifiedContentBody
                );

                endforeach;
                
            endif;

            if(count($tables)):

                foreach($tables as $table):

                    $modifiedContentBody = str_replace(
                        '{{' . $table->key . '}}',
                        formDocumentTable($table),
                        $modifiedContentBody
                    );

                endforeach;

            endif;

        @endphp
        
        {!! $modifiedContentBody !!}
    </div>

    <div class="ap-content-details-photo">
        <table width="100%">
            <tr>
                <td width="50%">
                    @if(property_exists($options, 'with_applicant_photo') && $options->with_applicant_photo)

                    <div class="ap-content-photo">
                        @if($beneficiary->photo)
                            <img src="{{ env('CDN_URL', '') . '/storage/' . $beneficiary->photo}}">
                        @endif
                    </div>

                    @endif
                    
                    @if(property_exists($options, 'with_applicant_signature') && $options->with_applicant_signature)

                    <div class="ap-content-applicant-signature">
                        
                        <small>
                            APPLICANT SIGNATURE
                        </small>
                    </div>

                    @endif
                </td>

                <td width="50%"></td>
                
            </tr>
        </table>
    </div>

    <div class="ap-content-details-signature">
        <table width="100%">
            <tr>
                <td width="50%">
                    @if(property_exists($options, 'with_left_approval') && $options->with_left_approval)
                    <div>
                        <small>
                            {{$approvals->left_approval->label}}
                        </small>
                        
                        @if(property_exists($options, 'with_left_approval_signature') && $options->with_left_approval_signature && $document->left_signature)
                        <div class="ap-content-approval-with-signature">
                            <img src="{{ env('CDN_URL', '') . '/storage/' . $document->left_signature}}">
                            
                            <p>
                                {{$approvals->left_approval->name}}
                            </p>
                            <small>
                                {{$approvals->left_approval->position}}
                            </small>
                        </div>
                        @else
                        <div class="ap-content-approval-without-signature">
                            <p>
                                {{$approvals->left_approval->name}}
                            </p>
                            <small>
                                {{$approvals->left_approval->position}}
                            </small>
                        </div>
                        @endif

                    </div>
                    @endif

                    @if(property_exists($options, 'with_left_approval_substitute') && $options->with_left_approval_substitute)
                    <div class="ap-mtop-10">
                        <small>
                            {{$approvals->left_approval_substitute->label}}
                        </small>
                        
                        @if(property_exists($options, 'with_left_approval_substitute_signature') && $options->with_left_approval_substitute_signature && $document->left_signature_substitute)
                        <div class="ap-content-approval-with-signature">
                            <img src="{{ env('CDN_URL', '') . '/storage/' . $document->left_signature_substitute}}">
                            
                            <p>
                                {{$approvals->left_approval_substitute->name}}
                            </p>
                            <small>
                                {{$approvals->left_approval_substitute->position}}
                            </small>
                        </div>
                        @else
                        <div class="ap-content-approval-without-signature">
                            <p>
                                {{$approvals->left_approval_substitute->name}}
                            </p>
                            <small>
                                {{$approvals->left_approval_substitute->position}}
                            </small>
                        </div>
                        @endif

                    </div>
                    @endif
                </td>
                <td width="50%">
                    @if(property_exists($options, 'with_right_approval') && $options->with_right_approval)
                    <div>
                        <small>
                            {{$approvals->right_approval->label}}
                        </small>
    
                        @if(property_exists($options, 'with_right_approval_signature') && $options->with_right_approval_signature && $document->right_signature)
                        <div class="ap-content-approval-with-signature">
                            <img src="{{ env('CDN_URL', '') . '/storage/' . $document->right_signature }}">
                            <p>
                                {{$approvals->right_approval->name}}
                            </p>
                            <small>
                                {{$approvals->right_approval->position}}
                            </small>
                        </div>
                        @else
                        <div class="ap-content-approval-without-signature">
                            <p>
                                {{$approvals->right_approval->name}}
                            </p>
                            <small>
                                {{$approvals->right_approval->position}}
                            </small>
                        </div>
                        @endif

                    </div>
                    @endif

                    @if(property_exists($options, 'with_right_approval_substitute') && $options->with_right_approval_substitute)
                    <div class="ap-mtop-10">
                        <small>
                            {{$approvals->right_approval_substitute->label}}
                        </small>
    
                        @if(property_exists($options, 'with_right_approval_substitute_signature') && $options->with_right_approval_substitute_signature && $document->right_signature)
                        <div class="ap-content-approval-with-signature">
                            <img src="{{ env('CDN_URL', '') . '/storage/' . $document->right_signature_substitute }}">
                            <p>
                                {{$approvals->right_approval_substitute->name}}
                            </p>
                            <small>
                                {{$approvals->right_approval_substitute->position}}
                            </small>
                        </div>
                        @else
                        <div class="ap-content-approval-without-signature">
                            <p>
                                {{$approvals->right_approval_substitute->name}}
                            </p>
                            <small>
                                {{$approvals->right_approval_substitute->position}}
                            </small>
                        </div>
                        @endif

                    </div>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <div class="ap-content-document">
        <table width="100%">
            <tr>
                <td width="50%">
                    @if(property_exists($options, 'with_qr_code') && $options->with_qr_code)
                    <div class="ap-seal">
                        <small>
                            SCAN TO VERIFY
                        </small>
                    </div>
                
                    <div class="ap-mtop-15">
                        @php
                            $url = env('VERIFICATION_URL') . '/documents/' . $document->id;
                            $base64encode = base64_encode(\QrCode::format('png')
                                                            // ->merge(asset('image/logo/vh-favicon.jpg'), 0.2, true)
                                                            ->size(150)
                                                            ->errorCorrection('H')
                                                            ->generate($url));
                        @endphp
                        <img src="data:image/png;base64, {!! $base64encode !!} " width="150" height="150" style="display: block; margin-left: auto; margin-right: auto;">
                    </div>
                    @endif
                </td>
                <td width="50%">
                    <table class="ap-content-table" width="100%">
                        @if(property_exists($options, 'with_document_no') && $options->with_document_no)
                        <tr>
                            <td width="40%">
                                <small class="ap-label">
                                    DOCUMENT NO:
                                </small>
                            </td>
                            <td width="60%">
                                <div class="ap-text">
                                    <small>
                                        {{$document->code}}
                                    </small>
                                </div>
                            </td>
                        </tr>
                        @endif

                        @if(property_exists($options, 'with_issuance_date') && $options->with_issuance_date)
                        <tr>
                            <td width="40%">
                                <small class="ap-label">
                                    DATE ISSUED:
                                </small>
                            </td>
                            <td width="60%">
                                <div class="ap-text">
                                    @if(property_exists($content, 'issuance_date') && $content->issuance_date)
                                        <small>
                                            {{strtoupper( (new \Carbon\Carbon($content->issuance_date))->format('F d, Y') )}}
                                        </small>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endif
            
                        @if(property_exists($options, 'with_expiration_date') && $options->with_expiration_date)
                        <tr>
                            <td width="40%">
                                <small class="ap-label">
                                    VALID UNTIL:
                                </small>
                            </td>
                            <td width="60%">
                                <div class="ap-text">
                                    @if(property_exists($content, 'expiration_date') && $content->expiration_date)
                                        <small>
                                            {{strtoupper( (new \Carbon\Carbon($content->expiration_date))->format('F d, Y') )}}
                                        </small>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endif
                    </table>
                </td>
            </tr>
        </table>
    </div>

</div>