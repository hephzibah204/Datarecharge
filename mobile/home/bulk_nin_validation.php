<div class="page-content header-clear-medium">
    <div class="card card-style">
        <div class="content mb-0">
            <p class="mb-0 text-center font-600 color-highlight">NIN Services</p>
            <h1 class="text-center">Bulk NIN Validation</h1>
            <p class="text-center mb-3">Validate multiple NINs at once</p>
        </div>
    </div>

    <div class="card card-style">
        <div class="content">
            <form method="post" id="bulkNinForm">
                <fieldset>
                    <div class="input-style input-style-always-active has-borders validate-field mb-4">
                        <label class="color-theme opacity-80 font-700 font-12">Validation Type</label>
                        <select name="validation_type" class="form-control" required onchange="updatePrice()">
                            <option value="">Select validation type</option>
                            <option value="standard" data-price="<?php echo $siteSettings->fee_bulk_validation ?? 500; ?>">Standard Validation - ₦<?php echo number_format($siteSettings->fee_bulk_validation ?? 500); ?>/NIN</option>
                            <option value="premium" data-price="<?php echo ($siteSettings->fee_bulk_validation ?? 500) * 2; ?>">Premium Validation - ₦<?php echo number_format(($siteSettings->fee_bulk_validation ?? 500) * 2); ?>/NIN</option>
                        </select>
                    </div>

                    <div class="input-style input-style-always-active has-borders validate-field mb-4">
                        <label class="color-theme opacity-80 font-700 font-12">Enter NINs (One per line, max 30)</label>
                        <textarea name="nins" id="ninsTextarea" placeholder="12345678901&#10;12345678902&#10;12345678903" class="round-small" rows="8" required></textarea>
                        <div class="mt-2 text-right">
                            <small class="color-highlight font-600" id="ninCount">0 NINs entered</small>
                        </div>
                    </div>

                    <div class="input-style input-style-always-active has-borders validate-field mb-4">
                        <label class="color-theme opacity-80 font-700 font-12">Price Per NIN</label>
                        <input type="text" id="pricePerNin" name="price_per_nin" class="round-small" readonly>
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
                    <input name="modification_type_code" type="hidden" value="bulk_nin_validation">

                    <div class="form-button">
                        <button type="submit" name="submit-bulk-nin-validation" class="btn btn-info btn-lg btn-block">
                            Submit Validation Request
                        </button>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>

    <div class="card card-style">
        <div class="content">
            <h4 class="font-600 mb-2">Validation Rules</h4>
            <ul class="font-12 mb-0">
                <li>Enter 1 to 30 NINs, one per line</li>
                <li>Each NIN must be exactly 11 digits</li>
                <li>Duplicate NINs will be automatically removed</li>
                <li>Empty lines will be ignored</li>
                <li>Price is calculated automatically based on valid NIN count</li>
                <li>Wallet balance must cover total amount</li>
            </ul>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Update NIN count and price on input
    $('#ninsTextarea').on('input', function() {
        updateNinCount();
        updatePrice();
    });

    $('#ninsTextarea').on('paste', function() {
        setTimeout(function() {
            updateNinCount();
            updatePrice();
        }, 100);
    });

    function updateNinCount() {
        var text = $('#ninsTextarea').val();
        var lines = text.split('\n');
        var validNins = [];
        var invalidLines = [];

        lines.forEach(function(line, index) {
            var nin = line.trim();
            if (nin === '') return;
            if (/^\d{11}$/.test(nin)) {
                if (!validNins.includes(nin)) {
                    validNins.push(nin);
                }
            } else {
                invalidLines.push(index + 1);
            }
        });

        var count = validNins.length;
        $('#ninCount').text(count + ' valid NIN(s) entered' + (invalidLines.length > 0 ? ' | ' + invalidLines.length + ' invalid line(s)' : ''));

        if (count > 30) {
            $('#ninCount').addClass('text-danger').text('Maximum 30 NINs allowed (' + count + ' entered)');
        } else {
            $('#ninCount').removeClass('text-danger');
        }

        // Store valid NINs for validation
        window.validNins = validNins;
    }

    function updatePrice() {
        var count = window.validNins ? window.validNins.length : 0;
        if (count > 30) count = 30;

        var pricePerNin = parseFloat($('#pricePerNinInput').val()) || 0;
        var total = count * pricePerNin;

        $('#totalAmount').val('₦' + total.toLocaleString());
    }

    // Set initial price per NIN from selected option
    $('select[name="validation_type"]').on('change', function() {
        var selectedOption = $(this).find('option:selected');
        var price = selectedOption.data('price') || 0;
        $('#pricePerNinInput').val(price);
        $('#pricePerNin').val('₦' + price.toLocaleString());
        updatePrice();
    });

    // Hidden input to store price per NIN
    $('form').prepend('<input type="hidden" id="pricePerNinInput" name="price_per_nin" value="0">');

    // Initial price update
    setTimeout(function() {
        updateNinCount();
        updatePrice();
    }, 100);

    // Form validation
    $('#bulkNinForm').on('submit', function(e) {
        var count = window.validNins ? window.validNins.length : 0;
        if (count === 0) {
            e.preventDefault();
            swal("Error!", "Please enter at least one valid NIN", "error");
            return false;
        }
        if (count > 30) {
            e.preventDefault();
            swal("Error!", "Maximum 30 NINs allowed", "error");
            return false;
        }
        
        // Add valid NINs to form
        $(this).append('<input type="hidden" name="nins[]" value="' + window.validNins.join(',') + '">');
        
        // Show loading
        $('.btn-info').removeClass('gradient-highlight').addClass('btn-secondary').html('<i class="fa fa-spinner fa-spin"></i> Processing...');
    });
});
</script>