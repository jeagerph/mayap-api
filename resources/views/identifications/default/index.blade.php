<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>IDENTIFICATION OF {{ $identification->code }} | {{ env('APP_NAME') }}</title>

    <style>
        body,
        html {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            box-sizing: border-box;
        }

        .ap-paper {
            width: 100%;
            height: 100%;
            position: relative;
            box-sizing: border-box;
        }

        .ap-identification {
            width: 100%;
            height: 100vh;
            /* Full height of the viewport */
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 0;
            page-break-inside: avoid;
            /* Prevent breaking within the card */
        }

        .ap-identification-front,
        .ap-identification-back {
            width: 100%;
            height: 100%;
            background-position: center center;
            background-repeat: no-repeat;
            background-size: cover;
            /* Ensure the background image covers the full page */
        }

        .ap-identification-back {
            page-break-before: always;
            /* Force the back to appear on a new page */
            transform: rotate(180deg);
            /* Rotate the back ID upside down */
        }
    </style>
</head>

<body>
    <div class="ap-paper">
        @php
            $beneficiary = $identification->beneficiary;
            $company = $identification->company;
            $idSetting = $company->idSetting;
            $view = $identification->view ? json_decode($identification->view) : null;
            $options = $identification->options ? json_decode($identification->options) : null;
            $content = $identification->content ? json_decode($identification->content) : null;
            $approvals = $identification->approvals ? json_decode($identification->approvals) : null;
            $precinct_no = $voter_details ? $voter_details['precinct_no'] : null;
        @endphp

        <!-- Front ID -->
        <div class="ap-identification ap-identification-front"
            style="background-image: url('{{ asset('assets/images/mayap/front-id3.png') }}');">
            @if ($view->header)
                @include("identifications.{$view->header}", [
                    'company' => $company,
                    'view' => $view,
                    'content' => $content,
                    'idSetting' => $idSetting,
                ])
            @else
                @include('identifications.default.header', [
                    'company' => $company,
                    'view' => $view,
                    'content' => $content,
                    'idSetting' => $idSetting,
                ])
            @endif

            @if ($view->front)
                @include("identifications.{$view->front}", [
                    'beneficiary' => $beneficiary,
                    'identification' => $identification,
                    'content' => $content,
                    'options' => $options,
                    'approvals' => $approvals,
                    'idSetting' => $idSetting,
                    'precinct_no' => $precinct_no,
                ])
            @else
                @include('identifications.default.front', [
                    'beneficiary' => $beneficiary,
                    'identification' => $identification,
                    'content' => $content,
                    'options' => $options,
                    'approvals' => $approvals,
                    'idSetting' => $idSetting,
                    'precinct_no' => $precinct_no,
                ])
            @endif
        </div>

        <!-- Back ID -->
        <div class="ap-identification ap-identification-back"
            style="background-image: url('{{ asset('assets/images/mayap/back-id1.png') }}');">
            @if ($view->back)
                @include("identifications.{$view->back}", [
                    'company' => $company,
                    'beneficiary' => $beneficiary,
                    'identification' => $identification,
                    'approvals' => $approvals,
                    'options' => $options,
                    'idSetting' => $idSetting,
                ])
            @else
                @include('identifications.default.back', [
                    'company' => $company,
                    'beneficiary' => $beneficiary,
                    'identification' => $identification,
                    'approvals' => $approvals,
                    'options' => $options,
                    'idSetting' => $idSetting,
                ])
            @endif
        </div>
    </div>
</body>

</html>
