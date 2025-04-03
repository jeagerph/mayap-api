<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>{{$invoice->invoice_no}} INVOICE | BCMP</title>

	<link rel="stylesheet" type="text/css" media="all" href="{{ asset('css/invoices/default.css') }}">

</head>
<body>
    {{-- Remove the 'ap-paper-preview' if this is for deployment --}}
    <div class="ap-paper">

        <div style="text-align: center;">
            <img src="{{ asset('assets/images/kapitan-ph/velcro-tech-header-h.png')}}" style="width: 70%; margin-top: 0px; margin-bottom: 5px;" alt="BCMP Header Logo">
        </div>

        

        @include('print.invoice.content.header', ['invoice' => $invoice])

        @include('print.invoice.content.content', ['invoice' => $invoice])


    </div>
</body>
</html>