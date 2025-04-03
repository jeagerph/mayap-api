<div class="ap-header">
    <table class="ap-header-table">
        <tbody>
            <tr>
                <td width="20%">
                    <div class="ap-header-logo">
                        <img src="{{ env('CDN_URL', '') . '/storage/' . $profile->barangay_logo }}" height="120" width="120">
                    </div>
                    
                </td>
                <td width="80%" style>
                    <p class="ap-header-barangay">BARANGAY NEW KABABAE</p>
                    <p class="ap-header-title">FAMILY CARD</p>
                    <p class="ap-header-tagline">
                        "SA BARANGAY NAGSISIMULA ANG PAGUNLAD!"
                    </p>
                </td>
            </tr>
        </tbody>
    </table>
</div>


<div class="ap-content">

    <table class="ap-content-table">
        <tr>
            <td width="70%">

                <div class="ap-content-details">
                    <div class="ap-name">
    
                        <p>
                            {{ $constituent->fullName()}}
                        </p>
                    </div>
    
                    <div class="ap-other">
                        <table width="100%">
                            <tr>
                                <td width="40%">
                                    <div class="ap-details">
                                        <small>
                                            DATE OF BIRTH
                                        </small>
                                        <p>
                                            {{ strtoupper((new \Carbon\Carbon($constituent->date_of_birth))->format('M d, Y'))}}
                                        </p>
                                    </div>
                                    
                                </td>
                                <td width="40%">
                                    <div class="ap-details">
                                        <small>
                                            CIVIL STATUS
                                        </small>
                                        <p>
                                            {{ $constituent->civil_status ? $constituent->civilStatuses[$constituent->civil_status] : 'N/A' }}
                                        </p>
                                    </div>
                                    
                                </td>
                                <td width="20%">
                                    <div class="ap-details">
                                        <small>
                                            GENDER
                                        </small>
                                        <p>
                                            {{ $constituent->genders[$constituent->gender]}}
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
                                            {{ $constituent->address }}
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        </table>

                        <table width="100%">
                            <tr>
                                <td width="40%">
                                    @if(property_exists($options, 'with_expiration_date') && $options->with_expiration_date)
                                    <div class="ap-details">
                                        <small>
                                            VALID UNTIL
                                        </small>
                                        @php

                                        if($options->expiration_date->default === 'months'):
                                            $expirationDate = now()->addMonths($options->expiration_date->months)->format('M d, Y');
                                        elseif($options->expiration_date->default === 'specific'):
                                            $expirationDate = (new \Carbon\Carbon($options->expiration_date->specific))->format('M d, Y');
                                        else:
                                            $expirationDate = now()->addMonths(12)->format('M d, Y');
                                        endif;

                                        @endphp
                                        <p>
                                            {{strtoupper( $expirationDate )}}
                                        </p>
                                    </div>
                                    @endif
                                </td>
                                <td width="60%">
                                    <div class="ap-details">
                                        <small>
                                            RESIDENT NO.
                                        </small>
                                        <p>
                                            {{ $constituent->code }}
                                        </p>
                                    </div>
                                    
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </td>

            <td width="30%">

                <div class="ap-content-photo">
                    <img src="{{ env('CDN_URL', '') . '/storage/' . $constituent->photo}}">
                </div>

                <div class="ap-content-signature">
                    <div class="ap-line"></div>
                    
                    <small>
                        SIGNATURE
                    </small>
                </div>
            </td>
        </tr>
    </table>
</div>

<div class="ap-household">

    <div class="ap-household-title">
        <p>
            RELATIVES
        </p>
    </div>

    @php
        $relatives = $constituent->relatives()->orderBy('order_no', 'asc')->get();
        $count = $relatives->count();


    @endphp

    <table width="100%">
        <tr>
            <td width="50%" style="vertical-align: top;">
                <table class="ap-household-table ap-household-table-divider">
                    <tbody>
                        
                        @foreach($relatives as $key => $relative)
                            @php
                                $rowCount = $key + 1;
                            @endphp
                            @if ($rowCount <= 3)
                            <tr>
                                <td width="20%">
                                    <div class="ap-photo">
                                        <img src="{{ env('CDN_URL', '') . '/storage/' . $relative->relative->photo}}">
                                    </div>
                                    
                                </td>
                                <td width="80%">
                                    <p class="ap-details">
                                        {{ $relative->relative->fullName('L, F MI')}} 
                                    </p>
                                    <p class="ap-details-small">
                                        {{ $relative->relative->genders[$relative->relative->gender][0]}}
                                        |
                                        {{ strtoupper((new \Carbon\Carbon($relative->relative->date_of_birth))->format('M d, Y'))}}
                                        |
                                        {{ $relative->relationship}}
                                    </p>
                                </td>
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </td>
            <td width="50%" style="vertical-align: top;">
                @if ($count > 3)

                <table class="ap-household-table ap-household-table-divider">            
                    <tbody>
                        @foreach($relatives as $key => $relative)
                            @php
                                $rowCount = $key + 1;
                            @endphp
                            @if ($rowCount > 3)
                            <tr>
                                <td width="20%">
                                    <div class="ap-photo">
                                        <img src="{{ env('CDN_URL', '') . '/storage/' . $relative->relative->photo}}">
                                    </div>
                                    
                                </td>
                                <td width="80%">
                                    <p class="ap-details">
                                        {{ $relative->relative->fullName('L, F MI')}} 
                                    </p>
                                    <p class="ap-details-small">
                                        {{ $relative->relative->genders[$relative->relative->gender][0]}}
                                        |
                                        {{ strtoupper((new \Carbon\Carbon($relative->relative->date_of_birth))->format('M d, Y'))}}
                                        |
                                        {{ $relative->relationship}}
                                    </p>
                                </td>
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>

                @endif
            </td>
        </tr>
    </table>
</div>

<div class="ap-approval">
    <table width="100%">
        <tr>
            <td width="10%"></td>
            <td width="80%">
                @if(property_exists($options, 'with_right_approval') && $options->with_right_approval)
                <div>
                    @if(property_exists($options, 'with_right_approval_signature') && $options->with_right_approval_signature && $identification->right_signature)
                    <div class="ap-with-signature">
                        <img src="{{ env('CDN_URL', '') . '/storage/' . $identification->right_signature }}">
                        <p>
                            {{$approvals->right_approval->name}}
                        </p>
                        <small>
                            {{$approvals->right_approval->position}}
                        </small>
                    </div>
                    @else
                    <div class="ap-without-signature">
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
            <td width="10%"></td>
        </tr>
    </table>
</div>

<div class="ap-footer">

    <hr>

    <table width="100%">
        <tr>
            <td width="70%">
                <table width="100%">
                    <tr>
                        <td width="40%">
                            <div class="ap-emergency">
                                <p class="ap-title">
                                    EMERGENCY HOTLINES:
                                </p>
                
                                <div>
                                    <p class="ap-text">
                                        0967 192 4651 / 0962 416 7631
                                    </p>
                                    <p class="ap-text">
                                        232 6626
                                    </p>
                                    
                                </div>
                                
                            </div>
                        </td>
                        <td width="60%">
                            <div class="ap-found">
                                <p class="ap-title">
                                    IF FOUND, PLEASE RETURN TO:
                                </p>
                                <div>
                                    <p class="ap-text">
                                        MULTIPURPOSE HALL, FOSTER ST., NEW KABABAE, OLONGAPO CITY
                                    </p>
                                </div>
                            </div>
                        </td>
                    </tr>
                    
                    <tr>
                        <td colspan="2">
                            <p class="ap-text-bcmp">
                                &copy; Barangay Comprehensive Management Platform (BCMP)
                            </p>
                        </td>
                    </tr>
                    
                </table>
            </td>
            <td width="30%">
                <div>
                    @php
                        $url = env('BARANGAY_ONLINE_URL') . '/identifications/' . $identification->slug->code;
                        $base64encode = base64_encode(\QrCode::format('png')
                                                        // ->merge(asset('image/logo/vh-favicon.jpg'), 0.2, true)
                                                        ->size(60)
                                                        ->errorCorrection('H')
                                                        ->generate($url));
                    @endphp
                    <img src="data:image/png;base64, {!! $base64encode !!} " width="60" height="60" style="display: block; margin-left: auto; margin-right: auto; margin-top: -10px;">
                </div>
            </td>
        </tr>
    </table>

    

    
</div>