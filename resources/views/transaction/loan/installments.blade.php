@foreach($installments as $installment)
<tr>
    <td>{{$installment['installment']}}</td>
    <td>{{$installment['installment_date']}}</td>
    <td>{{$installment['opening_balance']}}</td>
    <td>{{$installment['principal']}}</td>
    <td>{{$installment['interest']}}</td>
    <td>{{$installment['total']}}</td>
    <td>{{$installment['remaining_balance']}}</td>
</tr>
@endforeach