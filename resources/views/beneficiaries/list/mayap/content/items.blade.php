<div class="ap-items-details ap-mtop-20">

    <table width="100%" class="ap-items-table">
        <thead style="background: #797979; color: #FFF;">
            <tr>
                <th width="20%">
                    <small class="ap-text-bold">
                        DATE REGISTERED
                    </small>
                </th>

                <th width="20%">
                    <small class="ap-text-bold">
                        PROVINCE
                    </small>
                </th>

                <th width="20%">
                    <small class="ap-text-bold">
                        CITY/ MUNICIPALITY
                    </small>
                </th>

                <th width="20%">
                    <small class="ap-text-bold">
                        BARANGAY
                    </small>
                </th>

                <th width="20%">
                    <small class="ap-text-bold">
                        PUROK/ SITIO
                    </small>
                </th>

                <th width="20%">
                    <small class="ap-text-bold">
                        FULL NAME
                    </small>
                </th>

                <th width="20%">
                    <small class="ap-text-bold">
                        GENDER
                    </small>
                </th>

                <th width="20%">
                    <small class="ap-text-bold">
                        BIRTH DATE
                    </small>
                </th>

                <th width="20%">
                    <small class="ap-text-bold">
                        AGE
                    </small>
                </th>

                <th width="20%">
                    <small class="ap-text-bold">
                        MOBILE NO
                    </small>
                </th>

                <th width="20%">
                    <small class="ap-text-bold">
                        CIVIL STATUS
                    </small>
                </th>

                <th width="20%">
                    <small class="ap-text-bold">
                        ADDRESS
                    </small>
                </th>

                <th width="20%">
                    <small class="ap-text-bold">
                        BIRTH PLACE
                    </small>
                </th>

                <th width="20%">
                    <small class="ap-text-bold">
                        EDUC. ATTAINMENT
                    </small>
                </th>

                <th width="20%">
                    <small class="ap-text-bold">
                        CLASSIFICATION
                    </small>
                </th>

                <th width="20%">
                    <small class="ap-text-bold">
                        MONTHLY INCOME
                    </small>
                </th>

                <th width="20%">
                    <small class="ap-text-bold">
                        SOURCE INCOME
                    </small>
                </th>

                <th width="20%">
                    <small class="ap-text-bold">
                        OCCUPATION
                    </small>
                </th>

                <th width="20%">
                    <small class="ap-text-bold">
                        OFFICER/ LEADER
                    </small>
                </th>

                <th width="20%">
                    <small class="ap-text-bold">
                        HEAD OF HOUSEHOLD
                    </small>
                </th>

                <th width="20%">
                    <small class="ap-text-bold">
                        HOUSEHOLD MEMBER
                    </small>
                </th>

                <th width="20%">
                    <small class="ap-text-bold">
                        HOUSEHOLD VOTERS
                    </small>
                </th>

                <th width="20%">
                    <small class="ap-text-bold">
                        VOTER TYPE
                    </small>
                </th>

                <th width="20%">
                    <small class="ap-text-bold">
                        NO. OF ASSISTANCE
                    </small>
                </th>

                <th width="20%">
                    <small class="ap-text-bold">
                        ENCODER
                    </small>
                </th>

                
            </tr>
        </thead>

        <tbody>

            @foreach($beneficiaries as $beneficiary)

            <tr class="ap-row-divider">
                <td width="20%">
                    <p class="ap-text">
                        {{ $beneficiary->date_registered }}
                    </p>
                </td>
                
                <td width="20%">
                    <p class="ap-text">
                        {{ $beneficiary->province->name }}
                    </p>
                </td>

                <td width="20%">
                    <p class="ap-text">
                        {{ $beneficiary->city->name }}
                    </p>
                </td>

                <td width="20%">
                    <p class="ap-text">
                        {{ $beneficiary->barangay->name }}
                    </p>
                </td>

                <td width="20%">
                    <p class="ap-text">
                        {{ $beneficiary->purok ?: 'NOT INDICATED' }}
                    </p>
                </td>

                <td width="20%">
                    <p class="ap-text">
                        {{ $beneficiary->fullName() }}
                    </p>
                </td>

                <td width="20%">
                    <p class="ap-text">
                        {{ $beneficiary->genderOptions[$beneficiary->gender] }}
                    </p>
                </td>
                
                <td width="20%">
                    <p class="ap-text">
                        {{ (new \Carbon\Carbon($beneficiary->date_of_birth))->format('F d, Y') }}
                    </p>
                </td>
                
                <td width="20%">
                    <p class="ap-text">
                        {{ \Carbon\Carbon::parse($beneficiary->date_of_birth)->age }}
                    </p>
                </td>
                
                <td width="20%">
                    <p class="ap-text">
                        {{ $beneficiary->mobile_no }}
                    </p>
                </td>
                
                <td width="20%">
                    <p class="ap-text">
                        {{ $beneficiary->civil_status }}
                    </p>
                </td>
                
                <td width="20%">
                    <p class="ap-text">
                        {{ $beneficiary->address }}
                    </p>
                </td>
                
                <td width="20%">
                    <p class="ap-text">
                        {{ $beneficiary->place_of_birth }}
                    </p>
                </td>
                
                <td width="20%">
                    <p class="ap-text">
                        {{ $beneficiary->educational_attainment }}
                    </p>
                </td>
                
                <td width="20%">
                    <p class="ap-text">
                        {{ $beneficiary->classification ?: 'N/a' }}
                    </p>
                </td>
                
                <td width="20%">
                    <p class="ap-text">
                        {{ $beneficiary->monthly_income }}
                    </p>
                </td>
                
                <td width="20%">
                    <p class="ap-text">
                        {{ $beneficiary->source_of_income }}
                    </p>
                </td>
                
                <td width="20%">
                    <p class="ap-text">
                        {{ $beneficiary->occupation }}
                    </p>
                </td>
                
                <td width="20%">
                    <p class="ap-text">
                        {{ $beneficiary->is_officer ? 'YES': 'NO' }}
                    </p>
                </td>
                
                <td width="20%">
                    <p class="ap-text">
                        {{ $beneficiary->is_household ? 'YES': 'NO' }}
                    </p>
                </td>
                
                <td width="20%">
                    <p class="ap-text">
                        {{ $beneficiary->household_count }}
                    </p>
                </td>
                
                <td width="20%">
                    <p class="ap-text">
                        {{ $beneficiary->household_voters_count }}
                    </p>
                </td>
                
                <td width="20%">
                    <p class="ap-text">
                        {{ $beneficiary->voterTypeOptions[$beneficiary->voter_type]['name'] }}
                    </p>
                </td>
                
                <td width="20%">
                    <p class="ap-text">
                        {{ $beneficiary->assistances_count }}
                    </p>
                </td>
                
                <td width="20%">
                    <p class="ap-text">
                        {{ ($beneficiary->creator())['full_name'] }}
                    </p>
                </td>
                
                
            </tr>


            @endforeach

        </tbody>
    </table>

</div>