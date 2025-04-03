<div class="ap-header">
    <table class="ap-header-table">
        <tbody>
            <tr>
                <td width="20%">
                    <div class="ap-header-logo">
                        <img src="{{ env('CDN_URL', '') . '/storage/' . $company->logo }}" height="150" width="150">
                    </div>
                    
                </td>
                <td class="ap-header-table-text" width="60%">
                    <p class="ap-header-office">{{ $setting->name }}</p>
                </td>
                <td width="20%">
                    <div class="ap-header-logo">
                        <img src="{{ env('CDN_URL', '') . '/storage/' . $company->sub_logo }}" height="150" width="150">
                    </div>
                </td>
            </tr>
        </tbody>
    </table>

    <div class="ap-header-border">
        @if($document->header_border)
            <img src="{{ env('CDN_URL') . '/storage/' . $document->header_border }}">
        @else
            <img src="{{ asset('assets/images/default-border.png') }}">
        @endif
    </div>
</div>


