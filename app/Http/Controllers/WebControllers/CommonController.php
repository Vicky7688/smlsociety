<?php

namespace App\Http\Controllers\WebControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class CommonController extends Controller
{
	public function fetchData(Request $request, $type, $id = 0, $returntype = "all")
	{
		$request['return'] = 'all';
		$request['returntype'] = $returntype;
		$parentData = [\Auth::id()];
		switch ($type) {
           	case 'sliderstatement':
				$request['table'] = '\App\Models\Slider';
				$request['searchdata'] = ['categoryName	'];
				$request['select'] = 'all';
				$request['order'] = ['id', 'desc'];
				$request['parentData'] = 'all';
				break;
			case 'sessionmaster':
				$request['table'] = '\App\Models\SessionMaster';
				$request['searchdata'] = ['startDate', 'endDate'];
				$request['select'] = 'all';
				$request['order'] = ['id', 'desc'];
				$request['parentData'] = 'all';
				break;
			case 'state':
				$request['table'] = '\App\Models\StateMaster';
				$request['searchdata'] = ['name'];
				$request['select'] = 'all';
				$request['order'] = ['id', 'desc'];
				$request['parentData'] = 'all';
				break;
			case 'districtmaster':
				$request['table'] = '\App\Models\DistrictMaster';
				$request['searchdata'] = ['name'];
				$request['select'] = 'all';
				$request['order'] = ['id', 'desc'];
				$request['parentData'] = 'all';
				break;
			case 'tehsilmaster':
				$request['table'] = '\App\Models\TehsilMaster';
				$request['searchdata'] = ['name'];
				$request['select'] = 'all';
				$request['order'] = ['id', 'desc'];
				$request['parentData'] = 'all';
				break;
			case 'postmaster':
				$request['table'] = '\App\Models\PostOfficeMaster';
				$request['searchdata'] = ['name'];
				$request['select'] = 'all';
				$request['order'] = ['id', 'desc'];
				$request['parentData'] = 'all';
				break;
			case 'villagemaster':
				$request['table'] = '\App\Models\VillageMaster';
				$request['searchdata'] = ['name'];
				$request['select'] = 'all';
				$request['order'] = ['id', 'desc'];
				$request['parentData'] = 'all';
				break;
			case 'groupmaster':
				$request['table'] = '\App\Models\GroupMaster';
				$request['searchdata'] = ['name'];
				$request['select'] = 'all';
				$request['order'] = ['id', 'desc'];
				$request['parentData'] = 'all';
				break;
			case 'ledgermaster':
				$request['table'] = '\App\Models\LedgerMaster';
				$request['searchdata'] = ['name'];
				$request['select'] = 'all';
				$request['order'] = ['id', 'desc'];
				$request['parentData'] = 'all';
				break;
			case 'borrowing':
				$request['table'] = '\App\Models\BorrowingLimitMaster';
				$request['searchdata'] = ['name'];
				$request['select'] = 'all';
				$request['order'] = ['id', 'desc'];
				$request['parentData'] = 'all';
				break;
			case 'naretionmaster':
				$request['table'] = '\App\Models\NarrationMaster';
				$request['searchdata'] = ['name'];
				$request['select'] = 'all';
				$request['order'] = ['id', 'desc'];
				$request['parentData'] = 'all';
				break;
			case 'savingTransaction':
				$request['table'] = '\App\Models\MemberSaving';
				$request['searchdata'] = ['accountNo'];
				$request['select'] = 'all';
				$request['order'] = ['id', 'desc'];
				$request['parentData'] = 'all';
				break;
			case 'purposeMaster':
				$request['table'] = '\App\Models\PurposeMaster';
				$request['searchdata'] = ['name'];
				$request['select'] = 'all';
				$request['order'] = ['id', 'desc'];
				$request['parentData'] = 'all';
				break;
			case 'loanMaster':
				$request['table'] = '\App\Models\LoanMaster';
				$request['searchdata'] = ['name'];
				$request['select'] = 'all';
				$request['order'] = ['id', 'desc'];
				$request['parentData'] = 'all';
				break;
			case 'loanTypeMaster':
				$request['table'] = '\App\Models\LoanTypeMaster';
				$request['searchdata'] = ['name'];
				$request['select'] = 'all';
				$request['order'] = ['id', 'desc'];
				$request['parentData'] = 'all';
				break;
			case 'dailySchemes':
				$request['table'] = '\App\Models\SchemeMaster';
				$request['searchdata'] = ['name'];
				$request['select'] = 'all';
				$request['order'] = ['id', 'desc'];
				$request['parentData'] = 'all';
				break;
			case 'commissionMaster':
				$request['table'] = '\App\Models\CommissionMaster';
				$request['searchdata'] = ['name'];
				$request['select'] = 'all';
				$request['order'] = ['id', 'desc'];
				$request['parentData'] = 'all';
				break;
			case 'agentMaster':
				$request['table'] = '\App\Models\AgentMaster';
				$request['searchdata'] = ['name'];
				$request['select'] = 'all';
				$request['order'] = ['id', 'desc'];
				$request['parentData'] = 'all';
				break;
			case 'fdMaster':
				$request['table'] = '\App\Models\FdMaster';
				$request['searchdata'] = ['name'];
				$request['select'] = 'all';
				$request['order'] = ['id', 'desc'];
				$request['parentData'] = 'all';
				break;
			case 'taxMaster':
				$request['table'] = '\App\Models\TaxMaster';
				$request['searchdata'] = ['name'];
				$request['select'] = 'all';
				$request['order'] = ['id', 'desc'];
				$request['parentData'] = 'all';
				break;
			case 'unitMaster':
				$request['table'] = '\App\Models\UnitMaster';
				$request['searchdata'] = ['name'];
				$request['select'] = 'all';
				$request['order'] = ['id', 'desc'];
				$request['parentData'] = 'all';
				break;
			case 'itemMaster':
				$request['table'] = '\App\Models\ItemMaster';
				$request['searchdata'] = ['name'];
				$request['select'] = 'all';
				$request['order'] = ['id', 'desc'];
				$request['parentData'] = 'all';
				break;
			case 'depotMaster':
				$request['table'] = '\App\Models\DepotMaster';
				$request['searchdata'] = ['name'];
				$request['select'] = 'all';
				$request['order'] = ['id', 'desc'];
				$request['parentData'] = 'all';
				break;
			case 'saleClientMaster':
				$request['table'] = '\App\Models\SaleClientMaster';
				$request['searchdata'] = ['name'];
				$request['select'] = 'all';
				$request['order'] = ['id', 'desc'];
				$request['parentData'] = 'all';
				break;
			case 'purchaseClientMaster':
				$request['table'] = '\App\Models\PurchaseClientMaster';
				$request['searchdata'] = ['name'];
				$request['select'] = 'all';
				$request['order'] = ['id', 'desc'];
				$request['parentData'] = 'all';
				break;
			case 'branchMaster':
				$request['table'] = '\App\Models\BranchMaster';
				$request['searchdata'] = ['name'];
				$request['select'] = 'all';
				$request['order'] = ['id', 'desc'];
				$request['parentData'] = 'all';
				break;
			default:
				# code...
				break;
		}

		$request['where'] = 0;
		$request['type'] = $type;

		try {
			$totalData = $this->getData($request, 'count');
		} catch (\Exception $e) {
			$totalData = 0;
		}

		if (
			(isset($request->searchtext) && !empty($request->searchtext)) ||
			(isset($request->todate) && !empty($request->todate)) ||
			(isset($request->product) && !empty($request->product)) ||
			(isset($request->status) && $request->status != '') ||
			(isset($request->agent) && !empty($request->agent))
		) {
			$request['where'] = 1;
		}

		try {
			$totalFiltered = $this->getData($request, 'count');
		} catch (\Exception $e) {
			$totalFiltered = 0;
		}
		//return $data = $this->getData($request, 'data');
		try {
			$data = $this->getData($request, 'data');
		} catch (\Exception $e) {
			$data = [$e];
		}
		//dd($data);
		if ($request->return == "all" || $returntype == "all") {

			$json_data = array(
				"draw" => intval($request['draw']),
				"recordsTotal" => intval($totalData),
				"recordsFiltered" => intval($totalFiltered),
				"data" => $data
			);
			echo json_encode($json_data);
		} else {
			return response()->json($data);
		}
	}

	public function getData($request, $returntype)
	{
		$table = $request->table;
		$data = $table::query();
		$data->orderBy($request->order[0], $request->order[1]);

		if ($request->parentData != 'all') {
			if (!is_array($request->whereIn)) {
				$data->whereIn($request->whereIn, $request->parentData);
			} else {
				$data->where(function ($query) use ($request) {
					$query->where($request->whereIn[0], $request->parentData)
						->orWhere($request->whereIn[1], $request->parentData);
				});
			}
		}

		if (
			$request->type != "sessionmaster" &&
			$request->type != "tehsilmaster" &&
			$request->type != "villagemaster" &&
			$request->type != "borrowing" &&
			$request->type != "groupmaster"   &&
			$request->type != "ledgermaster" &&
			$request->type != "loanMaster" &&
			$request->type != "purposeMaster" &&
			$request->type != "dailySchemes" &&
			$request->type != "commissionMaster" &&
			$request->type != "agentMaster" &&
			$request->type != "fdMaster" &&
			$request->type != "taxMaster" &&
			$request->type != "loanTypeMaster" &&
			$request->type != "unitMaster" &&
			$request->type != "itemMaster" &&
			$request->type != "depotMaster" &&
			$request->type != "saleClientMaster" &&
			$request->type != "purchaseClientMaster" &&
			$request->type != "branchMaster" &&
			$request->type != "sliderstatement"

		) {
			if (!empty($request->fromdate)) {
				$data->whereDate('created_at', $request->fromdate);
			}
		}

		switch ($request->type) {
			case 'sessionmaster':
				//$data->where('is_ip_delete', '0');
				break;
		}

		if ($request->where) {
			if (
				(isset($request->fromdate) && !empty($request->fromdate))
				&& (isset($request->todate) && !empty($request->todate))
			) {
				if ($request->fromdate == $request->todate) {
					$data->whereDate('created_at', '=', Carbon::createFromFormat('Y-m-d', $request->fromdate)->format('Y-m-d'));
				} else {
					$data->whereBetween('created_at', [Carbon::createFromFormat('Y-m-d', $request->fromdate)->format('Y-m-d'), Carbon::createFromFormat('Y-m-d', $request->todate)->addDay(1)->format('Y-m-d')]);
				}
			}

			if (isset($request->product) && !empty($request->product)) {

				switch ($request->type) {
				}
			}

			if (isset($request->status) && $request->status != '' && $request->status != null) {
				switch ($request->type) {

					case 'sessionmaster':
						$data->where('status', $request->status);
						break;
					default:
						$data->where('status', $request->status);
						break;
				}
			}

			if (!empty($request->searchtext)) {
				$data->where(function ($q) use ($request) {
					foreach ($request->searchdata as $value) {
						$q->orWhere($value, 'like', $request->searchtext . '%');
						$q->orWhere($value, 'like', '%' . $request->searchtext . '%');
						$q->orWhere($value, 'like', '%' . $request->searchtext);
					}
				});
			}
		}

		if ($request->return == "all" || $request->returntype == "all") {
			if ($returntype == "count") {
				return $data->count();
			} else {
				if ($request['length'] != -1) {
					$data->skip($request['start'])->take($request['length']);
				}

				if ($request->select == "all") {
					return $data->get();
				} else {
					return $data->select($request->select)->get();
				}
			}
		} else {
			if ($request->select == "all") {
				return $data->first();
			} else {
				return $data->select($request->select)->first();
			}
		}
	}


	public function update(Request $post)
	{

		switch ($post->actiontype) {
			case 'sessionmaster':

				break;

			default:

				break;
		}
	}
}
