<!DOCTYPE html>
<html>
<head>
    <title>FD Certificate</title>
	<style>
		body{
			font-family:arial, helvetica, verdana;
		}
		table{
			 border-collapse: collapse;
			 width:100%;	
			 margin:0 auto;
		}
		table,tr,th,td{
		    border-color:#ccc;
		}
		th,td{
		    height: 10px;
			padding:4px;
		}
		/* div.heading,h2,h6{
			line-height:5px;
		} */
		.heading h6{
		    margin:6px;
		}
		 .footer td{
			font-weight: 600;
		}
		thead{
		    text-align:left;
			font-size:14px;
			background:#e4e4e4;
		}
		tbody{
		    font-size:15px;
		}
		@media print {
		  #printPageButton {
			display: none;
		  }
		}
		.outer-border{
			border:4px solid #000000;
			margin:0 auto;
			width:800px;
			padding:5px;
		}
		.inner-border{
			border:1px solid #000000;
			margin:0 auto;
			width:auto;
		}
		.decor{
			font-size:18px;border:2px solid #000000;width:300px;border-radius:50px;margin:0 auto;clear:both;padding:6px;font-weight:bold;
		}
		
	</style>
</head>

<body>
	<button id='printPageButton' onclick="window.print()">Print</button>
	<div id="printpage">
		<div class="outer-border">
			<div class="inner-border">
				<div class="heading" style="text-align: center;">
					<!--<h2 style="letter-spacing:1px; font-size:25px;padding-bottom:10px;">THE SANGHOL C.A.S.S. LTD. </h2>
					<h2 style="letter-spacing:1px; font-size:25px;">दि सन्घोल सी ए एस एस ली &#870; </h2>
					<h6 style="font-size:18px;">B.O:- SANGHOL, TEH:- JAISINGHPUR, DISTT:- KANGRA(H.P)</h6>-->
					<h2 style="letter-spacing:1px; font-size:25px;margin-bottom:0px;">{{$branch->name}}</h2>
					<h6 style="font-size:18px;">{{$branch->address}}</h6>
					<div class='decor'>FIXED DEPOSIT (Scheme) CERTIFICATE</div>
				</div>
				<table style="width:768px; height: auto;" border="0">
					<tbody>
						<tr><td>&nbsp;</td></tr>
						<tr>
							<td style="padding:0px;">
								<table cellpadding="0" cellspacing="0" border="0">
									<tr>
										<td width="30%"><b>A/C NO</b></td>
										<td><b>:-</b></td>
										<!--<td>2/149(Member)</td>-->
										<td>{{$fd->accountNo}}({{$fd->memberType}})</td>
									</tr>
									<tr>
										<td width="40%"><b>FD/LTD NO</b></td>
										<td><b>:-</b></td>
										<!--<td>0149</td>-->
										<td>{{$fd->fdNo}}</td>
									</tr>
									<tr>
										<td><b>L/F NO</b></td>
										<td><b>:-</b></td>
										<td>{{$fd->fdNo}}</td>
									</tr>
								</table>
							</td>
							<td align="right" style="padding:0px;">
								<table cellpadding="0" cellspacing="0" border="0" align="right" style="width:80%">
								   <tr>
										<td><b>Dated</b></td>
										<td><b>:-</b></td>
										<!--<td>12-05-2021</td>-->
										<td>{{ date('d-m-Y', strtotime($fd->openingDate))}}</td>
									</tr>
									<tr>
										<td width="20%"><b>Maturity Date</b></td>
										<td width="2%"><b>:-</b></td>
										<!--<td width="10%">12-05-2021</td>-->
										<td width="10%">{{date('d-m-Y', strtotime($fd->maturityDate))}}</td>
									</tr>
									<tr>
										<td><b>Interest Runs From</b></td>
										<td><b>:-</b></td>
										<!--<td>12-05-2021</td>-->
										<td>{{date('d-m-Y', strtotime($fd->interestStartDate))}}</td>
									</tr>
									
								</table>
							</td>
						</tr>
						<tr>
							<!--<td colspan="2"><b>Nominee:-</b> RAM AVATAR THAKUR, DEVENDER FADANVIS</td>-->
							<td colspan="2"><b>Nominee:-</b>{{$fd->nomineeName1}} , {{$fd->nomineeName2}}</td>
						</tr>
						<tr>
							<td colspan="2">
								<p><i>Received with thanks from Sh./Smt.</i> <b>{{$fd->memberAccount->name}}</b></p>
								<p><i>Rupees</i> <b>&#8377; {{$fd->principalAmount}}."/- ("{{$wordamount}}" only)";</b></p>
								<p><i>Maturity Value</i> <b>&#8377; {{ $fd->maturityAmount."/- (".$mwordamount." only)"}}</b></p>
								<p><i>as a long term deposit for</i> <b>12</b> <i>months bearing interest at</i> <b>{{$fd->interestAmount}}</b> <i>percent per annum.</i></p>
							</td>
						</tr>
						<tr><td style="height:50px;">&nbsp;</td></tr>
						<tr>
							<td>
								<div style="border:1px solid #000000;width:250px;padding:7px;font-size:26px;font-weight:bold;font-style:italic;">
									Rs. {{$fd->principalAmount."/-"; }}
								</div>
							</td>
							<td align="right" style="font-size: 10px;">
								<b>For {{$branch->name}}</b>
							</td>
						</tr>
						<tr><td>&nbsp;</td></tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</body>
</html>