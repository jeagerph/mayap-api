<div class="ap-content">

    <table class="ap-content-table">
        <tr>
            <td width="70%">

                <div class="ap-content-details">
                    <div class="ap-name">
    
                        <p>
                            {{ $beneficiary->fullName()}}
                        </p>
                    </div>
    
                    <div class="ap-other">
                        <table width="100%">
                            <tr>
                                <td width="30%">
                                    <div class="ap-details">
                                        <small>
                                            DATE OF BIRTH
                                        </small>
                                        <p>
                                            {{ (new \Carbon\Carbon($beneficiary->date_of_birth))->format('M d, Y')}}
                                        </p>
                                    </div>
                                    
                                </td>
                                <td width="40%">
                                    <div class="ap-details">
                                        <small>
                                            CIVIL STATUS
                                        </small>
                                        <p>
                                            {{ $beneficiary->civil_status ?: 'N/A' }}
                                        </p>
                                    </div>
                                    
                                </td>
                                <td width="30%">
                                    <div class="ap-details">
                                        <small>
                                            GENDER
                                        </small>
                                        <p>
                                            {{ $beneficiary->genderOptions[$beneficiary->gender]}}
                                        </p>
                                    </div>
                                    
                                </td>
                            </tr>
    
                            <tr>
                                <td width="50%">
                                    <div class="ap-details">
                                        <small>
                                            ADDRESS
                                        </small>
                                        <p>
                                            {{ $beneficiary->address }}
                                        </p>
                                    </div>
                                </td>
                                <td width="50%">
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
                    <img src="{{ env('CDN_URL', '') . '/storage/' . $beneficiary->photo}}">
                </div>

                <div class="ap-content-signature">
                    <div class="ap-line"></div>
                    
                    <small>
                        {{ $beneficiary->code }}
                    </small>
                </div>
            </td>
        </tr>
    </table>
</div>