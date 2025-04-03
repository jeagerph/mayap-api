<div class="ap-invoice-content">

    <table>
        <tr>
            <td width="35%">
                <div>
                    <small class="ap-label">
                        Billed To
                    </small>
                </div>

                <p class="ap-barangay">
                    BRGY {{$invoice->profile->barangay_name}}
                </p>
                <p class="ap-city">
                    {{$invoice->cityName()}}
                </p>

                <div class="ap-attention">
                    <small class="ap-label">
                        Attention:
                    </small>
                    @if($invoice->show_left_representative)
                    <div class="ap-representative">
                        <p class="ap-name">
                            {{strtoupper($invoice->representativeName('left'))}}
                        </p>
                        <p class="ap-position">
                            {{strtoupper($invoice->left_representative_position)}}
                        </p>
                    </div>
                    @endif
                    @if($invoice->show_right_representative)
                    <div class="ap-representative">
                        <p class="ap-name">
                            {{strtoupper($invoice->representativeName('right'))}}
                        </p>
                        <p class="ap-position">
                            {{strtoupper($invoice->right_representative_position)}}
                        </p>
                    </div>
                    @endif
                </div>

                
            </td>
            <td width="35%">
                <div>
                    <small class="ap-label">
                        Billing No.
                    </small>
                </div>

                <p class="ap-text">
                    {{$invoice->invoice_no}}
                </p>

                <div style="margin-top: 35px;">
                    <small class="ap-label">
                        Date Issued
                    </small>
                </div>

                <p class="ap-text">
                    {{(new \Carbon\Carbon($invoice->invoice_date))->format('F d, Y')}}
                </p>
                
            </td>
            <td width="30%"
                style="text-align: right;">
                <small class="ap-label">
                    Billing Total
                </small>

                <p class="ap-text-invoice-total">
                    PHP {{number_format($invoice->totalInclusions() - $invoice->discount, 2)}}
                </p>
            </td>
        </tr>
    </table>

    <div class="ap-service-inclusions">
        {{-- <p class="ap-title">
            SERVICE INCLUSIONS
        </p> --}}

        <p class="ap-text">
            Good Day!
        </p>

        <p class="ap-text" style="text-indent: 25px;">
            This is to bill your office for the payment of the service item(s) as stated below:
        </p>

        @php
            $totalOtherCharges = 0;
            $totalDomainHosting = 0;
            $totalVirtualStorage = 0;
            $totalDessiminations = 0;

            $totalInclusionAmount = 0;
            $totalInclusionDiscount = 0;

            $otherCharges = $invoice->inclusions()->where('inclusion_type', 1)->orderBy('start_date', 'asc')->get();
            $domainHostings = $invoice->inclusions()->where('inclusion_type', 2)->orderBy('start_date', 'asc')->get();
            $virtualStorage = $invoice->inclusions()->where('inclusion_type', 3)->orderBy('start_date', 'asc')->get();
            $dessiminations = $invoice->inclusions()->whereIn('inclusion_type', [4,5])->orderBy('start_date', 'asc')->get();
            
        @endphp

        @if(count($domainHostings))

            <p style="color: #46a3db;">
                DOMAIN & HOSTING
            </p>

            <table class="ap-table ap-table-divider">
                <thead>
                    <tr style="background: #46a3db; padding: 2px 2px; color: white;">
                        <th width="30%">SERVICE/ITEM</th>
                        <th width="15%">DATE</th>
                        <th width="10%">QTY</th>
                        <th width="15%">UNIT PRICE</th>
                        <th width="15%">DISCOUNT</th>
                        <th width="15%" style="text-align: right;">UNIT TOTAL</th>
                    </tr>
                </thead>
    
                <tbody>
                    @foreach ($domainHostings as $item)
    
                        @php
                            $amount = $item->unit_price * $item->quantity;
                        @endphp
        
                        <tr>
                            <td width="30%">
                                {{$item->name}}

                                @if($item->description)
                                <div style="margin-top: 5px;">
                                    <small>
                                        {{$item->description}}
                                    </small>
                                </div>
                                @endif

                                @if($item->remarks)
                                <div style="margin-top: 5px;">
                                    <small style="font-style: italic;">
                                        REMARKS: {{$item->remarks}}
                                    </small>
                                </div>
                                @endif
                            </td>
                            <td width="15%">
                                {{ strtoupper((new \Carbon\Carbon($item->start_date))->format('M-d-Y')) }} ~ <br>{{ strtoupper((new \Carbon\Carbon($item->end_date))->format('M-d-Y')) }}
                            </td>
                            <td width="10%">
                                {{$item->quantity}}
                            </td>
                            <td width="15%">
                                {{number_format($item->unit_price, 2)}}
                            </td>
                            <td width="15%">
                                {{number_format($item->discount, 2)}}
                            </td>
                            <td width="15%" style="text-align: right;">
                                {{number_format($amount - $item->discount, 2)}}
                            </td>
                        </tr>
        
                        @php
                            $totalDomainHosting += ($amount - $item->discount);

                            $totalInclusionAmount += $amount;
                            $totalInclusionDiscount += $item->discount;
                        @endphp
        
                    @endforeach

                    <tr>
                        <td width="30%">
                            
                        </td>
                        <td width="15%">
                            
                        </td>
                        <td width="10%">
                            
                        </td>
                        <td width="15%">
                            
                        </td>
                        <td width="15%">
                            SUBTOTAL
                        </td>
                        <td width="15%" style="text-align: right; font-weight: bold;">
                            {{number_format($totalDomainHosting, 2)}}
                        </td>
                    </tr>
                </tbody>
            </table>
        @endif

        @if(count($virtualStorage))

            <p style="color: #46a3db;">
                VIRTUAL STORAGE
            </p>

            <table class="ap-table ap-table-divider">
                <thead>
                    <tr style="background: #46a3db; padding: 2px 2px; color: white;">
                        <th width="30%">SERVICE/ITEM</th>
                        <th width="15%">DATE</th>
                        <th width="10%">QTY</th>
                        <th width="15%">UNIT PRICE</th>
                        <th width="15%">DISCOUNT</th>
                        <th width="15%" style="text-align: right;">UNIT TOTAL</th>
                    </tr>
                </thead>
    
                <tbody>
                    @foreach ($virtualStorage as $item)
    
                        @php
                            $amount = $item->unit_price * $item->quantity;
                        @endphp
        
                        <tr>
                            <td width="30%">
                                {{$item->name}}

                                @if($item->description)
                                <div style="margin-top: 5px;">
                                    <small>
                                        {{$item->description}}
                                    </small>
                                </div>
                                @endif

                                @if($item->remarks)
                                <div style="margin-top: 5px;">
                                    <small style="font-style: italic;">
                                        REMARKS: {{$item->remarks}}
                                    </small>
                                </div>
                                @endif
                            </td>
                            <td width="15%">
                                {{ strtoupper((new \Carbon\Carbon($item->start_date))->format('M-d-Y')) }} ~ <br>{{ strtoupper((new \Carbon\Carbon($item->end_date))->format('M-d-Y')) }}
                            </td>
                            <td width="10%">
                                {{$item->quantity}}
                            </td>
                            <td width="15%">
                                {{number_format($item->unit_price, 2)}}
                            </td>
                            <td width="15%">
                                {{number_format($item->discount, 2)}}
                            </td>
                            <td width="15%" style="text-align: right;">
                                {{number_format($amount - $item->discount, 2)}}
                            </td>
                        </tr>
        
                        @php
                            $totalVirtualStorage += ($amount - $item->discount);
                            
                            $totalInclusionAmount += $amount;
                            $totalInclusionDiscount += $item->discount;
                        @endphp
        
                    @endforeach

                    <tr>
                        <td width="30%">
                            
                        </td>
                        <td width="15%">
                            
                        </td>
                        <td width="10%">
                            
                        </td>
                        <td width="15%">
                            
                        </td>
                        <td width="15%">
                            SUBTOTAL
                        </td>
                        <td width="15%" style="text-align: right; font-weight: bold;">
                            {{number_format($totalVirtualStorage, 2)}}
                        </td>
                    </tr>
                </tbody>
            </table>
        @endif

        @if(count($dessiminations))

            <p style="color: #46a3db;">
                INFORMATION DESSIMINATION
            </p>

            <table class="ap-table ap-table-divider">
                <thead>
                    <tr style="background: #46a3db; padding: 2px 2px; color: white;">
                        <th width="30%">SERVICE/ITEM</th>
                        <th width="15%">DATE</th>
                        <th width="10%">QTY</th>
                        <th width="15%">UNIT PRICE</th>
                        <th width="15%">DISCOUNT</th>
                        <th width="15%" style="text-align: right;">UNIT TOTAL</th>
                    </tr>
                </thead>
    
                <tbody>
                    @foreach ($dessiminations as $item)
    
                        @php
                            $amount = $item->unit_price * $item->quantity;
                        @endphp
        
                        <tr>
                            <td width="20%">
                                {{$item->name}}

                                @if($item->description)
                                <div style="margin-top: 5px;">
                                    <small>
                                        {{$item->description}}
                                    </small>
                                </div>
                                @endif

                                @if($item->remarks)
                                <div style="margin-top: 5px;">
                                    <small style="font-style: italic;">
                                        REMARKS: {{$item->remarks}}
                                    </small>
                                </div>
                                @endif
                            </td>
                            <td width="15%">
                                {{ strtoupper((new \Carbon\Carbon($item->start_date))->format('M-d-Y')) }}
                            </td>
                            <td width="10%">
                                {{$item->quantity}}
                            </td>
                            <td width="15%">
                                {{number_format($item->unit_price, 2)}}
                            </td>
                            <td width="15%">
                                {{number_format($item->discount, 2)}}
                            </td>
                            <td width="15%" style="text-align: right;">
                                {{number_format($amount - $item->discount, 2)}}
                            </td>
                        </tr>
        
                        @php
                            $totalDessiminations += ($amount - $item->discount);

                            $totalInclusionAmount += $amount;
                            $totalInclusionDiscount += $item->discount;
                        @endphp
        
                    @endforeach

                    <tr>
                        <td width="30%">
                            
                        </td>
                        <td width="15%">
                            
                        </td>
                        <td width="10%">
                            
                        </td>
                        <td width="15%">
                            
                        </td>
                        <td width="15%">
                            SUBTOTAL
                        </td>
                        <td width="15%" style="text-align: right; font-weight: bold;">
                            {{number_format($totalDessiminations, 2)}}
                        </td>
                    </tr>
                </tbody>
            </table>
        @endif

        @if(count($otherCharges))

            <p style="color: #46a3db;">
                OTHER CHARGES
            </p>

            <table class="ap-table ap-table-divider">
                <thead>
                    <tr style="background: #46a3db; padding: 2px 2px; color: white;">
                        <th width="30%">SERVICE/ITEM</th>
                        <th width="15%">DATE</th>
                        <th width="10%">QTY</th>
                        <th width="15%">UNIT PRICE</th>
                        <th width="15%">DISCOUNT</th>
                        <th width="15%" style="text-align: right;">UNIT TOTAL</th>
                    </tr>
                </thead>
    
                <tbody>
                    @foreach ($otherCharges as $item)
    
                        @php
                            $amount = $item->unit_price * $item->quantity;
                        @endphp
        
                        <tr>
                            <td width="30%">
                                {{$item->name}}

                                @if($item->description)
                                <div style="margin-top: 5px;">
                                    <small>
                                        {{$item->description}}
                                    </small>
                                </div>
                                @endif

                                @if($item->remarks)
                                <div style="margin-top: 5px;">
                                    <small style="font-style: italic;">
                                        REMARKS: {{$item->remarks}}
                                    </small>
                                </div>
                                @endif
                            </td>
                            <td width="15%">
                                {{ strtoupper((new \Carbon\Carbon($item->start_date))->format('M-d-Y')) }} ~ <br>{{ strtoupper((new \Carbon\Carbon($item->end_date))->format('M-d-Y')) }}
                            </td>
                            <td width="10%">
                                {{$item->quantity}}
                            </td>
                            <td width="15%">
                                {{number_format($item->unit_price, 2)}}
                            </td>
                            <td width="15%">
                                {{number_format($item->discount, 2)}}
                            </td>
                            <td width="15%" style="text-align: right;">
                                {{number_format($amount - $item->discount, 2)}}
                            </td>
                        </tr>
        
                        @php
                            $totalOtherCharges += ($amount - $item->discount);

                            $totalInclusionAmount += $amount;
                            $totalInclusionDiscount += $item->discount;
                        @endphp
        
                    @endforeach

                    <tr>
                        <td width="30%">
                            
                        </td>
                        <td width="15%">
                            
                        </td>
                        <td width="10%">
                            
                        </td>
                        <td width="15%">
                            
                        </td>
                        <td width="15%">
                            SUBTOTAL
                        </td>
                        <td width="15%" style="text-align: right; font-weight: bold;">
                            {{number_format($totalOtherCharges, 2)}}
                        </td>
                    </tr>
                </tbody>
            </table>
        @endif

        <table>
            <tr>
                <td width="30%"></td>
                <td width="30%"></td>
                <td width="40%">
                    <table>
                        <tr>
                            <td width="40%" style="text-align: right;">
                                <p style="font-family: ArialRegular; font-size: 14px; margin-bottom: 1px;">
                                    SUBTOTAL
                                </p>
                            </td>
                            <td width="60%" style="text-align: right;">
                                <p style="font-family: ArialBold; margin-bottom: 1px;">
                                    {{number_format($totalInclusionAmount, 2)}}
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td width="40%" style="text-align: right;">
                                <p style="font-family: ArialRegular; font-size: 14px; margin-top: 0px;">
                                    DISCOUNT
                                </p>
                            </td>
                            <td width="60%" style="text-align: right;">
                                <p style="font-family: ArialBold; margin-top: 0px;">
                                    {{number_format($totalInclusionDiscount, 2)}}
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td width="40%" style="text-align: right;">
                                <p style="font-family: ArialRegular; font-size: 14px; margin-top: 1px;">
                                    TOTAL
                                </p>
                            </td>
                            <td width="60%" style="text-align: right;">
                                <p style="font-family: ArialBold; margin-top: 1px;">
                                    {{number_format($totalInclusionAmount - $totalInclusionDiscount, 2)}}
                                </p>
                            </td>
                        </tr>
                    </table>
                    
                </td>
            </tr>
        </table>
    </div>

    <div class="ap-signature">
        <p>
            We trust that you will find our services satisfactory, should you have concerns regarding our services, feel free to contact us to enlighten you on the details.
        </p>

        <div class="ap-assignatory">
            <p>
                Very truly yours,
            </p>

            <img class="ap-image" src="{{asset('assets/images/kapitan-ph/rhea-mae-antonio-signature.png')}}" alt="Velcro Representative Signature">
    
            <p class="ap-name">
                Rhea Mae M. Antonio
            </p>
            <small>
                Sales Representative
            </small>
        </div>

        
    </div>
    
</div>