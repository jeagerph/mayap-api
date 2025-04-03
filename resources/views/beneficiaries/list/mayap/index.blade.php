<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>BENEFICIARIES REPORT | {{ $company->name }}</title>

    <link rel="stylesheet" type="text/css" media="all" href="{{ asset('css/reports/default.css') }}">

</head>
<body>
	<div class="ap-paper">

		@include("beneficiaries.list.mayap.content.header", [
            'company' => $company,
            'request' => $request,
            'beneficiaries' => $beneficiaries
        ])

        <div class="ap-content">
            @include("beneficiaries.list.mayap.content.items", [
                'company' => $company,
                'request' => $request,
                'beneficiaries' => $beneficiaries
            ])
        </div>
    </div>
</body>
</html>