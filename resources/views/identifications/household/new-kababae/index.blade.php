<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>HOUSEHOLD ID OF {{$identification->code}} | KAPITAN PH</title>

	<link rel="stylesheet" type="text/css" media="all" href="{{ asset('css/new-kababae-household-2.css') }}">

</head>
<body>
    {{-- Remove the 'ap-paper-preview' if this is for deployment --}}
    <div class="ap-paper">

        @php
            $constituent = $identification->constituent;
            $profile = $identification->profile;
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
                <td width="100%">
                    <div class="ap-identification ap-front"
                        style="background-image: url({{ asset('assets/images/new-kababae/household-1.png') }}); background-repeat: no-repeat; background-size: cover; background-position: center; background-color: #fff;">

                        @if($view->front)
                            @include("identifications.{$view->front}", [
                                'province' => $profile->province,
                                'city' => $profile->city,
                                'barangay' => $profile->barangay,
                                'profile' => $profile,
                                'view' => $view,
                                'content' => $content,
                            ])
                        @else
                            @include('identifications.default.front', [
                                'province' => $profile->province,
                                'city' => $profile->city,
                                'barangay' => $profile->barangay,
                                'profile' => $profile,
                                'view' => $view,
                                'content' => $content,
                                'approvals' => $approvals
                            ])
                        @endif
            
                        {{-- @if($view->front)
                            @include("identifications.{$view->front}", [
                                'constituent' => $constituent,
                                'identification' => $identification,
                                'content' => $content,
                                'options' => $options,
                            ])
                        @else
                            @include('identifications.default.front', [
                                'constituent' => $constituent,
                                'identification' => $identification,
                                'content' => $content,
                                'options' => $options,
                            ])
                        @endif --}}
                    </div>
                </td>
            </tr>
        </table>

    </div>
</body>
</html>