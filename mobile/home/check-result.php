<div class="page-content header-clear-medium">
        
        <div class="card card-style">
            <div class="content mb-0">
                <p class="mb-0 text-center font-600 color-highlight">Check Results</p>
                <h1 class="text-center">WAEC / NECO Results</h1>
                <p class="text-center font-12">Use your purchased PIN to check results online</p>
            </div>
        </div>

        <div class="card card-style">
            <div class="content">
                <h4 class="font-600 mb-3">How To Check Your Results</h4>
                
                <div class="mb-3">
                    <h5 class="font-600 color-highlight">WAEC Result</h5>
                    <p class="mb-1">1. Visit <a href="https://www.waecdirect.org" target="_blank" class="color-highlight">www.waecdirect.org</a></p>
                    <p class="mb-1">2. Enter your Examination Number</p>
                    <p class="mb-1">3. Select Examination Year</p>
                    <p class="mb-1">4. Enter your WAEC PIN</p>
                    <p class="mb-1">5. Click "Submit" to view your result</p>
                    <p class="mb-0">6. Print or save as PDF</p>
                </div>

                <hr/>

                <div>
                    <h5 class="font-600 color-highlight">NECO Result</h5>
                    <p class="mb-1">1. Visit <a href="https://result.neco.gov.ng" target="_blank" class="color-highlight">result.neco.gov.ng</a></p>
                    <p class="mb-1">2. Enter your Examination Number</p>
                    <p class="mb-1">3. Select Examination Year</p>
                    <p class="mb-1">4. Enter your NECO PIN</p>
                    <p class="mb-1">5. Click "Check Result" to view</p>
                    <p class="mb-0">6. Print or save as PDF</p>
                </div>
            </div>
        </div>

        <div class="card card-style">
            <div class="content">
                <h4 class="font-600 mb-3">Your Purchased Exam Pins</h4>
                <?php if(isset($data[0]) && is_array($data[0]) && count($data[0]) > 0): ?>
                    <div class="list-group">
                    <?php foreach($data[0] AS $pin): ?>
                        <div class="d-flex border-bottom pb-2 mb-2">
                            <div class="flex-grow-1">
                                <strong class="color-highlight"><?php echo htmlspecialchars($pin->servicename ?? 'Exam Pin'); ?></strong>
                                <p class="mb-0 font-12"><?php echo htmlspecialchars($pin->servicedesc ?? ''); ?></p>
                                <p class="mb-0 font-11 text-muted">Date: <?php echo htmlspecialchars($pin->date ?? ''); ?></p>
                            </div>
                            <div class="text-right">
                                <span class="font-14 font-600">₦<?php echo number_format($pin->amount ?? 0, 2); ?></span>
                                <br/>
                                <span class="badge <?php echo ($pin->status ?? 1) == 1 ? 'bg-green-dark' : 'bg-red-dark'; ?>">
                                    <?php echo ($pin->status ?? 1) == 1 ? 'Success' : 'Failed'; ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fa fa-search font-40 opacity-30 mb-2"></i>
                        <p class="mb-0">No exam pin transactions found.</p>
                        <a href="exam-pins" class="btn btn-m font-600 gradient-highlight mt-3 rounded-s">Purchase Exam Pins</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card card-style">
            <div class="content">
                <h4 class="font-600 mb-3">Quick Links</h4>
                <div class="row">
                    <div class="col-6">
                        <a href="exam-pins" class="btn btn-full btn-s rounded-s font-600 gradient-highlight mb-2">
                            <i class="fa fa-shopping-cart mr-1"></i> Buy Pins
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="https://www.waecdirect.org" target="_blank" class="btn btn-full btn-s rounded-s font-600 bg-blue-dark mb-2">
                            <i class="fa fa-external-link mr-1"></i> WAEC Portal
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="https://result.neco.gov.ng" target="_blank" class="btn btn-full btn-s rounded-s font-600 bg-green-dark mb-2">
                            <i class="fa fa-external-link mr-1"></i> NECO Portal
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="https://www.jamb.gov.ng" target="_blank" class="btn btn-full btn-s rounded-s font-600 bg-teal-dark mb-2">
                            <i class="fa fa-external-link mr-1"></i> JAMB Portal
                        </a>
                    </div>
                </div>
            </div>
        </div>

</div>
