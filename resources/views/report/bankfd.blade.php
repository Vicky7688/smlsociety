@extends('layouts.app')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card page_headings cards mb-4">
        <div class="card-body py-2">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="py-2"><span class="text-muted fw-light">Reports / </span>Bank FD</h4>
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-12 mb-4 cards">
            <div class="card">
                <div class="card-body">
                    <form action="javascript:void(0)" id="bankfdReport" name="bankfdReport">
                        <div class="row">
                            <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 inputesPaddingReport">
                                <label for="Date from" class="form-label">Date from</label>
                                <input type="text" class="form-control formInputsReport" id="date_from" name="date_from"  value="{{ Session::get('currentdate') }} ?? {{ date('d-m-Y', strtotime(session('sessionStart'))) }}" readonly/>
                            </div>
                            <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 inputesPaddingReport">
                                <label for="Till Date" class="form-label">Till Date</label>
                                <input type="text" class="form-control formInputsReport" id="date_till_date" value="{{ date('d-m-Y',strtotime(session('sessionEnd'))) }}" name="date_till_date" readonly/>
                            </div>
                            <div class="col-lg-8 col-md-12 col-12 py-2 inputesPaddingReport reportBtnInput">
                                <div class="d-flex justify-content-end text-end mt-4 reportBtnCustom">

                                    <button type="submit" class="btn btn-primary waves-effect waves-light reportSmallBtnCustom"
                                        id="viewdatabooksdetails">
                                        View
                                    </button>
                                    {{--  <a type="button" href="{{route('balancebookPrint.print')}}" target="_blank"
                                        class="ms-2 btn btn-primary print-button reportSmallBtnCustom">
                                        Print
                                    </a>  --}}
                                    <div class="ms-2 dropdown">
                                        <button class="btn btn-primary dropdown-toggle reportSmallBtnCustom" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                            More
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <li> <a class="dropdown-item" href="#" onclick="downloadPDF()"><i class="fa-regular fa-file-pdf"></i> &nbsp; Download as PDF</a></li>
                                            <li><a class="dropdown-item" href="#" onclick="downloadWord()"><i class="fa-regular fa-file-word"></i> &nbsp; Download as Word</a></li>
                                            <li><a class="dropdown-item" href="#" onclick="share()"><i class="fa-solid fa-share-nodes"></i> &nbsp; Share</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body tablee" id="balanebookrecords">

        </div>
    </div>
</div>

@endsection
@push('script')
<script>
    $(document).ready(function(){
        document.getElementById('bankfdReport').addEventListener('submit',function(event){
            event.preventDefault();
            const startDate = document.getElementById('date_from').value;
            const endDate = document.getElementById('date_till_date').value;

            $.ajax({
                url : "{{ route('getbankfddetails') }}",
                type : 'post',
                data : {startDate:startDate , endDate:endDate},
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                dataType : 'json',
                success : function(res){

                }
            });
        });
    });
</script>
@endpush
