<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>IDENTIFICATION OF {{$identification->code}} | {{ env('APP_NAME') }}</title>

	<link rel="stylesheet" type="text/css" media="all" href="{{ asset('css/identifications/default.css') }}">

</head>
<body>
    {{-- Remove the 'ap-paper-preview' if this is for deployment --}}
    <div class="ap-paper">

        @php
            $beneficiary = $identification->beneficiary;
            $company = $identification->company;
            $idSetting = $company->idSetting;
            $view = $identification->view
                ? json_decode($identification->view)
                : null;
            $options = $identification->options
                ? json_decode($identification->options)
                : null;
            $content = $identification->content
                ? json_decode($identification->content)
                : null;
            $approvals = $identification->approvals
                ? json_decode($identification->approvals)
                : null;
        @endphp

        <table>
            <tr>
                <td width="50%">
                    <div class="ap-identification ap-identification-front">

                        @if($view->header)
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
            
                        @if($view->front)
                            @include("identifications.{$view->front}", [
                                'beneficiary' => $beneficiary,
                                'identification' => $identification,
                                'content' => $content,
                                'options' => $options,
                            ])
                        @else
                            @include('identifications.default.front', [
                                'beneficiary' => $beneficiary,
                                'identification' => $identification,
                                'content' => $content,
                                'options' => $options,
                            ])
                        @endif
                    </div>
                </td>
                <td width="50%">
                    <div class="ap-identification ap-identification-back">

                        @if($view->back)
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
                </td>
            </tr>
        </table>
    </div>
</body>
</html>