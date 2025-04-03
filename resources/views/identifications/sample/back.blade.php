<div class="ap-content">

    <div class="ap-content-watermark">
        {{-- @if ($company->logo)
            <img src="{{ env('CDN_URL', '') . '/storage/' . $company->logo }}">
        @endif --}}
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
                                {{ $member->emergency_contact_name ?: 'N/A' }}
                            </p>
                        </div>
                    </td>
                    <td width="30%">
                        <div class="ap-details">
                            <small>
                                CONTACT NO
                            </small>
                            <p>
                                {{ $member->emergency_contact_no ?: 'N/A' }}
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
                                {{ $member->emergency_contact_address ?: 'N/A' }}
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
                    <div>
                        <div class="ap-approval-without-signature">
                            <p>
                                MS. JUANA SANTIAGO
                            </p>
                            <small>
                                PREPARED BY
                            </small>
                        </div>

                    </div>
                </td>
                <td width="50%">
                    <div>
                        <div class="ap-approval-without-signature">
                            <p>
                                HON. JUAN DELA CRUZ
                            </p>
                            <small>
                                GENERAL MANAGER
                            </small>
                        </div>

                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="ap-content-footer">

        <table width="100%">
            <td width="30%">
                <div>
                    @php
                        $url = env('BARANGAY_ONLINE_URL') . '/identifications/' . $template;
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
                        {{$company->address}}
                    </small>
                </div>
                <div class="ap-details">
                    <small style="font-family: ArialItalic">
                        Contact Info: {{$company->contact_no}}
                    </small>
                </div>
                <div class="ap-details-last">
                    <small style="font-family: ArialItalic">
                        &copy; {{ $company->name}}
                    </small>
                </div>
            </td>
        </table>

        
    </div>
</div>