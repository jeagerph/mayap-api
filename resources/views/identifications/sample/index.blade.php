<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>{{ $template }} | MEMBERSHIP PH</title>

	<link rel="stylesheet" type="text/css" media="all" href="{{ asset('css/identification.css') }}">

</head>
<body>
    {{-- Remove the 'ap-paper-preview' if this is for deployment --}}
    <div class="ap-paper">

        <table>
            <tr>
                <td width="50%">
                    
                    <div class="ap-identification ap-identification-front">

                        @include('identifications.sample.header', [
                            'member' => $member,
                            'company' => $member->company,
                            'template' => $template,
                        ])
            
                        @include('identifications.sample.front', [
                            'member' => $member,
                            'company' => $member->company,
                            'template' => $template,
                        ])
                    </div>
                </td>
                <td width="50%">
                    <div class="ap-identification ap-identification-back">

                        @include('identifications.sample.back', [
                            'member' => $member,
                            'company' => $member->company,
                            'template' => $template,
                        ])
                    </div>
                </td>
            </tr>
        </table>

    </div>
</body>
</html>