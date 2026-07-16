<div class="page-content header-clear-medium">
    <div class="card card-style">
        <div class="content mb-0">
            <p class="mb-0 text-center font-600 color-highlight">NIN Profile Management</p>
            <h1 class="text-center">Phone Number Modification</h1>
            <p class="text-center mb-3">Request a change to your registered phone number on NIN</p>
        </div>
    </div>
    <div class="card card-style">
        <div class="content">
            <form method="post" action="phone_modification">
                <fieldset>
                    <div class="input-style input-style-always-active has-borders validate-field mb-4">
                        <label class="color-theme opacity-80 font-700 font-12">New Phone Number</label>
                        <input type="tel" name="new_value" placeholder="Enter new phone number" class="round-small" required>
                    </div>
                    <div class="input-style input-style-always-active has-borders validate-field mb-4">
                        <label class="color-theme opacity-80 font-700 font-12">Reason for Change</label>
                        <textarea name="reason" placeholder="Explain why you need this change" class="round-small" rows="3" required></textarea>
                    </div>
                    <div class="input-style input-style-always-active has-borders validate-field mb-4">
                        <label class="color-theme opacity-80 font-700 font-12">Amount</label>
                        <input type="text" value="â‚¦5,000" class="round-small" readonly>
                    </div>
                    <input name="transref" type="hidden" value="<?php echo $transRef; ?>">
                    <input name="transkey" id="transkey" type="hidden">
                    <input name="modification_type_code" type="hidden" value="phone">
                    <div class="form-button">
                        <button type="submit" name="submit-nin-modification" class="btn btn-info btn-lg btn-block">
                            Submit Request
                        </button>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>
</div>
