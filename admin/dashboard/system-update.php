<?php
    $gitInstalled = $data['git_installed'] ?? false;
    $gitVersion = $data['git_version'] ?? 'Unknown';
    $branch = $data['branch'] ?? 'main';
    $remoteUrl = $data['remote_url'] ?? 'Not configured';
    $currentCommit = $data['current_commit'] ?? '';
    $incomingCount = $data['incoming_count'] ?? 0;
    $incomingCommits = $data['incoming_commits'] ?? [];
    $recentCommits = $data['recent_commits'] ?? [];
    $localChanges = $data['local_changes'] ?? '';
    $lastChecked = $data['last_checked'] ?? date("Y-m-d H:i:s");

    $currentParts = !empty($currentCommit) ? explode("|", $currentCommit) : [];
    $currentHash = $currentParts[0] ?? 'N/A';
    $currentSubject = $currentParts[1] ?? 'N/A';
    $currentAuthor = $currentParts[2] ?? 'N/A';
    $currentDate = $currentParts[3] ?? 'N/A';
    $currentRelative = $currentParts[4] ?? '';
?>

<div class="row">
    <div class="col-12">

        <!-- Top summary status cards -->
        <div class="row">
            <div class="col-md-3 col-sm-6 col-12">
                <div class="box box-body">
                    <h6 class="text-uppercase text-muted">Current Version / Hash</h6>
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="font-size-24 font-weight-bold text-primary font-monospace"><?php echo htmlspecialchars($currentHash); ?></span>
                        <i class="fa fa-code-fork font-size-30 text-primary"></i>
                    </div>
                    <small class="text-muted"><?php echo htmlspecialchars($branch); ?> branch</small>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-12">
                <div class="box box-body">
                    <h6 class="text-uppercase text-muted">Update Status</h6>
                    <div class="d-flex align-items-center justify-content-between">
                        <?php if($incomingCount > 0): ?>
                            <span class="font-size-24 font-weight-bold text-danger"><?php echo $incomingCount; ?> New Update<?php echo $incomingCount > 1 ? 's' : ''; ?></span>
                            <i class="fa fa-arrow-circle-down font-size-30 text-danger"></i>
                        <?php else: ?>
                            <span class="font-size-24 font-weight-bold text-success">Up to Date</span>
                            <i class="fa fa-check-circle font-size-30 text-success"></i>
                        <?php endif; ?>
                    </div>
                    <small class="text-muted">Checked <?php echo htmlspecialchars($lastChecked); ?></small>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-12">
                <div class="box box-body">
                    <h6 class="text-uppercase text-muted">Working Tree</h6>
                    <div class="d-flex align-items-center justify-content-between">
                        <?php if(!empty($localChanges)): ?>
                            <span class="font-size-20 font-weight-bold text-warning">Modified</span>
                            <i class="fa fa-exclamation-triangle font-size-30 text-warning"></i>
                        <?php else: ?>
                            <span class="font-size-20 font-weight-bold text-success">Clean</span>
                            <i class="fa fa-shield font-size-30 text-success"></i>
                        <?php endif; ?>
                    </div>
                    <small class="text-muted"><?php echo !empty($localChanges) ? 'Local modified files present' : 'Working directory is clean'; ?></small>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-12">
                <div class="box box-body">
                    <h6 class="text-uppercase text-muted">Environment</h6>
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="font-size-20 font-weight-bold <?php echo $gitInstalled ? 'text-info' : 'text-danger'; ?>">
                            <?php echo $gitInstalled ? 'Git Active' : 'Git Missing'; ?>
                        </span>
                        <i class="fa fa-terminal font-size-30 text-info"></i>
                    </div>
                    <small class="text-muted" title="<?php echo htmlspecialchars($gitVersion); ?>"><?php echo htmlspecialchars(substr($gitVersion, 0, 24)); ?></small>
                </div>
            </div>
        </div>

        <!-- Main Action & Pull Card -->
        <div class="box">
            <div class="box-header with-border d-flex align-items-center justify-content-between">
                <h4 class="box-title"><i class="fa fa-cloud-download text-primary"></i> Pull System Update from Git</h4>
                <div>
                    <a href="system-update" class="btn btn-default btn-sm">
                        <i class="fa fa-refresh"></i> Check for Updates
                    </a>
                </div>
            </div>
            <div class="box-body">
                <div class="callout <?php echo $incomingCount > 0 ? 'callout-warning' : 'callout-info'; ?>" style="margin-bottom: 20px;">
                    <h5><i class="fa fa-info-circle"></i> Repository Details</h5>
                    <p class="mb-1"><strong>Remote URL:</strong> <a href="<?php echo htmlspecialchars($remoteUrl); ?>" target="_blank" rel="noopener"><?php echo htmlspecialchars($remoteUrl); ?></a></p>
                    <p class="mb-1"><strong>Tracking Branch:</strong> <span class="badge badge-info"><?php echo htmlspecialchars($branch); ?></span></p>
                    <p class="mb-0"><strong>Latest Installed Commit:</strong> <code><?php echo htmlspecialchars($currentHash); ?></code> &mdash; <?php echo htmlspecialchars($currentSubject); ?> <small class="text-muted">(by <?php echo htmlspecialchars($currentAuthor); ?>, <?php echo htmlspecialchars($currentRelative ?: $currentDate); ?>)</small></p>
                </div>

                <?php if($incomingCount > 0): ?>
                    <div class="alert alert-warning">
                        <h4><i class="fa fa-bell"></i> <strong><?php echo $incomingCount; ?> New Commit<?php echo $incomingCount > 1 ? 's' : ''; ?> Available!</strong></h4>
                        <p>There are newer commits pushed to the remote repository. Pulling will update your local installation to match origin/<?php echo htmlspecialchars($branch); ?>.</p>
                        
                        <div class="table-responsive mt-3 mb-3 bg-white p-2" style="border-radius: 4px;">
                            <table class="table table-sm table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>Commit</th>
                                        <th>Message</th>
                                        <th>Author</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($incomingCommits as $c): ?>
                                    <tr>
                                        <td><span class="badge badge-primary"><?php echo htmlspecialchars($c['hash']); ?></span></td>
                                        <td><strong><?php echo htmlspecialchars($c['subject']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($c['author']); ?></td>
                                        <td><?php echo htmlspecialchars($c['relative'] ?: $c['date']); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if(!empty($localChanges)): ?>
                    <div class="alert alert-secondary" style="background:#fdf7e3; border-color:#faebcc; color:#8a6d3b;">
                        <strong><i class="fa fa-info-circle"></i> Note on Local Changes:</strong>
                        <pre style="margin-top: 5px; font-size: 12px; max-height: 120px;"><?php echo htmlspecialchars($localChanges); ?></pre>
                        <small>Local uncommitted changes may be merged or cause conflicts if they touch the same files as incoming commits.</small>
                    </div>
                <?php endif; ?>

                <form method="post" id="pullUpdateForm" onsubmit="return confirmUpdate();">
                    <input type="hidden" name="pull-system-update" value="1">
                    <button type="submit" id="pullBtn" class="btn btn-success btn-lg">
                        <i class="fa fa-download"></i> Pull & Apply Update Now
                    </button>
                    <span id="pullSpinner" style="display:none; margin-left: 15px;">
                        <i class="fa fa-spinner fa-spin fa-2x text-primary" style="vertical-align: middle;"></i>
                        <span class="text-primary font-weight-bold" style="vertical-align: middle;"> Pulling updates from GitHub... Please wait...</span>
                    </span>
                </form>
            </div>
        </div>

        <!-- Recent Commits History Table -->
        <div class="box">
            <div class="box-header with-border">
                <h4 class="box-title"><i class="fa fa-history text-muted"></i> Recent Commit History</h4>
            </div>
            <div class="box-body table-responsive no-padding">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th style="width: 100px;">Commit</th>
                            <th>Commit Message</th>
                            <th>Author</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($recentCommits)): foreach($recentCommits as $idx => $commit): ?>
                        <tr <?php echo $idx === 0 ? 'class="bg-light-success font-weight-bold"' : ''; ?>>
                            <td>
                                <span class="badge <?php echo $idx === 0 ? 'badge-success' : 'badge-default'; ?>">
                                    <?php echo htmlspecialchars($commit['hash']); ?>
                                </span>
                                <?php if($idx === 0): ?>
                                    <span class="badge badge-info ml-1">Current</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($commit['subject']); ?></td>
                            <td><?php echo htmlspecialchars($commit['author']); ?></td>
                            <td><span class="text-muted"><?php echo htmlspecialchars($commit['relative'] ?: $commit['date']); ?></span></td>
                        </tr>
                        <?php endforeach; else: ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted">No recent commit history available.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<script>
function confirmUpdate() {
    var conf = confirm("Are you sure you want to pull the latest system updates from Git repository?\n\nThis will execute 'git pull origin <?php echo htmlspecialchars($branch); ?>'.");
    if(conf) {
        document.getElementById('pullBtn').disabled = true;
        document.getElementById('pullBtn').innerHTML = '<i class="fa fa-spinner fa-spin"></i> Processing...';
        document.getElementById('pullSpinner').style.display = 'inline-block';
        return true;
    }
    return false;
}
</script>
