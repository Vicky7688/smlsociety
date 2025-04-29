<!--table 1-->

<table class="table table-bordered" style="width: 50%; float: left;">
   <thead class="table_head">
      <tr>
         <th class="borderr  text-center text-uppercase" colspan="3">Liability</th>
      </tr>
      <tr>
         <th class="borderr ">Particulars</th>
         <th class="borderr  text-right">Amount</th>
         <th class="borderr  text-right">Total</th>
      </tr>
   </thead>
   <tbody>
       @foreach($Liability as $Liabilityvalue)
       <tr>

         <td class=" ">{{$Liabilityvalue['head_name']}}</td>
         <td class="  text-right"></td>
         <td class="  text-right">{{$Liabilityvalue['total_amount']}}</td>
      </tr>

       @foreach($Liabilityvalue['data'] as $value)
        <tr>
         <td class="borderr ">{{$value['item_name']}}</td>
         <td class="borderr  text-right">{{$value['amount']}}</td>
         <td class="borderr  text-right"></td>
      </tr>
       @endforeach
       @endforeach
      @if($Assetcount > $Liabilitycount)
          @for($i = 0; $i < $Assetcount - $Liabilitycount; $i++)
           <tr>
            <td class="borderr "> - </td>
             <td class="borderr  text-right"> - </td>
             <td class="borderr  text-right"> - </td>
            </tr>
          @endfor
      @endif
   </tbody>
</table>
<!--Table two-->
<table  class=" table table-bordered" style="width: 50%; float: left;">
   <thead class="table_head">
      <tr>
         <th class="borderr  text-center text-uppercase" colspan="3">Asset</th>
      </tr>
      <tr>
         <th class="borderr ">Particulars</th>
         <th class="borderr  text-right">Amount</th>
         <th class="borderr  text-right">Total</th>
      </tr>
   </thead>
   <tbody>
       @foreach($Asset as $Assetvalue)

        
       <tr>
         <td class="borderr ">{{$Assetvalue['head_name']}}</td>
         <td class="borderr  text-right"></td>
         <td class="borderr  text-right">{{$Assetvalue['total_amount']}}</td>
      </tr>
      
       @foreach($Assetvalue['data'] as $value)
      
        <tr>
             <td class="borderr ">{{$value['item_name']}}</td>
             <td class="borderr  text-right">{{$value['amount']}}</td>
             <td class="borderr  text-right"></td>
        </tr>
       
       @endforeach
       @endforeach

        @if($Assetcount < $Liabilitycount)
          @for($i = 0; $i < $Liabilitycount - $Assetcount; $i++)
           <tr>
             <td class="borderr "> - </td>
             <td class="borderr  text-right"> - </td>
             <td class="borderr  text-right"> - </td>
            </tr>
          @endfor
      @endif
   </tbody>
</table>

<div style="clear:both;"></div>

<table  class="table table-borderred table-striped table-sm" style="width: 50%; float: left;">
    <tbody>
        <tr>
            @if($totalProfit > 0)
            <td><b>Net Profit</b></td>
            <td style="text-align:right"><b>{{$totalProfit}}</b></td>
                @php
                $profit = $totalProfit ;
                @endphp
            @else
            <td colspan="3">-</td>
                @php
                $profit = 0 ;
                @endphp
            @endif
        </tr>
        <tr>
            <td colspan="1"><b>Grand Total (Liabilities)</b></td>
            <td style="text-align:right"><b>{{$LiabilityTotal + $profit}}</b></td>
        </tr>
    </tbody>
</table>

<table id="" class="table table-borderred table-striped table-sm" style="width: 50%; float: left;">
    <tbody>
        <tr>
             @if($totalExpense > 0)
                <td><b>Net Loss</b></td>
                <td style="text-align:right"><b>{{$totalExpense}}</b></td>
                   @php
                    $profit = $totalExpense ;
                   @endphp
            @else
            <td colspan="3">-</td>
              @php
                $profit = 0 ;
                @endphp
            @endif
        </tr>
        <tr>
            <td colspan="1"><b>Grand Total (Assets)</b></td>
            <td style="text-align:right"><b>{{$AssetTotal + $profit}}</b></td>
        </tr>
    </tbody>
</table>
