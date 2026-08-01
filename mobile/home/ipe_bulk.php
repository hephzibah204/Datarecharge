<div class="page-content header-clear-medium">
    <div class="card card-style">
        <div class="content mb-0">
            <p class="mb-0 text-center font-600 color-highlight">NIN Services</p>
            <h1 class="text-center">IPE Clearance (Bulk)</h1>
            <p class="text-center mb-3">Process multiple IPE clearance requests</p>
        </div>
    </div>

    <div class="card card-style">
        <div class="content">
            <form method="post" id="ipeForm">
                <fieldset>
                    <div class="input-style input-style-always-active has-borders validate-field mb-4">
                        <label class="color-theme opacity-80 font-700 font-12">Enter Tracking ID(s) (One per line)</label>
                        <textarea name="tracking_ids" id="trackingTextarea" placeholder="TRK123456789&#10;TRK987654321" class="round-small" rows="8" required></textarea>
                        <div class="mt-2 text-right">
                            <small class="color-highlight font-600" id="trackingCount">0 Tracking ID(s) entered</small>
                        </div>
                    </div>

                    <div class="input-style input-style-always-active has-borders validate-field mb-4">
                        <label class="color-theme opacity-80 font-700 font-12">Price Per Tracking ID</label>
                        <input type="text" id="pricePerTracking" class="round-small" value="₦<?php echo number_format($siteSettings->fee_ipe_clearance ?? 1000); ?>" readonly>
                        <input type="hidden" id="pricePerTrackingInput" name="price_per_tracking" value="<?php echo $siteSettings->fee_ipe_clearance ?? 1000; ?>">
                    </div>

                    <div class="input-style input-style-always-active has-borders validate-field mb-4">
                        <label class="color-theme opacity-80 font-700 font-12">Total Amount</label>
                        <input type="text" id="totalAmount" name="total_amount" class="round-small" readonly>
                    </div>

                    <div class="input-style input-style-always-active has-borders validate-field mb-4">
                        <label class="color-theme opacity-80 font-700 font-12">Transaction PIN</label>
                        <input type="number" name="transkey" placeholder="Enter Transaction PIN" class="round-small" required>
                    </div>

                    <input name="transref" type="hidden" value="<?php echo $transRef; ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">

                    <div class="form-button">
                        <button type="submit" name="submit-ipe-clearance" class="btn btn-info btn-lg btn-block">
                            Submit IPE Request
                        </button>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#trackingTextarea').on('input', function() {
        updateCount();
    });

    function updateCount() {
        var text = $('#trackingTextarea').val();
        var lines = text.split('\n');
        var validIds = [];

        lines.forEach(function(line) {
            var trk = line.trim();
            if (trk !== '' && !validIds.includes(trk)) {
                validIds.push(trk);
            }
        });

        var count = validIds.length;
        $('#trackingCount').text(count + ' valid Tracking ID(s) entered');

        var price = parseFloat($('#pricePerTrackingInput').val()) || 1000;
        var total = count * price;
        $('#totalAmount').val('₦' + total.toLocaleString());
        window.validTrackingIds = validIds;
    }

    setTimeout(updateCount, 100);

    $('#ipeForm').on('submit', function(e) {
        var count = window.validTrackingIds ? window.validTrackingIds.length : 0;
        if (count === 0) {
            e.preventDefault();
            swal("Error!", "Please enter at least one Tracking ID", "error");
            return false;
        }
        $(this).append('<input type="hidden" name="tracking_list" value="' + window.validTrackingIds.join(',') + '">');
        $('.btn-info').removeClass('gradient-highlight').addClass('btn-secondary').html('<i class="fa fa-spinner fa-spin"></i> Processing...');
    });
});
</script>