<div class="ap-content">

    <table class="ap-content-table">
        <tr>
            <td width="70%">

                <div class="ap-content-details">
                    <div class="ap-name">
    
                        <p>
                            {{ $member->fullName()}}
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
                                            {{ (new \Carbon\Carbon($member->date_of_birth))->format('M d, Y')}}
                                        </p>
                                    </div>
                                    
                                </td>
                                <td width="40%">
                                    <div class="ap-details">
                                        <small>
                                            CIVIL STATUS
                                        </small>
                                        <p>
                                            {{ $member->civil_status ? $member->civilStatuses[$member->civil_status] : 'N/A' }}
                                        </p>
                                    </div>
                                    
                                </td>
                                <td width="20%">
                                    <div class="ap-details">
                                        <small>
                                            GENDER
                                        </small>
                                        <p>
                                            {{ $member->genders[$member->gender]}}
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
                                            {{ $member->address }}
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
                                            {{ $member->precinct_no ?: 'N/A' }}
                                        </p>
                                    </div>
                                </td>
                                <td width="40%">
                                    <div class="ap-details">
                                        <small>
                                            VALID UNTIL
                                        </small>
                                        @php

                                            $expirationDate = now()->addMonths(12)->format('M d, Y');

                                        @endphp
                                        <p>
                                            {{strtoupper( $expirationDate )}}
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
                    <img src="{{ env('CDN_URL', '') . '/storage/' . $member->photo}}">
                </div>

                <div class="ap-content-signature">
                    <div class="ap-line"></div>
                    
                    <small>
                        {{ $member->code }}
                    </small>
                </div>
            </td>
        </tr>
    </table>
</div>