<div class="ap-header">
    <table class="ap-header-table">
        <tbody>
            <tr>
                <td width="20%">
                    <div class="ap-header-logo">
                        <img src="{{ env('CDN_URL', '') . '/storage/' . $profile->barangay_logo }}" height="40" width="40">
                    </div>
                    
                </td>
                <td class="ap-header-table-text" width="60%">
                    <p class="ap-header-republic">REPUBLIC OF THE PHILIPPINES</p>
                    <p class="ap-header-city">{{strtoupper($profile->city_name)}}</p>
                    <p class="ap-header-barangay">BARANGAY {{strtoupper($profile->barangay_name)}}</p>
                </td>
                <td width="20%">
                    <div class="ap-header-logo">
                        <img src="{{ env('CDN_URL', '') . '/storage/' . $profile->city_logo }}" height="40" width="40">
                    </div>
                </td>
            </tr>
        </tbody>
    </table>

    <div class="ap-header-title"
    style="background: <?php echo property_exists($view, 'title_background_color') ? $view->title_background_color : ''?>">
       <p>
            {{$content->title}}
       </p>
    </div>
</div>


<div class="ap-content">

    <div class="ap-watermark">
        @if ($profile->barangay_logo)
            <img src="{{ env('CDN_URL', '') . '/storage/' . $profile->barangay_logo }}">
        @endif
    </div>

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
                                            {{ (new \Carbon\Carbon($constituent->date_of_birth))->format('M d, Y')}}
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
                                <td width="60%">
                                    <div class="ap-details">
                                        <small>
                                            PRECINCT NO.
                                        </small>
                                        <p>
                                            {{ $constituent->precinct_no ?: 'N/A' }}
                                        </p>
                                    </div>
                                </td>
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
                        {{ $constituent->code }}
                    </small>
                </div>
            </td>
        </tr>
    </table>
</div>

<div class="ap-household">

    <div class="ap-household-title"
        style="background: <?php echo property_exists($view, 'title_background_color') ? $view->title_background_color : ''?>">
        <p>
            RELATIVES
        </p>
    </div>

    @php
        $relatives = $constituent->relatives()->orderBy('order_no', 'asc')->get();
        $count = $relatives->count();


    @endphp

    <table>
        <tr>
            <td width="50%">
                <table class="ap-household-table ap-household-table-divider">
                    <tbody>
                        
                        @foreach($relatives as $key => $relative)
                            @php
                                $rowCount = $key + 1;
                            @endphp
                            @if ($rowCount <= 4)
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
            <td width="50%">
                @if ($count > 4)

                <table class="ap-household-table ap-household-table-divider">            
                    <tbody>
                        @foreach($relatives as $key => $relative)
                            @php
                                $rowCount = $key + 1;
                            @endphp
                            @if ($rowCount > 4)
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
            <td width="50%">
                <div class="ap-tagline">
                    <p class="ap-text">
                        "{{$profile->tagline}}"
                    </p>
                </div>
            </td>
            <td width="50%">
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
        </tr>
    </table>
</div>

<div class="ap-footer">

    <hr>

    <table width="100%">
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
                <img src="data:image/png;base64, {!! $base64encode !!} " width="60" height="60" style="display: block; margin-left: auto; margin-right: auto;">
            </div>
        </td>
        <td width="70%" style="margin-left: 10px;">
            <small>
                IN CASE OF LOSS, RETURN TO:
            </small>
    
            <div class="ap-details-first">
                <small style="font-family: ArialItalic">
                    {{$profile->address}}
                </small>
            </div>
            <div class="ap-details">
                <small style="font-family: ArialItalic">
                    Contact Info: {{$profile->contact_no}}
                </small>
            </div>
            <div class="ap-details-last">
                <small style="font-family: ArialItalic">
                    &copy; Barangay Comprehensive Management Platform (BCMP)
                </small>
            </div>
        </td>
    </table>

    
</div>