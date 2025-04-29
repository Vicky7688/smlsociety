@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card page_headings cards mb-4">
        <div class="card-body py-2">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="py-2"><span class="text-muted fw-light">Reports / </span>Day Book</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 mb-4 cards">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 inputesPaddingReport">
                            <label for="DATEFROM" class="form-label">DATE FROM</label>
                            <input type="text" class="form-control formInputsReport" placeholder="YYYY-MM-DD" id="start_date" name="start_date" value="{{ Session::get('currentdate') }}" />
                        </div>
                        <div class="col-lg-2 col-md-3 col-sm-4 col-6 py-3 inputesPaddingReport">
                            <label for="DATETO" class="form-label">DATE TO</label>
                            <input type="text" class="form-control formInputsReport" placeholder="YYYY-MM-DD" id="end_date" name="end_date" />
                        </div>

                        <div class="col-lg-7 col-md-4 col-12  py-2 saving_column inputesPaddingReport reportBtnInput">
                            <div class="d-flex justify-content-end text-end mt-4 reportBtnCustom">
                                <button type="button" class="btn btn-primary waves-effect waves-light reportSmallBtnCustom" id="viewdatabooksdetails">
                                    View
                                </button>
                                <a type="button" href="{{route('dayBookPrint.print')}}" target="_blank" class="ms-2 btn btn-primary print-button reportSmallBtnCustom">
                                    Print
                                </a>
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
                </div>
            </div>
        </div>
    </div>
