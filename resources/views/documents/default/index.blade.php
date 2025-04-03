<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>DOCUMENT FOR {{strtoupper($document->name)}} |  {{ env('APP_NAME') }}</title>

	<link rel="stylesheet" type="text/css" media="all" href="{{ asset('css/documents/default.css') }}">
	<link rel="stylesheet" type="text/css" media="all" href="{{ asset('css/icons.css') }}">

</head>
<body>
    {{-- Remove the 'ap-paper-preview' if this is for deployment --}}
    <div class="ap-paper">

        @php
            $beneficiary = $document->beneficiary;
            $company = $document->company;
            $setting = $company->idSetting;

            $view = $document->view
                ? json_decode($document->view)
                : null;
            $content = $document->content
                ? json_decode($document->content)
                : null;
            $options = $document->options
                ? json_decode($document->options)
                : null;
            $inputs = $document->inputs
                ? json_decode($document->inputs)
                : [];
            $tables = $document->tables
                ? json_decode($document->tables)
                : [];
            $approvals = $document->approvals
                ? json_decode($document->approvals)
                : null;
        @endphp

        @if($view->header)
            @include("documents.{$view->header}", [
                'company' => $company,
                'content' => $content,
                'setting' => $setting,
            ])
        @else
            @include('documents.default.header', [
                'company' => $company,
                'content' => $content,
                'setting' => $setting,
            ])
        @endif

        <table class="ap-document-table" width="100%">
            <tbody>
                <tr>
                    <td width="100%">

                        @if($view->content)
                            @include("documents.{$view->content}", [
                                'company' => $company,
                                'document' => $document,
                                'content' => $content,
                                'options' => $options,
                                'inputs' => $inputs,
                                'approvals' => $approvals,
                            ])
                        @else
                            @include("documents.default.content", [
                                'company' => $company,
                                'document' => $document,
                                'content' => $content,
                                'options' => $options,
                                'inputs' => $inputs,
                                'approvals' => $approvals,
                            ])
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>

        @if($view->footer)
            @include("documents.{$view->footer}", [
                'company' => $company,
                'setting' => $setting,
            ])
        @else
            @include('documents.default.footer', [
                'company' => $company,
                'setting' => $setting,
            ])
        @endif

    </div>
</body>
</html>