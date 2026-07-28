<div class="page-content header-clear-medium">
    <div class="card card-style">
        <div class="content mb-0">
            <p class="mb-0 text-center font-600 color-highlight">NIN Profile Management</p>
            <h1 class="text-center">NIN Modification</h1>
            <p class="text-center mb-3">Select what you want to modify</p>
        </div>
    </div>

    <div class="row mb-0">
        <div class="col-6 mb-3">
            <a href="name_modification" class="card card-style d-block">
                <div class="content text-center py-3">
                    <i class="fa fa-user-edit font-30 color-highlight mb-2"></i>
                    <h4 class="font-14">Name</h4>
                    <p class="font-11 opacity-60 mb-0">Change your name on NIN</p>
                </div>
            </a>
        </div>
        <div class="col-6 mb-3">
            <a href="dob_modification" class="card card-style d-block">
                <div class="content text-center py-3">
                    <i class="fa fa-birthday-cake font-30 color-highlight mb-2"></i>
                    <h4 class="font-14">Date of Birth</h4>
                    <p class="font-11 opacity-60 mb-0">Update your DOB</p>
                </div>
            </a>
        </div>
        <div class="col-6 mb-3">
            <a href="phone_modification" class="card card-style d-block">
                <div class="content text-center py-3">
                    <i class="fa fa-phone-alt font-30 color-highlight mb-2"></i>
                    <h4 class="font-14">Phone Number</h4>
                    <p class="font-11 opacity-60 mb-0">Change registered phone</p>
                </div>
            </a>
        </div>
        <div class="col-6 mb-3">
            <a href="address_modification" class="card card-style d-block">
                <div class="content text-center py-3">
                    <i class="fa fa-home font-30 color-highlight mb-2"></i>
                    <h4 class="font-14">Address</h4>
                    <p class="font-11 opacity-60 mb-0">Modify your address</p>
                </div>
            </a>
        </div>
    </div>

    <div class="card card-style mt-4">
        <div class="content mb-0 pb-3">
            <h3 class="font-600 mb-3">Modification Request History</h3>
            <?php 
            $modifications = $data[0] ?? []; 
            if (empty($modifications)): 
            ?>
                <div class="text-center py-3">
                    <i class="fa fa-info-circle font-20 color-blue-dark mb-2"></i>
                    <p class="font-12 mb-0 opacity-60">No modification requests found.</p>
                </div>
            <?php else: ?>
                <div class="list-group">
                    <?php foreach ($modifications as $req): ?>
                        <div class="list-group-item border-0 mb-3 px-0 d-flex flex-column" style="border-bottom: 1px solid rgba(0,0,0,0.05) !important;">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="font-700 font-14"><?php echo strtoupper($req->type); ?> Modification</span>
                                <span class="badge font-10 px-2 py-1 rounded-pill bg-<?php 
                                    echo ($req->status === 'approved' || $req->status === 'completed') ? 'success' : 
                                         (($req->status === 'declined' || $req->status === 'cancelled') ? 'danger' : 'warning'); 
                                ?> text-white"><?php echo strtoupper(str_replace('_', ' ', $req->status)); ?></span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center font-11 opacity-60">
                                <span>Ref: <?php echo htmlspecialchars($req->ref); ?></span>
                                <span><?php echo date('M d, Y', strtotime($req->date_created)); ?></span>
                            </div>
                            <?php if (!empty($req->admin_notes)): ?>
                                <div class="mt-2 font-11 p-2 bg-light rounded text-muted">
                                    <strong>Admin Notes:</strong> <?php echo htmlspecialchars($req->admin_notes); ?>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($req->result_document)): ?>
                                <a href="<?php echo htmlspecialchars($req->result_document); ?>" download class="btn btn-xs btn-success mt-2 align-self-start font-11 px-3 py-1">
                                    <i class="fa fa-download mr-1"></i> Download Completed Report
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
