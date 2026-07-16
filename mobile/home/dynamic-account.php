<div class="page-content header-clear-medium bg-light">
    <div class="container mt-3">
        <div class="">
            <p class="mb-2 text-dark font-600 font-16">
                <p class="mb-2 text-danger font-600 font-15">Get A Dynamic Wema Account. </p>
                <p class="mb-2"><b>Note: </b> The dynamic account is a TEMPORARY account for funding, can only be used ONE TIME. </p>
            </p>
        </div>

        <?php $generatedAccountNumber = $controller->monnifyDynamic(); ?>
        <form method="post">
            <div class="mt-5 mb-3">
                <div class="input-style has-borders no-icon input-style-always-active mb-4">
                    <input type="number" class="form-control" id="amount" name="amount" placeholder="Amount" required>
                    <label for="amount" class="color-highlight">Amount</label>
                    <em>(required)</em>
                </div>
        
            </div>
            <button type="submit" name="fund-with-dynamic" style="width: 100%;" class="btn btn-full btn-l font-600 font-15 gradient-highlight mt-4 rounded-s" id="dynamic" onclick="$('#dynamic').removeClass('btn-primary'); $('#dynamic').addClass('btn-secondary'); $('#dynamic').html('<i class=\'fa fa-spinner fa-spin\'></i> Processing ...'); $('#gtbankform').submit();">
                Pay Now
            </button>
        </form> <hr/>

        <?php if (!empty($generatedAccountNumber)): ?>
            <div class="account-details mt-5">
                <p class="mb-2 text-dark font-600 font-16"><b>Bank Name: </b>Wema Bank </p>
                <p class="mb-2 text-dark font-600 font-16"><b>Account No: </b><?php echo $generatedAccountNumber; ?></p>
                <p class="mb-2"><b>Note: </b> Do not save this account as beneficiary, can only be used ONE TIME.</p>
                <button class="btn btn-primary font-700 rounded-xl mt-3" onclick="copyToClipboard('<?php echo $generatedAccountNumber; ?>')">Copy Account No</button>
            </div><hr/>
        <?php endif; ?>
    </div>
</div>
