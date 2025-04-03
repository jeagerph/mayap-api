<div class="ap-content">

    <div class="ap-content-watermark">
        @if ($company->logo)
            <img src="{{ env('CDN_URL', '') . '/storage/' . $company->logo }}">
        @endif
    </div>

    <h3>
        IN CASE OF EMERGENCY
    </h3>
    
    <div class="ap-content-details">
        <div class="ap-other">
            <table width="100%">
                <tr>
                    <td width="70%">
                        <div class="ap-details">
                            <small>
                                FULL NAME
                            </small>
                            <p>
                                {{ $beneficiary->emergency_contact_name ?: 'N/A' }}
                            </p>
                        </div>
                    </td>
                    <td width="30%">
                        <div class="ap-details">
                            <small>
                                CONTACT NO
                            </small>
                            <p>
                                {{ $beneficiary->emergency_contact_no ?: 'N/A' }}
                            </p>
                        </div>
                    </td>
                </tr>
        
                <tr>
                    <td colspan="2" width="100%">
                        <div class="ap-details">
                            <small>
                                ADDRESS
                            </small>
                            <p>
                                {{ $beneficiary->emergency_contact_address ?: 'N/A' }}
                            </p>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        
    </div>

    <div class="ap-content-approvals">

        <table width="100%">
            <tr>
                <td width="50%">
                    @if(property_exists($options, 'with_left_approval') && $options->with_left_approval)
                    <div>
                        @if(property_exists($options, 'with_left_approval_signature') && $options->with_left_approval_signature && $identification->left_signature)
                        <div class="ap-approval-with-signature">
                            <img src="{{ env('CDN_URL', '') . '/storage/' . $identification->left_signature }}">
                            <p>
                                {{$approvals->left_approval->name}}
                            </p>
                            <small>
                                {{$approvals->left_approval->position}}
                            </small>
                        </div>
                        @else
                        <div class="ap-approval-without-signature">
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
                </td>
                <td width="50%">
                    @if(property_exists($options, 'with_right_approval') && $options->with_right_approval)
                    <div>
                        @if(property_exists($options, 'with_right_approval_signature') && $options->with_right_approval_signature && $identification->right_signature)
                        <div class="ap-approval-with-signature">
                            <img src="{{ env('CDN_URL', '') . '/storage/' . $identification->right_signature }}">
                            <p>
                                {{$approvals->right_approval->name}}
                            </p>
                            <small>
                                {{$approvals->right_approval->position}}
                            </small>
                        </div>
                        @else
                        <div class="ap-approval-without-signature">
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
                </td>
            </tr>
        </table>
    </div>

    <div class="ap-content-footer">

        <table width="100%">
            <td width="30%">
                <div>
                    @php
                        $url = env('VERIFICATION_URL') . '/identifications/' .  $beneficiary->code;
                        $base64encode = base64_encode(\QrCode::format('png')
                                                        // ->merge(asset('image/logo/vh-favicon.jpg'), 0.2, true)
                                                        ->size(70)
                                                        ->errorCorrection('H')
                                                        ->generate($url));
                    @endphp
                    <img src="data:image/png;base64, {!! $base64encode !!} " width="70" height="70" style="display: block; margin-left: auto; margin-right: auto;">
                </div>
            </td>
            <td width="70%" style="margin-left: 10px;">
                <small>
                    IN CASE OF LOSS, RETURN TO:
                </small>
        
                <div class="ap-details-first">
                    <small style="font-family: ArialItalic">
                        {{$idSetting->address}}
                    </small>
                </div>
                <div class="ap-details">
                    <small style="font-family: ArialItalic">
                        Contact Info: {{$idSetting->contact_no}}
                    </small>
                </div>
                <div class="ap-details-last">
                    <small style="font-family: ArialItalic">
                        &copy; PrimeX Enterprises Philippines, Inc.
                    </small>
                </div>
            </td>
        </table>

        
    </div>
</div>