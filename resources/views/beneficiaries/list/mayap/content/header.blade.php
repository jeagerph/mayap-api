@php
$filters = $request->get('filter');
$queries = '';

if ($filters):
    if (array_key_exists('gender', $filters)):

        $queryGender = $filters['gender'];
        $queryGenderLabel = 'GENDER: ';
        
        if ($queryGender):
            $beneficiaryModel = new \App\Models\Beneficiary;
            
            $queryGenderLabel .= $beneficiaryModel->genderOptions[$queryGender];

            $queries .= $queryGenderLabel . ' | ';

        endif;
        
    endif;

    if (array_key_exists('isOfficer', $filters)):

        $queryOfficer = $filters['isOfficer'];
        $queryOfficerLabel = 'OFFICER: ';
        
        if ($queryOfficer):

            $queryOfficerLabel .= $queryOfficer ? 'YES':'NO';

            $queries .= $queryOfficerLabel . ' | ';

        endif;

    endif;

    if (array_key_exists('voterType', $filters)):

        $queryVoterType = $filters['voterType'];
        $queryVoterTypeLabel = 'VOTER TYPE: ';
        
        if ($queryVoterType):
            $beneficiaryModel = new \App\Models\Beneficiary;

            $queryVoterTypeLabel .= $beneficiaryModel->voterTypeOptions[$queryVoterType]['name'];

            $queries .= $queryVoterTypeLabel . ' | ';

        endif;

    endif;

    if (array_key_exists('isHousehold', $filters)):

        $queryHousehold = $filters['isHousehold'];
        $queryHouseholdLabel = 'HEAD OF HOUSEHOLD: ';
        
        if ($queryHousehold):

            $queryHouseholdLabel .= $queryHousehold ? 'YES':'NO';

            $queries .= $queryHouseholdLabel . ' | ';

        endif;

    endif;

    if (array_key_exists('provCode', $filters)):

        $queryProvince = $filters['provCode'];
        $queryProvinceLabel = 'PROVINCE: ';
        
        if ($queryProvince):

            $provinceModel = \App\Models\Province::where('prov_code', $queryProvince)->first();

            $queryProvinceLabel .= $provinceModel->name;

            $queries .= $queryProvinceLabel . ' | ';

        endif;

    endif;

    if (array_key_exists('cityCode', $filters)):

        $queryCity = $filters['cityCode'];
        $queryCityLabel = 'CITY/MUNICIPALITY: ';
        
        if ($queryCity):

            $cityModel = \App\Models\City::where('city_code', $queryCity)->first();

            $queryCityLabel .= $cityModel->name;

            $queries .= $queryCityLabel . ' | ';

        endif;

    endif;

    if (array_key_exists('barangay', $filters)):

        $queryBarangay = $filters['barangay'];
        $queryBarangayLabel = 'BARANGAY: ';
        
        if ($queryBarangay):

            $barangayModel = \App\Models\Barangay::where('id', $queryBarangay)->first();

            $queryBarangayLabel .= $barangayModel->name;

            $queries .= $queryBarangayLabel . ' | ';

        endif;

    endif;

    if (array_key_exists('purok', $filters)):

        $queryPurok = $filters['purok'];
        $queryPurokLabel = 'PUROK: ';
        
        if ($queryPurok):

            $queryPurokLabel .= $queryPurok;

            $queries .= $queryPurokLabel . ' | ';

        endif;

    endif;

    if (array_key_exists('street', $filters)):

        $queryStreet = $filters['street'];
        $queryStreetLabel = 'STREET: ';
        
        if ($queryStreet):

            $queryStreetLabel .= $queryStreet;

            $queries .= $queryStreetLabel . ' | ';

        endif;

    endif;

    if (array_key_exists('zone', $filters)):

        $queryZone = $filters['zone'];
        $queryZoneLabel = 'ZONE: ';
        
        if ($queryZone):

            $queryZoneLabel .= $queryZone;

            $queries .= $queryZoneLabel . ' | ';

        endif;

    endif;

    if (array_key_exists('age', $filters)):

        $queryAge = $filters['age'];
        $queryAgeLabel = 'AGE: ';
        
        if ($queryAge):

            $queryAgeLabel .= $queryAge;

            $queries .= $queryAgeLabel . ' | ';

        endif;

    endif;
endif;
@endphp
<div class="ap-header">

    <img class="ap-header-image" src="{{asset('assets/images/mayap/logo.png')}}" alt="LOGO" />

    <h2 class="ap-header-title">
        BENEFICIARIES REPORT
    </h2>

    <p class="ap-header-subtitle ap-text-italic">
        (Data downloaded as of {{now()->format('F d, Y H:iA')}})
    </p>

    <h3 class="ap-header-dates">
        DATE PERIOD:
        @if($request->get('from') == $request->get('to'))
            <span>{{(new \Carbon\Carbon($request->get('from')))->format('F d, Y')}}</span>
        @else
            <span>{{(new \Carbon\Carbon($request->get('from')))->format('F d, Y')}}</span> ~ <span>{{(new \Carbon\Carbon($request->get('to')))->format('F d, Y')}}</span>
        @endif
    </h3>

    <h3 class="ap-header-dates">
        {{ $queries }}
    </h3>

    <h3 class="ap-header-dates">
        TOTAL: {{ $beneficiaries->count() }}
    </h3>
    
</div>