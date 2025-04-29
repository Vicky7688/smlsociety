<html>

<head>
	<title></title>
	<style>
	   
		table {
			border-collapse: collapse;
			width: 100%;
			margin: 0 auto;
		    border: 1px solid;
		}

		table,
		tr,
		th,
		td {
			border-color: #100101;
		}

		th,
		td {
			padding: 5px;
		}

		div.heading,
		h2,
		h6 {
			line-height: 5px;
		}

		.heading h6 {
			margin: 26px;
		}

		.footer td {
			font-weight: 600;
		}

		thead {
			text-align: left;
		}

		tbody {
			font-size: 12px;
		}

		@media print {
			#printPageButton {
				display: none;
			}
		}
	</style>
	<script type="text/javascript" src="/5GCgXNxaPt7-iIQt"></script>
</head>

<body>
	<button id='printPageButton' onclick="window.print()">Print</button>
	<div id="printpage">
		<div class="heading" style="text-align: center;">
			<h2 style="letter-spacing:1px; font-size:25px;">{{$branch->name}} </h2>
			<h6 style="font-size:22px;">{{$branch->address}}</h6>
			<b>
				<p style='font-size:18px'>Receipt & Disbursement Report From {{$startDate}} To {{$endDate}}</p>
			</b>
		</div>
		<table border="1px" style="width:730px; height: autopx;border-style: double;border-width: 0.5px;">
			<thead class="thead">
				<tr>
					<th style='text-align:center;'>Sr No.</th>
					<th>Group Name</th>
					<th style='text-align:right;'>Debit</th>
					<th style='text-align:right;'>Credit</th>
				</tr>
			</thead>
			<tbody>
			    @php 
			     $totalcr = 0 ;
			     $totaldr = 0 ;
			    @endphp
			    @foreach($groups as $key=>$group)
			    @php
			       $totalcr +=  $group->total_credit ;
			       $totaldr += $group->total_debit ;
			    @endphp
				<tr>
					<td width="10%" style='text-align:center;'>{{++$key}}</td>
					<td width="50%">{{$group->ledger->name ?? $group->group->name}}</td>
					<td width="20%" style='text-align:right;'>{{$group->total_debit}}</td>
					<td width="20%" style='text-align:right;'>{{$group->total_credit}}</td>
				</tr>
				@endforeach
			
				<tr class='footer'>
					<td colspan='2' style='text-align:right;'>Total:</td>
					<td style='text-align:right;'>{{$totaldr}}</td>
					<td style='text-align:right;'>{{$totalcr}}</td>
				</tr>
				<tr class='footer'>
					<td colspan='2' style='text-align:right;'>Opening Cash:</td>
					<td style='text-align:right;'></td>
					<td style='text-align:right;'>{{$openingCash}}</td>
				</tr>
				<tr class='footer'>
					<td colspan='2' style='text-align:right;'>Closing Cash:</td>
					<td style='text-align:right;'>{{$closingCash}}</td>
					<td style='text-align:right;'></td>
				</tr>
				<tr class='footer'>
					<td colspan='2' style='text-align:right;'>Grand Total:</td>
					<td style='text-align:right;'>{{$openingCash+$totalcr}}</td>
					<td style='text-align:right;'>{{$closingCash+$totaldr}}</td>
				</tr>
			</tbody>
		</table>
		<div style="text-align: center;">Page 1</div>
	</div>
</body>

</html>