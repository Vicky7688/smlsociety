<script>
$('#submitButtonn').on('click', function () {
        // Check if Dr and Cr are equal before submitting to the database
        var grandTotalDr = 0;
        var grandTotalCr = 0;
        $.each(journalEntries, function (index, entry) {
            var drAmount = (entry.drcr === 'Dr') ? entry.amount : 0;
            var crAmount = (entry.drcr === 'Cr') ? entry.amount : 0;
            grandTotalDr += parseFloat(drAmount);
            grandTotalCr += parseFloat(crAmount);
        });

        // If Dr and Cr are equal, proceed to submit the data to the database
        if (grandTotalDr === grandTotalCr) {
            // Perform your AJAX submission here
            alert('Submitting to the database...');
        } else {
            alert('Dr and Cr amounts are not equal. Please review your entries.');
        }
    });
</script>