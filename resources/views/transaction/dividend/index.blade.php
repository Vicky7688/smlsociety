@extends('layouts.app')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="card page_headings cards mb-4">
    <div class="card-body py-2">
      <div class="row justify-content-between align-items-center">
        <div class="col-md-6 fdHeading">
          <h4 class="py-2"><span class="text-muted fw-light">Transactions / </span>Bank Fixed Deposit</h4>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-12 mb-4 cards">
      <div class="card">
        <div class="card-body cardsY">
          <form action="javascript:void(0)" id="formData" name="formData">
            @csrf
            <div class="nav-align-top rdCustom">
              <div class="tab-content tableContent fdTabContent mt-2">
                <div class="tab-pane fade active show" id="fdDetails" role="tabpanel"> @csrf

                  <div class="row">
                    <div class="col-lg-4 col-md-4 col-sm-4 col-6 mt-2">
                      <label  class="form-label">Upto Date</label>
                      <input type="text" id="fddate" name="fddate" class="form-control form-control-sm "   value="{{ Session::get('currentdate') }}" />
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4 col-6 mt-2">
                      <label  class="form-label">Paid Date</label>
                      <input type="text" id="fddate" name="fddate" class="form-control form-control-sm "  value="{{ date('d-m-Y') }}" />
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4 col-6 mt-2">
                      <label  class="form-label">Days Before</label>
                      <input type="text" id="fdnumber" name="fdnumber" class="form-control form-control-sm "  />
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4 col-6 mt-2">
                      <label  class="form-label">Dividend</label>
                      <input type="text" id="fdnumber" name="fdnumber" class="form-control form-control-sm "  />
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4 col-6 mt-2">
                      <label  class="form-label">Min</label>
                      <input type="text" id="fdnumber" name="fdnumber" class="form-control form-control-sm "  />
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4 col-6 mt-2">
                      <label  class="form-label">Max</label>
                      <input type="text" id="fdnumber" name="fdnumber" class="form-control form-control-sm "  />
                    </div>




                    <div class="col-lg-4 col-md-4 col-sm-4 col-6 mt-2 d-flex align-items-end mt-4">
                          <button type="submit" id="submitButton" class="btn btn-primary waves-effect waves-light">Save</button>

                    </div>
                  </div>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>


<script>
    $(document).ready(function () {
        // Handle form submission
        $('#formData').on('submit', function (e) {
            e.preventDefault(); // Prevent the form from submitting via the browser

            var formData = $(this).serialize(); // Serialize the form data

            $.ajax({
                url: "{{ route('fd.bank.index') }}", // The route where the form will be submitted
                type: "POST", // The HTTP method to use
                data: formData, // The serialized form data
                success: function (response) {
                    // On success, show a success message or perform other actions
                    alert("Form submitted successfully!");

                    // Reset the form
                    $('#formData')[0].reset();

                    // Optionally, reset select2 elements if you're using select2
                    $('.select21').val('').trigger('change');
                },
                error: function (response) {
                    // On error, show an error message or perform other actions
                    alert("There was an error submitting the form. Please try again.");
                }
            });
        });
    });
</script>


<script>
    function calculateMaturityAmount() {
        var type = $("#intresttype").val();
        var interest = parseFloat($("#intrestrate").val()) || 0;
        var year = parseFloat($("#period").val()) || 0;
        var days = parseFloat($("#days").val()) || 0;
        var amount = parseFloat($("#amount").val()) || 0;
        var maturityAmt = amount; // Default to the amount if any value is missing

        // If any of the necessary inputs are missing, just return the amount
        if (!type || interest === 0 || amount === 0) {
            $("#maturityamount").val(amount);
            return;
        }

        if (type === 'Simple Interest') {
            var totalDays = year * 365 + days;
            var interestAmount = (amount * interest * totalDays) / 36500;
            maturityAmt = amount + interestAmount;
        } else if (type === 'Quarterly Interest') {
            maturityAmt = amount;
            for (var i = 1; i <= year * 4; i++) {
                maturityAmt *= (interest / 4 + 100) / 100;
            }
        } else if (type === 'Yearly Interest') {
            maturityAmt = amount;
            for (var i = 1; i <= year; i++) {
                maturityAmt *= (interest + 100) / 100;
            }
            var additionalInterest = (amount * interest * days) / 36500;
            maturityAmt += additionalInterest;
        }

        $("#maturityamount").val(Math.round(maturityAmt));
    }

    $("#days, #period, #amount, #intrestrate").on('keyup', calculateMaturityAmount);
    $("#intresttype").on('change', calculateMaturityAmount);
</script>

@endsection
