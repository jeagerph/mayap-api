<div class="ap-content">
    <div class="ap-content-details" style="display: flex; align-items: center; justify-content: space-between;">
        <!-- QR Code on the Left -->
        <div style="padding-top: 500px; margin-left: 45px; margin-bottom: 10px; text-align: center;">
            @php
                $url = env('VERIFICATION_URL') . '/identifications/' .  $beneficiary->code;
                $base64encode = base64_encode(\QrCode::format('png')
                    ->size(1300) 
                    ->errorCorrection('H')
                    ->generate($url));
            @endphp
            <img src="data:image/png;base64, {!! $base64encode !!}" width="300" height="300" />
        </div>

        <!-- Name and Title in the Center -->
       
        <div class="ap-name" style="text-align: right; flex: 1; margin: 0 15px; display: flex; flex-direction: column; justify-content: center; height: 350px; min-width: 800px;">
            <p style="font-size: 56px; font-weight: bold; margin: 0; line-height: 1.;">
                {{ $beneficiary->fullName() }}
            </p>
            <p style="font-size: 42px; font-weight: semi-bold; margin: 5px 0;">
                {{ date('F d, Y', strtotime($beneficiary->date_of_birth)) }}
            </p>
            <p style="font-size: 42px; font-weight: semi-bold; margin: 5px 0;">
                MEMBER
            </p>
        </div>

        <!-- Photo on the Right -->
        <div class="ap-content-photo" style="text-align: center; margin-right: 10px; padding-top: 260px; margin-right: 40px;">
            <img style="height: 350px; width: 300px; border: 2px solid #999999; border-radius: 8px;" 
                src="{{ env('CDN_URL', '') . '/storage/' . $beneficiary->photo }}">
            <p style="color: white; font-size: 26px; font-weight: bold; text-align: center; margin-top: 100px;">
                {{ $beneficiary->code }}
            </p>
        </div>
    </div>
</div>
