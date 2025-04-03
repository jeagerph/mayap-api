<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>GENERATED DOCUMENTS REPORT | KAPITAN PH</title>

	<link rel="stylesheet" type="text/css" media="all" href="{{ asset('css/app.css') }}">

</head>
<body>
    {{-- Remove the 'ap-paper-preview' if this is for deployment --}}
    <div class="ap-paper">
        <div class="ap-text-center">
            {{-- <img src="{{ asset('images/precision-tek-black.png') }}" style="width: 20%;"> --}}

            <h1 class="ap-text-bold ap-margin-remove" style="font-size: 32px; letter-spacing: 2px; color: blue;">KAPITAN PH</h1>
            <div>
                <small style="letter-spacing: 2px;">
                    VELCRO TECH PH
                </small>
            </div>
            
        </div>

        <hr>
        
        <div class="ap-text-center">
            <h2 class="ap-margin-small-bottom">
                GENERATED DOCUMENTS REPORT
            </h2>
            <p class="ap-margin-remove-top ap-margin-xsmall-bottom">
                @if($from == $to)
                    {{strtoupper((new \Carbon\Carbon($from))->format('F d, Y'))}}
                @else
                    {{strtoupper((new \Carbon\Carbon($from))->format('F d, Y'))}} ~ {{strtoupper((new \Carbon\Carbon($to))->format('F d, Y'))}}
                @endif
            </p>
            <small class="ap-text-italic">
                (Data downloaded as of {{now()->format('M d, Y H:i A')}})
            </small>

            <p class="ap-text-bold">
                TOTAL DOCUMENTS: <span class="ap-margin-small-left">{{count($documentsList)}}</span>
            </p>
        </div>

        <div class="ap-margin-medium-top">
            <p class="ap-text-bold">
                LIST OF DOCUMENTS
            </p>

            <hr>

            <table width="100%"
                class="ap-table ap-table-divider ap-table-small ap-margin-remove-top">
                @foreach($documentsList as $document)
                    @if($document->constituent_type_id == 2)
                    <tr>
                        <td width="70%">
                            <p class="ap-text-bold ap-margin-remove-top ap-margin-small-bottom">
                                {{strtoupper($document->template->name)}}
                            </p>
                            <div>
                                @if($document->constituent)
                                <small>
                                    {{strtoupper($document->constituent->fullName())}}
                                </small>
                                @else
                                <small>
                                    DELETED
                                </small>
                                @endif
                                
                            </div>
                            <div>
                                @if($document->constituent)
                                <small>
                                    {{strtoupper($document->constituent->address)}}
                                </small>
                                @else
                                <small>
                                    DELETED
                                </small>
                                @endif
                            </div>
                            
                        </td>
                        <td width="30%" class="ap-text-right">
                            <div>
                                <small class="ap-text-bold">
                                    {{$document->code}}
                                </small>
                            </div>
                            <div class="ap-margin-small-top">
                                <small>
                                    {{$document->constituentTypes[$document->constituent_type_id]}}
                                </small>
                            </div>
                            <div class="ap-margin-small-top">
                                <small class="ap-text-italic">
                                    Date issued: {{(new \Carbon\Carbon($document->created_at))->format('F d, Y')}}
                                </small>
                            </div>
                        </td>
                    </tr>
                    @elseif($document->constituent_type_id == 3)
                    <tr>
                        <td width="70%">
                            <p class="ap-text-bold ap-margin-remove-top ap-margin-small-bottom">
                                {{strtoupper($document->template->name)}}
                            </p>
                            <div>
                                @if($document->constituent)
                                <small>
                                    {{strtoupper($document->constituent->name)}} ({{strtoupper($document->constituent->type)}})
                                </small>
                                @else
                                <small>
                                    DELETED
                                </small>
                                @endif
                            </div>
                            <div>
                                @if($document->constituent)
                                <small>
                                    {{strtoupper($document->constituent->owner_name)}}
                                </small>
                                @else
                                <small>
                                    DELETED
                                </small>
                                @endif
                                
                            </div>
                        </td>
                        <td width="30%" class="ap-text-right">
                            <div>
                                <small class="ap-text-bold">
                                    {{$document->code}}
                                </small>
                            </div>
                            <div class="ap-margin-small-top">
                                <small>
                                    {{$document->constituentTypes[$document->constituent_type_id]}}
                                </small>
                            </div>
                            <div class="ap-margin-small-top">
                                <small class="ap-text-italic">
                                    Date issued: {{(new \Carbon\Carbon($document->created_at))->format('F d, Y')}}
                                </small>
                            </div>
                        </td>
                    </tr>
                    @elseif($document->constituent_type_id == 4)
                    <tr>
                        <td width="70%">
                            <p class="ap-text-bold ap-margin-remove-top ap-margin-small-bottom">
                                {{strtoupper($document->template->name)}}
                            </p>
                            <div>
                                @if($document->constituent)
                                <small>
                                    {{strtoupper($document->constituent->owner_name)}}
                                </small>
                                @else
                                <small>
                                    DELETED
                                </small>
                                @endif
                            </div>
                            <div>
                                
                                @if($document->constituent)
                                <small>
                                    {{strtoupper($document->constituent->address)}}
                                </small>
                                @else
                                <small>
                                    DELETED
                                </small>
                                @endif
                            </div>
                        </td>
                        <td width="30%" class="ap-text-right">
                            <div>
                                <small class="ap-text-bold">
                                    {{$document->code}}
                                </small>
                            </div>
                            <div class="ap-margin-small-top">
                                <small>
                                    {{$document->constituentTypes[$document->constituent_type_id]}}
                                </small>
                            </div>
                            <div class="ap-margin-small-top">
                                <small class="ap-text-italic">
                                    Date issued: {{(new \Carbon\Carbon($document->created_at))->format('F d, Y')}}
                                </small>
                            </div>
                        </td>
                    </tr>
                    @elseif($document->constituent_type_id == 1)
                    <tr>
                        <td width="70%">
                            <p class="ap-text-bold ap-margin-remove-top ap-margin-small-bottom">
                                {{strtoupper($document->template->name)}}
                            </p>
                            <div>
                                <small>
                                    {{strtoupper($document->constituent->owner_name)}}
                                </small>
                            </div>

                        </td>
                        <td width="30%" class="ap-text-right">
                            <div>
                                <small class="ap-text-bold">
                                    {{$document->code}}
                                </small>
                            </div>
                            <div class="ap-margin-small-top">
                                <small>
                                    {{$document->constituentTypes[$document->constituent_type_id]}}
                                </small>
                            </div>
                            <div class="ap-margin-small-top">
                                <small class="ap-text-italic">
                                    Date issued: {{(new \Carbon\Carbon($document->created_at))->format('F d, Y')}}
                                </small>
                            </div>
                        </td>
                    </tr>
                    @endif
                @endforeach
            </table>
        </div>

        <div style="margin-top: 100px;">
            <table class="ap-table" width="100%">
                <tbody>
                    <tr>
                        <td style="width: 45%;">
                            
                        </td>
                        <td style="width: 10%;"></td>
                        <td style="width: 45%;">
                            <small class="ap-text-italic">
                                Prepared By:
                            </small>

                            <div class="ap-text-center ap-margin-medium-top" style="border-top: 1px solid black;">
                                <p class="ap-margin-remove-top ap-margin-xsmall-bottom ap-text-bold">
                                    {{strtoupper(Auth::user()->account->full_name)}}
                                </p>
                                <small>
                                    @if(Auth::user()->account->account_type_id == 2)
                                    {{strtoupper(Auth::user()->account->barangayAccount->barangayPosition->name)}}
                                    @endif
                                </small>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>
</body>
</html>