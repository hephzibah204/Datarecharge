
    <div class="page-content header-clear-medium">

        <div class="card card-style">
            
            <div class="content">
                <p class="mb-0 text-center font-600 color-highlight">SMS For All Network</p>
                <h1 class="text-center">Send Bulk SMS</h1>


                <form method="post" id="bulkSMSForm" action="send-bulk-sms.php">
                    <fieldset>
<hr/>
                <div class="d-flex">
                    <h5 style="background:<?php echo $sitecolor; ?>; color:#ffffff; padding:9px;  margin-right:5px;">Info: </h5>
                    <marquee direction="left" scrollamount="5" style="background:#f2f2f2; padding:3px; border-radius:5rem;">
                        <h5 class="py-2">
                        Type or Paste up to 10,000 phone numbers here (080... or 23480...) separate with comma ,NO SPACES!
                        </h5>
                    </marquee>
                </div>
                <hr/>
                <div class="input-style input-style-always-active has-borders mb-4">
                            <label for="phoneNumbers" class="color-theme opacity-80 font-700 font-12  ">Sender Name</label>
                            <textarea id="phoneNumbers" name="phoneNumbers" placeholder="Must not exceed 12 characters" class="round-small" rows="1" maxlength="12" required></textarea>
                        </div>
                        
                        <div class="input-style input-style-always-active has-borders mb-4">
                            <label for="phoneNumbers" class="color-theme opacity-80 font-700 font-12 rows="80" ">Phone Numbers</label>
                            <textarea id="phoneNumbers" name="phoneNumbers" placeholder="Enter phone numbers here seperated by a comma" class="round-small" rows="14" required></textarea>
                        </div>

                        <div class="input-style input-style-always-active has-borders mb-4">
                            <label for="message" class="color-theme opacity-80 font-700 font-12">Message</label>
                            <textarea id="message" name="message" placeholder="Enter your message here" class="round-small" rows="6" required></textarea>
                        </div>
                        
<div class="input-style input-style-always-active has-borders validate-field mb-4">
                                <label for="amounttopay" class="color-theme opacity-80 font-700 font-12">Amount To Pay</label>
                                <input type="number" name="amounttopay" placeholder="Amount To Pay" value="" class="round-small" id="amounttopay" readonly required  />
                            </div>

                        <!-- Add any other relevant options or settings for sending SMS here -->

                        <div class="form-button">
                            <button type="submit" id="btnsubmit" name="proceedBtn" style="width: 100%;" class="btn btn-full btn-l font-600 font-15 gradient-highlight mt-4 rounded-s">
                                Proceed
                            </button>
                        </div>
                    </fieldset>
                </form>
            </div>

        </div>

    </div>
   <!-- Add the following script inside the <head> tag of the HTML document -->

<script>
    // Send Bulk SMS Form Handler
    // ----------------------------------------------------------------------------
    // This handler calculates the amount to pay based on the number of phone numbers and the message length.
    $("#phoneNumbers, #message").on("keyup", function () {
        const phoneNumberInput = $("#phoneNumbers").val();
        const messageInput = $("#message").val();
        const phoneNumberCount = phoneNumberInput.split(",").length;

        // Assuming each phone number costs $0.10 and each character in the message costs $0.01
        const phoneNumberCost = phoneNumberCount * 0.10;
        const messageCost = messageInput.length * 0.01;
        const totalAmountToPay = phoneNumberCost + messageCost;

        $("#amounttopay").val(totalAmountToPay.toFixed(2));
    });

    // Submit the form
    $("#bulkSMSForm").submit(function (e) {
        // Add any additional handling logic before form submission if needed
        // For example, displaying a confirmation prompt before sending the bulk SMS
        // e.preventDefault(); // Uncomment this line to prevent the form from submitting automatically
    });
</script>
