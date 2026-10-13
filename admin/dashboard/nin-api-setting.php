<?php
require_once __DIR__ . '/../../api/providers/dataverify.php';
$dvKey = $controller->getConfigValue($data[0], "dataVerifyApi") ?: (getenv('PROVIDER_DataVerify_API_KEY') ?: 'DATAVERIFY_9G1UPLC6V4C5UUOD2NVM');
$dvEndpoint = $controller->getConfigValue($data[0], "dataVerifyProvider") ?: (getenv('PROVIDER_DataVerify_ENDPOINT') ?: 'https://dataverify.org');
$dvProvider = new DataVerifyProvider($dvKey, $dvEndpoint);
$dvBalance = null;
try {
    $dvBalance = $dvProvider->getBalance();
} catch (\Throwable $e) {
    $dvBalance = ['status' => false, 'message' => $e->getMessage()];
}
?>

<div class="row">
    <!-- Main NIN Settings -->
    <div class="col-md-7 col-12">
        <div class="box">
            <div class="box-header with-border d-flex align-items-center justify-content-between">
                <h4 class="box-title">NIN API Settings</h4>
                <a class="btn btn-info btn-rounded text-white" href="configurations">
                    <i class="fa fa-plug" aria-hidden="true"></i> Back
                </a>
            </div>
            <div class="box-body">
                <form method="post" class="form-submit">
                    <div class="form-group">
                        <label class="control-label">NIN API Status</label>
                        <div>
                            <select name="ninStatus" class="form-control" required>
                                <option value="">Select Status</option>
                                <?php $ninStatus = $controller->getConfigValue($data[0], "ninStatus"); ?>
                                <option value="On" <?php echo ($ninStatus == "On") ? "selected" : ""; ?>>On</option>
                                <option value="Off" <?php echo ($ninStatus == "Off") ? "selected" : ""; ?>>Off</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label">NIN API Key</label>
                        <div>
                            <input type="password" id="ninApiInput" name="ninApi" value="<?php echo htmlspecialchars($controller->getConfigValue($data[0], "ninApi") ?? ''); ?>" placeholder="NIN API Key / Bearer Token" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label">NIN API URL</label>
                        <div class="input-group">
                            <input type="text" id="ninProviderInput" name="ninProvider" value="<?php echo htmlspecialchars($controller->getConfigValue($data[0], "ninProvider") ?? ''); ?>" placeholder="https://dataverify.org" class="form-control" required>
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-secondary" onclick="presetDataVerify()" title="Use DataVerify endpoint">Use DataVerify</button>
                            </span>
                        </div>
                        <small class="form-text text-muted">Set to <code>https://dataverify.org</code> to route NIN validations & verifications through DataVerify.</small>
                    </div>

                    <hr>
                    <h5 class="box-title font-size-16 mb-3"><i class="fa fa-shield"></i> DataVerify Integration Settings</h5>

                    <div class="form-group">
                        <label class="control-label">DataVerify API Status</label>
                        <div>
                            <select name="dataVerifyStatus" class="form-control">
                                <?php $dvStatus = $controller->getConfigValue($data[0], "dataVerifyStatus") ?: 'On'; ?>
                                <option value="On" <?php echo ($dvStatus == "On") ? "selected" : ""; ?>>On</option>
                                <option value="Off" <?php echo ($dvStatus == "Off") ? "selected" : ""; ?>>Off</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label">DataVerify API Key</label>
                        <div>
                            <input type="text" id="dataVerifyApi" name="dataVerifyApi" value="<?php echo htmlspecialchars($dvKey); ?>" placeholder="DATAVERIFY_..." class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label">DataVerify Endpoint</label>
                        <div>
                            <input type="text" id="dataVerifyProvider" name="dataVerifyProvider" value="<?php echo htmlspecialchars($dvEndpoint); ?>" placeholder="https://dataverify.org" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <div>
                            <button type="submit" name="update-api-config" class="btn btn-info btn-submit">
                                <i class="fa fa-save" aria-hidden="true"></i> Update Details
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Live DataVerify Status & Balance Card -->
    <div class="col-md-5 col-12">
        <div class="box">
            <div class="box-header with-border">
                <h4 class="box-title"><i class="fa fa-wallet text-success"></i> DataVerify Live Status</h4>
            </div>
            <div class="box-body">
                <?php if (!empty($dvBalance['status']) && ($dvBalance['status'] == 1 || $dvBalance['status'] === true || isset($dvBalance['balance']))): ?>
                    <div class="alert alert-success">
                        <i class="fa fa-check-circle"></i> <strong>Connected to DataVerify (dataverify.org)</strong>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered mb-3">
                            <tbody>
                                <tr>
                                    <th>Wallet Balance</th>
                                    <td><span class="badge badge-success font-size-16">₦<?php echo number_format((float)($dvBalance['balance'] ?? $dvBalance['user_balance'] ?? 0), 2); ?></span></td>
                                </tr>
                                <tr>
                                    <th>Account Status</th>
                                    <td><span class="badge badge-info"><?php echo ucfirst($dvBalance['developer_status'] ?? 'Active'); ?></span></td>
                                </tr>
                                <tr>
                                    <th>Account Email</th>
                                    <td><code><?php echo htmlspecialchars($dvBalance['email'] ?? 'N/A'); ?></code></td>
                                </tr>
                                <tr>
                                    <th>Endpoint URL</th>
                                    <td><code><?php echo htmlspecialchars($dvEndpoint); ?></code></td>
                                </tr>
                                <tr>
                                    <th>Last Response</th>
                                    <td><small><?php echo htmlspecialchars($dvBalance['timestamp'] ?? date('Y-m-d H:i:s')); ?></small></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning">
                        <i class="fa fa-exclamation-triangle"></i>
                        <strong>DataVerify Connection:</strong>
                        <p class="mb-0 mt-1"><?php echo htmlspecialchars($dvBalance['message'] ?? 'Could not connect. Verify API key and endpoint.'); ?></p>
                    </div>
                    <p><small>Endpoint tested: <code><?php echo htmlspecialchars($dvEndpoint); ?>/api/developers/balance.php</code></small></p>
                <?php endif; ?>

                <div class="callout callout-info">
                    <h5>Supported DataVerify Actions:</h5>
                    <ul class="pl-3 mb-0">
                        <li><strong>NIN Slips:</strong> By 11-digit NIN, Phone, or Demographics</li>
                        <li><strong>BVN Slips:</strong> By BVN Number & Retrieval by Phone</li>
                        <li><strong>Bank Account Verification:</strong> Account & Name Matching</li>
                        <li><strong>IPE Clearance:</strong> Submit Tracking IDs & Check Status</li>
                        <li><strong>NIN Validation:</strong> No Record Found, SIM, Bank, Modification, Photo</li>
                        <li><strong>Personalization:</strong> Submit & Status Check</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function presetDataVerify() {
    var dvUrl = document.getElementById('dataVerifyProvider').value || 'https://dataverify.org';
    var dvKey = document.getElementById('dataVerifyApi').value || 'DATAVERIFY_9G1UPLC6V4C5UUOD2NVM';
    document.getElementById('ninProviderInput').value = dvUrl;
    document.getElementById('ninApiInput').value = dvKey;
}
</script>
