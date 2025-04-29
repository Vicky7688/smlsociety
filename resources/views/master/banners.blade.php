@extends('layouts.app')
@section('title', "Slider Statement")
@section('pagetitle', "Slider Statement")

@php
$table = "yes";
$export = "wallet";
@endphp

@section('content')
<style>
    .display-none{
        display:none;
    }
</style>
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-sm-12">
            <h4 class="py-3 mb-4"><span class="text-muted fw-light">Dashboard /</span> Slider
            </h4>
            <div class="card card-action mb-5">
                <div class="card-header">
                    <div class="card-action-title">Slidet</div>
                    <div class="card-action-element">
                        <ul class="list-inline mb-0">
                            <li class="list-inline-item">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#basicModal">
                                    Add Slider
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div class="card-datatable table-responsive">
                        <table class="table datatables-order table border-top" id="datatable" style="width:100%">
                            <thead class="thead-light">
                                <tr>
                                    <th class="w-17">#</th>
                                    <th>Name</th>
                                    <th>Image</th>
                                    <th>Status</th>
                                    <th>Action</th>    
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="basicModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1"> Add Slider </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="categoryManager" action="{{route('masterupdate')}}" method="post">
                {{ csrf_field() }}
                <input type="hidden" name="actiontype" value="slider" />
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="title" class="form-label">Name</label>
                            <input type="text" name="title" id="title" class="form-control" placeholder="Enter Name" />
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label">Slider Image</label>
                        <input name="image" class="form-control" type="file" id="image" />
                    </div>
        
                 <div class="col-md-6">
               <label for="link_by">Image Preview:</label>
               <br><br>
               <img src="{{ url('images/sliderpreview.png') }}" class="img-responsive postop" id="slider_preview" title="Image Preview" align="center">
               
             </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                        Close
                    </button>

                    <button id="submitButton" class="btn btn-primary waves-effect waves-light" type="submit" data-loading-text=" <span class='spinner-border me-1' role='status' aria-hidden='true'></span>
                        Loading...">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('style')
  <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/sweetalert2@7.12.15/dist/sweetalert2.min.css'>
@endpush

@push('script')
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@7.12.15/dist/sweetalert2.all.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {

        var url = "{{url('statement/fetch')}}/sliderstatement/{{$id}}";
        var onDraw = function() {
            
          $('input.statusHandler').on('click', function(evt) {
                evt.stopPropagation();
                var ele = $(this);
                var id = $(this).val();
                var status = "inactive";
                if ($(this).prop('checked')) {
                    status = "active";
                }

                $.ajax({
                        url: `{{ route('masterupdate') }}`,
                        type: 'post',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType: 'json',
                        data: {
                            'id': id,
                            'status': status,
                            "actiontype": "sliderstatus"
                        }
                    })
                    .done(function(data) {
                        if (data.status == "success") {
                            notify("Status Updated", 'success');
                            $('#datatable').dataTable().api().ajax.reload();
                        } else {
                            if (status == "active") {
                                ele.prop('checked', false);
                            } else {
                                ele.prop('checked', true);
                            }
                            notify("Something went wrong, Try again.", 'warning');
                        }
                    })
                    .fail(function(errors) {
                        if (status == "active") {
                            ele.prop('checked', false);
                        } else {
                            ele.prop('checked', true);
                        }
                        showError(errors, "withoutform");
                    });
            });    
            
        };
        var options = [{
                "data": "name",
                render: function(data, type, full, meta) {
                    return `<div><span class='text-inverse m-l-10'><b>` +
                        full.id +
                        `</b> </span><div class="clearfix"></div></div><span style='font-size:13px' class="pull=right">` +
                        full.created_at + `</span>`;
                }
            },
            {
                "data": "title"
            },
            {
                "data": "image",
                render: function(data, type, full, meta) {
                    return `<a href="` + full.image +
                        `" target="_blank"><img src="` + full.image +
                        `" width="100px" height="50px"></a>`;
                }
            },
              {
                "data": "status",
                render: function(data, type, full, meta) {
                    var check = "";
                    if (full.status == "active") {
                        check = "checked='checked'";
                    }

                    return `   <label class="switch">
                          <input type="checkbox" class="switch-input statusHandler" id="status_${full.id}" ${check} value="` + full.id + `" actionType="` + type + `">
                          <span class="switch-toggle-slider">
                            <span class="switch-on">
                              <i class="ti ti-check"></i>
                            </span>
                            <span class="switch-off">
                              <i class="ti ti-x"></i>
                            </span>
                          </span>
                       
                        </label>`;
                }
            },
             {
                "data": "action",
                render: function(data, type, full, meta) {
                    return `<button type="button" class="btn btn-primary" onclick="deleteSlide('` + full.id + `')"> Delete</button>`;
                }
            }
        ];

        datatableSetup(url, options, onDraw, '#datatable', {
            columnDefs: [{
                orderable: false,
                width: '80px',
                targets: [0]
            }]
        });

        $("#categoryManager").validate({
            rules: {
                categoryName: {
                    required: true,
                },
                description: {
                    required: true,
                }
            },
            messages: {
                categoryName: {
                    required: "Please enter value",
                },
                description: {
                    required: "Please enter value",
                }
            },
            errorElement: "p",
            errorPlacement: function(error, element) {
                if (element.prop("tagName").toLowerCase() === "select") {
                    error.insertAfter(element.closest(".form-group").find(".select2"));
                } else {
                    error.insertAfter(element);
                }
            },
            submitHandler: function() {
                var form = $('#categoryManager');
                var id = form.find('[name="id"]').val();
                form.ajaxSubmit({
                    dataType: 'json',
                    beforeSubmit: function() {
                        form.find('button[type="submit"]').html(
                            '<span class="spinner-border me-1" role="status" aria-hidden="true"></span> Loading...'
                        ).attr(
                            'disabled', true).addClass('btn-secondary');
                    },
                    success: function(data) {
                        if (data.status == "success") {
                            form[0].reset();
                            form.find('button[type="submit"]').html('Submit').attr(
                                'disabled', false).removeClass('btn-secondary');

                            notify("Task Successfully Completed", 'success');
                            $('#datatable').dataTable().api().ajax.reload();
                            $('#basicModal').modal('hide');
                        } else {
                            notify(data.status, 'warning');
                        }
                    },
                    error: function(errors) {
                        showError(errors, form);
                    }
                });
            }
        });
    });
    
    "use strict";
// Define your library strictly...
function readURL1(input) {
  if(input.files && input.files[0]) {
    var reader = new FileReader();
    reader.onload = function(e) {
      $('#slider_preview').css({
        'width': "80%",
        'height': "80%"
      });
      $('#slider_preview').attr('src', e.target.result);
    }
    reader.readAsDataURL(input.files[0]);
  }
}
$("#image").on('change', function() {
  readURL1(this);
});
$('#link_by').on('change', function() {
  var v = $(this).val();
  console.log(v) ;
  if(v == 'category') {
    $('#category_id').show();
    $('#pro').hide();
  }else{
       $('#category_id').hide();
       $('#pro').show();
  }
});
  
   function deleteSlide(id) {
        $.ajax({
                url: '{{route("masterupdate")}}',
                type: 'post',
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    "id": id,
                    "actiontype": "sliderDelete"
                },
                beforeSend: function() {
                    swal({
                        title: 'Wait!',
                        text: 'Please wait, we are deleting slides',
                        onOpen: () => {
                            swal.showLoading()
                        },
                        allowOutsideClick: () => !swal.isLoading()
                    });
                },
                success  : function(data) { 
                      swal.close();
                    if(data.status == "success"){
                    $('#datatable').dataTable().api().ajax.reload();
                         swal({
                                    type: 'success',
                                    title: 'Success',
                                    text: "Data Successfully Deleted",
                                    showConfirmButton: true,
                                });
                    }else{
                          swal({
                                    type: 'error',
                                    title: 'Failed',
                                    text: "Something went wrong",
                                    showConfirmButton: true,
                                    onClose: () => {
                                        form[0].reset();
                                    },
                                });
                             
                    }
                  
                },
                error    : function() { 
                       swal.close();
                        notify('Somthing went wrong', 'warning');
                    },
                complete : function() { 
                    
                  
                }
            })
    }
</script>
@endpush