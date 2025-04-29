@extends('layouts.app')

@php
$table = "yes";
@endphp

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-md-4">
            <h4 class="py-2"><span class="text-muted fw-light">Transactions / </span>Journal Voucher</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <form action="javascript: void(0)" method="post" id="searchForm" name="formData">
                        @csrf
                        <div class="row">
                            <input type="hidden" name="savingId" id="savingId">
                            <div class="col-md-2 col-12 mb-4">
                                <label for="fromDate" class="form-label">Date From</label>
                                <input type="date" class="form-control" placeholder="YYYY-MM-DD" id="voucherDate" />
                                <p class="error"></p>
                            </div>
                            <div class="col-md-2 col-12 mb-4">
                                <label for="toDate" class="form-label">Date To</label>
                                <input type="date" class="form-control" placeholder="YYYY-MM-DD"
                                value="{{ Session::get('currentdate') }}" id="toDate" />
                                <p class="error"></p>
                            </div>
                            <div class="col-md-2 col-12 mb-4">
                                <label for="voucherId" class="form-label">Voucher</label>
                                <input type="number" step="any" class="form-control" id="voucherId" name="voucherId" />
                                <p class="error"></p>
                            </div>
                            <div class="col-md-2 col-12 mb-4">
                                <label for="amount" class="form-label">Amount</label>
                                <input type="number" step="any" class="form-control" id="amount" name="amount" />
                                <p class="error"></p>
                            </div>

                            <div class="col-md-4 col-12 mt-4">
                                <button type="submit" id="searchButton"
                                    class="btn btn-primary waves-effect waves-light">Search</button>
                                <a href="{{route('journalVoucher.index')}}" type="button"
                                    class="btn btn-primary waves-effect waves-light">New Voucher</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body data_tables">
            <div class="table-responsive tabledata">
                <table class="table text-center table-bordered">
                    <thead>
                        <tr>
                            <th class="fw-bold">Date</th>
                            <th class="fw-bold">Voucher No</th>
                            <th class="fw-bold">Subgroup</th>
                            <th class="fw-bold">Ledger</th>
                            <th class="fw-bold">Amount</th>
                            <th class="fw-bold">Narration</th>
                            <th class="fw-bold">Action</th>
                        </tr>
                    </thead>
                    <tbody id="journalTableBody">
                        @foreach ($journalEntries as $entry)
                        <tr>
                            <td>{{ $entry->voucherDate }}</td>
                            <td>{{ $entry->voucherNo }}</td>
                            <td>{{ isset($groups[$entry->groupCode]) ? $groups[$entry->groupCode] : 'N/A' }}</td>
                            <td>{{ isset($ledgers[$entry->ledgerCode]) ? $ledgers[$entry->ledgerCode] : 'N/A' }}</td>
                            <td>{{ ($entry->transactionType === 'Dr') ? $entry->drAmount : $entry->crAmount }}</td>
                            <td>{{ $entry->narration }}</td>
                            <td>
                                <a type="button"
                                    href="{{ route('voucherPrint.print', ['voucherNo' => $entry->voucherNo]) }}"
                                    class="print-button">
                                    <i class="ti ti-printer me-1"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@push('script')

<script>
document.addEventListener("DOMContentLoaded", function() {
    var fromDateInput = document.getElementById("voucherDate");
    var currentDate = new Date();

    currentDate.setFullYear(currentDate.getFullYear() - 1);
    currentDate.setMonth(4 - 1);
    currentDate.setDate(1);

    var formattedDate = currentDate.toISOString().split('T')[0];

    fromDateInput.value = formattedDate;
});

document.getElementById('searchForm').addEventListener('submit', function(event) {
    event.preventDefault();

    // Get form data
    const formData = new FormData(event.target);
    const voucherDate = formData.get('voucherDate');
    const toDate = formData.get('toDate');
    const voucherId = formData.get('voucherId');
    const amount = formData.get('amount');

    // Filter journalEntries
    let filteredEntries = @json($journalEntries);

    if (voucherDate) {
        filteredEntries = filteredEntries.filter(entry => entry.voucherDate === voucherDate);
    }

    if (toDate) {
        filteredEntries = filteredEntries.filter(entry => entry.voucherDate <= toDate);
    }

    if (voucherId) {
        filteredEntries = filteredEntries.filter(entry => entry.voucherNo === parseInt(voucherId));
    }

    if (amount) {
        filteredEntries = filteredEntries.filter(entry => entry.drAmount === parseFloat(amount) || entry
            .crAmount === parseFloat(amount));
    }

    const tableBody = document.getElementById('journalTableBody');
    tableBody.innerHTML = '';

    const groupNames = @json($groups);
    const ledgerNames = @json($ledgers);

    // Add filtered entries to the table
    filteredEntries.forEach(entry => {
        // Get groupName and ledgerName

        const groupName = groupNames[entry.groupCode] || 'N/A';
        const ledgerName = ledgerNames[entry.ledgerCode] || 'N/A';

        const tableRow = `<tr>
                    <td>${entry.voucherDate}</td>
                    <td>${entry.voucherNo}</td>
                    <td>${groupName}</td>
                    <td>${ledgerName}</td>
                    <td>${entry.drAmount || entry.crAmount}</td>
                    <td>${entry.narration}</td>
                    <td>
                    <a type="button" href="/esociety_GITNEW/Society-/transactions/voucherPrint/${entry.voucherNo}" class="print-button">
                    <i class="ti ti-printer me-1"></i>
                    </a>
                    </td>
                </tr>`;
        tableBody.innerHTML += tableRow;
    });
});

</script>

@endpush
