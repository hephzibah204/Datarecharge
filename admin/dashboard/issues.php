<div class="row">
    <div class="col-12">
        <div class="box">
            <div class="box-header with-border d-flex align-items-center justify-content-between">
                <h4 class="box-title">Customer Issues</h4>
                <div class="d-flex align-items-center justify-content-end">
                    <a class="ml-3 btn btn-success btn-sm btn-rounded text-white" href="notification-home">
                        <i class="fa fa-home" aria-hidden="true"></i> Back
                    </a>
                </div>
            </div>
            <div class="box-body">
                <table id="example1" class="table table-sm table-bordered table-striped">
                    <tbody>
                        <?php 
                        if (!empty($data = $controller->getQueries())) {
                            foreach ($data as $list) { 
                        ?>
                        <tr>
                            <td class="text-center" style="white-space: normal;">
                                <p><b class="<?php echo ($list['admin_read'] == 0) ? 'text-danger' : 'text-success'; ?>">
                                    <?php echo ($list['admin_read'] == 0) ? 'Unread' : 'Read'; ?>
                                </b></p>
                                <p>
                                    <strong>Message:</strong> <?php echo $list['query']; ?>
                                </p>
                                <p>
                                    <a href="support?id=<?php echo $list['id']; ?>" class="btn btn-sm btn-primary btn-rounded">
                                        <i class="fa fa-eye"></i> View
                                    </a>
                                </p>
                            </td>
                        </tr>
                        <?php 
                            }
                        } else { 
                        ?>
                        <tr>
                            <td colspan="4" class="text-center text-danger">No Message To Display</td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>