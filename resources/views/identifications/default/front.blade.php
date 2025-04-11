<div class="ap-content">
    <div class="ap-content-details" style="display: flex; align-items: center; justify-content: space-between;">
        <div style="padding-top: 500px; margin-left: 45px; margin-bottom: 10px; text-align: center;">
            @php
                $url =
                    env('VERIFICATION_URL') .
                    '/identifications/' .
                    (is_object($beneficiary) && isset($beneficiary->code)
                        ? $beneficiary->code
                        : (isset($beneficiary['code'])
                            ? $beneficiary['code']
                            : ''));
                $base64encode = base64_encode(\QrCode::format('png')->size(1300)->errorCorrection('H')->generate($url));
            @endphp
            <img src="data:image/png;base64, {!! $base64encode !!}" width="300" height="300" />
        </div>

        <div class="ap-name"
            style="text-align: right; flex: 1; margin: 0 15px; display: flex; flex-direction: column; justify-content: center; height: 350px; min-width: 800px;">
            <p style="font-size: 56px; font-weight: bold; margin: 0; line-height: 1;">
                {{ is_object($beneficiary) ? $beneficiary->fullName() : (isset($beneficiary['full_name']) ? $beneficiary['full_name'] : '') }}
            </p>
            <p style="font-size: 42px; font-weight: semi-bold; margin: 5px 0;">
                {{ is_object($beneficiary) && isset($beneficiary->date_of_birth) ? date('F d, Y', strtotime($beneficiary->date_of_birth)) : (isset($beneficiary['date_of_birth']) ? date('F d, Y', strtotime($beneficiary['date_of_birth'])) : '') }}
            </p>
            <p style="font-size: 42px; font-weight: semi-bold; margin: 5px 0;">
                MEMBER
            </p>
        </div>

        <div class="ap-content-photo"
            style="text-align: center; margin-right: 10px; padding-top: 260px; margin-right: 40px;">
            @if (is_object($beneficiary) && isset($beneficiary->photo))
                <img style="height: 350px; width: 300px; border: 2px solid #999999; border-radius: 8px;"
                    src="{{ env('CDN_URL', '') . '/storage/' . (is_object($beneficiary) && isset($beneficiary->photo) ? $beneficiary->photo : (isset($beneficiary['photo']) ? $beneficiary['photo'] : '')) }}">
            @else
                <img style="height: 350px; width: 300px; border: 2px solid #999999; border-radius: 8px;"
                    src="{{ is_object($beneficiary) && isset($beneficiary->photo) ? $beneficiary->photo : (isset($beneficiary['photo']) ? $beneficiary['photo'] : '') }}">
            @endif
            <p style="color: white; font-size: 30px; font-weight: bold; margin-top: 100px;">
                {{-- {{ $beneficiary->code ? $beneficiary->code .' '. $precinct_no : $beneficiary['code'] .' '. $precinct_no }} --}}
                {{ is_object($beneficiary) && isset($beneficiary->code) ? $beneficiary->code . ' ' . $precinct_no : (isset($beneficiary['code']) ? $beneficiary['code'] . ' ' . $precinct_no : '') }}
            </p>
        </div>
    </div>
</div>
