<div class="ap-sidebar-officials"
    style="background: <?php echo property_exists($view, 'sidebar_background_color') ? $view->sidebar_background_color : ''?>">
    <p class="ap-sidebar-title">
        BARANGAY OFFICIALS
    </p>
    <p class="ap-sidebar-subtitle">
        {{$barangay->start_term}} - {{$barangay->end_term}}
    </p>

    @php
        $officials = $barangay->officials()->where('registered_domain', 1)->orderBy('order_no', 'asc')->get();
    @endphp

    <div class="ap-sidebar-list">
        @foreach($officials as $key => $official)

            @if($official->show_certificate)

                @if(!$key)

                    @if($official->show_certificate_photo)
                        <div class="ap-list-image">
                            <img src="{{ env('CDN_URL', '') . '/storage/' . $official->photo }}" alt="">
                        </div>
                    @endif

                    <div class="ap-list-item">
                        <p class="ap-list-item-title item-first">
                            {{strtoupper($official->prefix)}} {{strtoupper($official->name)}}
                        </p>
                        @if($official->show_certificate_position)
                            <small class="ap-list-item-subtitle">
                                {{$official->positionOptions[$official->position_id]}}
                            </small>
                        @endif
                    </div>

                @else

                    <div class="ap-list-item">
                        <p class="ap-list-item-title">
                            {{strtoupper($official->prefix)}} {{strtoupper($official->name)}}
                        </p>
                        @if($official->show_certificate_position)
                            <small class="ap-list-item-subtitle">
                                {{$official->positionOptions[$official->position_id]}}
                            </small>
                        @endif

                        @if($official->show_certificate_committees)
                            @php
                                $committees = $official->committees
                                    ? explode('~', $official->committees)
                                    : [];
                            @endphp
                            @foreach($committees as $committee)

                            <div class="ap-list-item-label">
                                {{$committee}}
                            </div>
                            
                            @endforeach
                        @endif
                        
                    </div>

                @endif

            @endif
        @endforeach
        
    </div>
</div>

<div class="ap-sidebar-document">
    <div class="ap-mtop-15">
        <table class="ap-document-table" width="100%">
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
    
    </div>

    @if(property_exists($options, 'with_qr_code') && $options->with_qr_code)
    <div class="ap-seal">
        <small>
            SCAN TO VERIFY
        </small>
    </div>

    <div class="ap-mtop-15">
        @php
            $url = env('BARANGAY_ONLINE_URL') . '/documents/' . $document->slug->code;
            $base64encode = base64_encode(\QrCode::format('png')
                                            // ->merge(asset('image/logo/vh-favicon.jpg'), 0.2, true)
                                            ->size(150)
                                            ->errorCorrection('H')
                                            ->generate($url));
        @endphp
        <img src="data:image/png;base64, {!! $base64encode !!} " width="150" height="150" style="display: block; margin-left: auto; margin-right: auto;">
    </div>
    @endif
</div>
