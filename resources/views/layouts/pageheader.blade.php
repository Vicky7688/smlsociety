<!-- Page header -->
<!-- <div class="page-header page-header-default mb-10">
    <div class="page-header-content">
        <div class="page-title">
            <div class="row">
                <h4 class="col-md-3 "><span class="text-semibold">Home</span> <span class="text-uppercase">- @yield('pagetitle')</span></h4>
                @if ($mydata['news'] != '' && $mydata['news'] != null)
                <h4 class="col-md-9 text-danger">
                    <marquee style="height: 25px" onmouseover="this.stop();" onmouseout="this.start();">{{$mydata['news']}}</marquee>
                </h4>
                @endif
            </div>
        </div>
    </div>
</div> -->
@if (!Request::is('loanenquiry') && !Request::is('flight'))
<!-- /page header -->

<form class="position-relative " id="searchForm">
    <div class="row ">
        <div class="col-sm-12 ">
            <div class="card mt-3">
                <div class="card-header d-flex justify-content-between">

                    <h4>@yield('pagetitle')</h4>

                    @if (@$export != null)
                    <div class="col-sm-12 col-md-6">
                        <div class="user-list-files d-flex float-end">
                            <button type="button" class="btn btn-danger me-2" id="formReset" data-loading-text="<b><i class='fa fa-spin fa-spinner'></i></b> Refreshing">Refresh</button>
                            <button type="button" class="btn btn-success submit-button text-white {{ isset($export) ? '' : 'hide' }}" product="{{ $export ?? '' }}" id="reportExport"> Export</button>

                        </div>
                    </div>
                    @endif
                </div>
                <div class="card-body">
                    <div>
                        <div class="row justify-content-between">
                            <div class="col-sm-12 col-md-12">
                                <div id="user_list_datatable_info" class="dataTables_filter">

                                    @if(isset($mystatus))
                                    <input type="hidden" name="status" value="{{$mystatus}}">
                                    @endif
                                    <div class="row">

                                        <div class="form-group col-md-2 m-b-10">
                                            <label for="exampleInputdate">From Date</label>
                                            <input type="date" name="from_date" class="form-control" placeholder="From Date">
                                        </div>

                                        <div class="form-group col-md-2 m-b-10">
                                            <label for="exampleInputdate">To Date</label>
                                            <input type="date" name="to_date" class="form-control" placeholder="To Date">
                                        </div>

                                        <div class="form-group col-md-2 m-b-10">
                                            <label for="exampleInputdate">Search Value</label>
                                            <input type="text" name="searchtext" class="form-control" placeholder="Search Value">
                                        </div>
                                        @if (Myhelper::hasNotRole(['retailer', 'apiuser',]))
                                        {{-- @if (@$export == null && (Myhelper::hasNotRole(['admin']))) --}}
                                        <div class="form-group col-md-2 m-b-10 {{ isset($agentfilter) ? $agentfilter : ''}}">
                                            <label for="exampleInputdate">User Id</label>
                                            <input type="text" name="agent" class="form-control" placeholder="Agent/Parent id">
                                        </div>
                                        {{-- @endif --}}
                                        @endif

                                        @if(isset($status))
                                        <div class="form-group col-md-2">
                                            <label for="exampleInputdate">Status</label>
                                            <select name="status" class="form-control select">
                                                <option value="">Select Status</option>
                                                @if (isset($status['data']) && sizeOf($status['data']) > 0)
                                                @foreach ($status['data'] as $key => $value)
                                                <option value="{{$key}}">{{$value}}</option>
                                                @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        @endif

                                        @if(isset($product))
                                        <div class="form-group col-md-2">
                                            <label for="exampleInputdate">Product</label>
                                            <select name="product" class="form-control select">
                                                <option value="">Select {{$product['type'] ?? ''}}</option>
                                                @if (isset($product['data']) && sizeOf($product['data']) > 0)
                                                @foreach ($product['data'] as $key => $value)
                                                <option value="{{$key}}">{{$value}}</option>
                                                @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        @endif

                                        <div class="col-md-2 col-sm-2 mt-3">
                                            <div class="user-list-files mt-3 d-flex search-button">
                                                <button type="submit" id="submit" class="btn btn-primary" data-loading-text="<b><i class='fa fa-spin fa-spinner'></i></b> Searching">
                                                    Search</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>
</form>
<!-- </div>
</div> -->
@endif

<div id="helpModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-slate">
                <button type="button" class="btn-close" data-bs-dismiss="modal">&times;</button>
                <h6 class="modal-title">Help Desk</h6>
            </div>
            <div class="modal-body no-padding">
                <table class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <tbody>
                        <tr>
                            <th>Support Number</th>
                            <td>{{$mydata['supportnumber']}}</td>
                        </tr>
                        <tr>
                            <th>Support Email</th>
                            <td>{{$mydata['supportemail']}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->