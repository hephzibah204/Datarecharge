<div class="page-content header-clear-medium">
    <div class="card card-style">
        <div class="content">
            <p class="mb-0 text-center font-600 color-highlight">Voter's Card</p>
            <h1 class="text-center">Voter Card Verification</h1>
            <p class="text-center font-12">Verify Permanent Voter's Card (PVC) details</p>
        </div>
    </div>

    <div class="card card-style">
        <div class="content">
            <form method="post" action="vin">
                <fieldset>
                    <div class="input-style input-style-always-active has-borders validate-field mb-4">
                        <label class="color-theme opacity-80 font-700 font-12">VIN (Voter Identification Number)</label>
                        <input type="text" name="vin" placeholder="Enter VIN from your PVC" class="round-small" required />
                    </div>

                    <input name="transkey" id="transkey" type="hidden" />

                    <div class="form-button">
                        <button type="submit" name="verify-vin" style="width: 100%;" class="btn btn-full btn-l font-600 font-15 gradient-highlight mt-4 rounded-s">
                            <i class="fa fa-search mr-1"></i> Verify Now
                        </button>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>

    <div class="card card-style">
        <div class="content">
            <h4 class="font-600 mb-2">About Voter Card Verification</h4>
            <p class="font-12 mb-0">Verify the details of a Permanent Voter's Card (PVC) using the Voter Identification Number (VIN). Get voter name, date of birth, polling unit, and registration details from INEC database.</p>
        </div>
    </div>
</div>
