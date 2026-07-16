<?php

class ProviderController extends Controller{

    protected $model;

    public function __construct(){
        if(isset($_SESSION['sysId']) && isset($_SESSION["sysRole"])){
            if($_SESSION['sysId']!='' && $_SESSION["sysRole"] !=''){
                $this->model=new ProviderModel;
            }
            else{ header("Location: ../"); exit();}
        }
        else{ header("Location: ../"); exit();}
    }

    public function handleProviderRequest($action,$id=null){
        extract($_POST);
        switch($action){
            case 'add':
                return $this->addProvider();
            case 'edit':
                return $this->updateProvider($id);
            case 'delete':
                return $this->deleteProvider($id);
            case 'toggle_status':
                return $this->toggleProviderStatus($id);
            case 'add_pricing':
                return $this->addPricing();
            case 'update_pricing':
                return $this->updatePricing();
            case 'delete_pricing':
                return $this->deletePricing($id);
            case 'add_override':
                return $this->addPriceOverride();
            case 'delete_override':
                return $this->deletePriceOverride($id);
            default:
                return ['status'=>'error','message'=>'Unknown action'];
        }
    }

    public function displayProvidersList(){
        $providers = $this->model->getProviders();
        if(!$providers || count($providers) == 0){
            return '
            <div class="card">
                <div class="card-body text-center">
                    <h5>No Providers Found</h5>
                    <a href="?action=add" class="btn btn-primary mt-2">Add Your First Provider</a>
                </div>
            </div>';
        }

        $html = '
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Service Providers</h5>
                <a href="?action=add" class="btn btn-primary btn-sm">+ Add New Provider</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Priority</th>
                                <th>Actions</th>
                                <th>Status</th>
                                <th>Last Check</th>
                                <th>Manage</th>
                            </tr>
                        </thead>
                        <tbody>';

        foreach($providers as $i=>$p){
            $i++;
            $statusBadge = $p->is_active
                ? '<span class="badge badge-success">Active</span>'
                : '<span class="badge badge-danger">Inactive</span>';
            $lastCheck = $p->last_check ? $this->formatDate($p->last_check) : 'Never';
            $typeBadge = '<span class="badge badge-info">'.ucfirst($p->type).'</span>';

            $html .= "
            <tr>
                <td>$i</td>
                <td><b>{$p->name}</b>" . ($p->display_name ? "<br><small class='text-muted'>{$p->display_name}</small>" : "") . "</td>
                <td>$typeBadge</td>
                <td>{$p->priority}</td>
                <td>
                    <a href='?action=edit&id={$p->id}' class='btn btn-warning btn-sm' title='Edit'>Edit</a>
                    <form method='post' style='display:inline' onsubmit='return confirm(\"Delete {$p->name}?\")'>
                        <input type='hidden' name='action' value='delete'>
                        <input type='hidden' name='id' value='{$p->id}'>
                        <button type='submit' class='btn btn-danger btn-sm'>Delete</button>
                    </form>
                    <form method='post' style='display:inline'>
                        <input type='hidden' name='action' value='toggle_status'>
                        <input type='hidden' name='id' value='{$p->id}'>
                        <button type='submit' class='btn btn-secondary btn-sm'>Toggle</button>
                    </form>
                </td>
                <td>$statusBadge</td>
                <td><small>$lastCheck</small></td>
                <td><a href='?action=view&id={$p->id}' class='btn btn-info btn-sm'>View</a></td>
            </tr>";
        }

        $html .= '
                        </tbody>
                    </table>
                </div>
            </div>
        </div>';

        return $html;
    }

    public function displayProviderForm($id=null){
        $p = $id ? $this->model->getProviderById($id) : null;
        $action = $p ? 'edit' : 'add';
        $title = $p ? 'Edit Provider: '.$p->name : 'Add New Provider';
        $idField = $p ? '<input type="hidden" name="id" value="'.$p->id.'">' : '';

        $name = $p ? $p->name : '';
        $type = $p ? $p->type : 'dataverify';
        $displayName = $p ? $p->display_name : '';
        $description = $p ? $p->description : '';
        $endpointUrl = $p ? $p->endpoint_url : '';
        $apiKey = $p ? $p->api_key : '';
        $apiVersion = $p ? $p->api_version : '1.0';
        $timeout = $p ? $p->timeout : 30;
        $supportedActions = $p ? $p->supported_actions : '';
        $priority = $p ? $p->priority : 0;
        $isActive = $p ? ($p->is_active ? 'checked' : '') : 'checked';
        $jsFile = $p ? $p->js_file : '';
        $externalScript = $p ? $p->external_script : '';
        $startDate = $p && $p->start_date != '1970-01-01' ? $p->start_date : '';
        $endDate = $p ? $p->end_date : '';
        $config = $p ? $p->config : '';

        $types = ['dataverify','airtime','data','cabletv','electricity','exam','bvn'];
        $typeOptions = '';
        foreach($types as $t){
            $sel = ($type == $t) ? 'selected' : '';
            $typeOptions .= "<option value=\"$t\" $sel>".ucfirst($t)."</option>";
        }

        return '
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">'.$title.'</h5>
                <a href="?action=list" class="btn btn-secondary btn-sm">Back to List</a>
            </div>
            <div class="card-body">
                <form method="post">
                    '.$idField.'
                    <input type="hidden" name="action" value="'.$action.'">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Provider Name</label>
                                <input type="text" name="name" class="form-control" value="'.$name.'" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Type</label>
                                <select name="type" class="form-control">'.$typeOptions.'</select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Display Name</label>
                        <input type="text" name="display_name" class="form-control" value="'.$displayName.'">
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="2">'.$description.'</textarea>
                    </div>

                    <hr>
                    <h6>API Configuration</h6>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>Endpoint URL</label>
                                <input type="url" name="endpoint_url" class="form-control" value="'.$endpointUrl.'">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>API Version</label>
                                <input type="text" name="api_version" class="form-control" value="'.$apiVersion.'">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>API Key</label>
                                <input type="text" name="api_key" class="form-control" value="'.$apiKey.'">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Timeout (seconds)</label>
                                <input type="number" name="timeout" class="form-control" value="'.$timeout.'">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Supported Actions (comma-separated)</label>
                        <input type="text" name="supported_actions" class="form-control" value="'.$supportedActions.'" placeholder="e.g. vtu,share_and_sell,sme,gifting">
                    </div>

                    <hr>
                    <h6>Advanced Settings</h6>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Priority</label>
                                <input type="number" name="priority" class="form-control" value="'.$priority.'">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check mt-4">
                                <input type="checkbox" name="is_active" class="form-check-input" value="1" '.$isActive.'>
                                <label class="form-check-label">Active</label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>JS File</label>
                                <input type="text" name="js_file" class="form-control" value="'.$jsFile.'">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>External Script</label>
                                <input type="text" name="external_script" class="form-control" value="'.$externalScript.'">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Start Date</label>
                                <input type="date" name="start_date" class="form-control" value="'.$startDate.'">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>End Date</label>
                                <input type="date" name="end_date" class="form-control" value="'.$endDate.'">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>JSON Config</label>
                        <textarea name="config" class="form-control" rows="4">'.$config.'</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Save Provider</button>
                    <a href="?action=list" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>';
    }

    public function displayProvidersView($id){
        $p = $this->model->getProviderById($id);
        if(!$p){
            return '<div class="alert alert-danger">Provider not found</div>';
        }

        $statusBadge = $p->is_active
            ? '<span class="badge badge-success">Active</span>'
            : '<span class="badge badge-danger">Inactive</span>';
        $lastCheck = $p->last_check ? $this->formatDate($p->last_check) : 'Never';
        $config = $p->config ? '<pre>'.json_encode(json_decode($p->config), JSON_PRETTY_PRINT).'</pre>' : '<em>None</em>';

        $pricing = $this->model->getProviderPricing($id);
        $pricingHtml = '';
        if($pricing && count($pricing) > 0){
            $pricingHtml .= '
            <div class="table-responsive mt-3">
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>Service Type</th>
                            <th>Base Fee</th>
                            <th>Cost Price</th>
                            <th>Plan</th>
                            <th>Duration</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>';
            foreach($pricing as $pr){
                $pStatus = $pr->is_active
                    ? '<span class="badge badge-success">Active</span>'
                    : '<span class="badge badge-danger">Inactive</span>';
                $pricingHtml .= "
                <tr>
                    <td><code>{$pr->service_type}</code></td>
                    <td>N".number_format($pr->base_fee,2)."</td>
                    <td>N".number_format($pr->cost_price,2)."</td>
                    <td>{$pr->plan_name}</td>
                    <td>{$pr->plan_duration}</td>
                    <td>$pStatus</td>
                    <td>
                        <form method='post' style='display:inline' onsubmit='return confirm(\"Delete this pricing?\")'>
                            <input type='hidden' name='action' value='delete_pricing'>
                            <input type='hidden' name='id' value='{$pr->id}'>
                            <button type='submit' class='btn btn-danger btn-sm'>Delete</button>
                        </form>
                    </td>
                </tr>";
            }
            $pricingHtml .= '
                    </tbody>
                </table>
            </div>';
        } else {
            $pricingHtml = '<p class="text-muted">No pricing configured yet.</p>';
        }

        $overrides = $this->model->getPriceOverrides($id,'');
        $overrideHtml = '';
        if($overrides && count($overrides) > 0){
            $overrideHtml .= '
            <div class="table-responsive mt-3">
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>Service Type</th>
                            <th>User Type</th>
                            <th>Override Fee</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>';
            foreach($overrides as $o){
                $overrideHtml .= "
                <tr>
                    <td><code>{$o->service_type}</code></td>
                    <td><span class='badge badge-warning'>{$o->user_type}</span></td>
                    <td>N".number_format($o->override_fee,2)."</td>
                    <td>
                        <form method='post' style='display:inline' onsubmit='return confirm(\"Delete this override?\")'>
                            <input type='hidden' name='action' value='delete_override'>
                            <input type='hidden' name='id' value='{$o->id}'>
                            <button type='submit' class='btn btn-danger btn-sm'>Delete</button>
                        </form>
                    </td>
                </tr>";
            }
            $overrideHtml .= '
                    </tbody>
                </table>
            </div>';
        } else {
            $overrideHtml = '<p class="text-muted">No price overrides configured.</p>';
        }

        $stats = $this->model->getPricingStats($id);

        return '
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">'.$p->name.' <small class="text-muted">('.ucfirst($p->type).')</small></h5>
                <div>
                    <a href="?action=edit&id='.$id.'" class="btn btn-warning btn-sm">Edit</a>
                    <a href="?action=list" class="btn btn-secondary btn-sm">Back</a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr><th>Status:</th><td>'.$statusBadge.'</td></tr>
                            <tr><th>Display Name:</th><td>'.($p->display_name ?? 'N/A').'</td></tr>
                            <tr><th>Priority:</th><td>'.$p->priority.'</td></tr>
                            <tr><th>Last Check:</th><td>'.$lastCheck.'</td></tr>
                            <tr><th>Pricing Plans:</th><td>'.$stats->total.' ('.$stats->active.' active)</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr><th>Endpoint:</th><td><code>'.($p->endpoint_url ?: 'N/A').'</code></td></tr>
                            <tr><th>API Version:</th><td>'.$p->api_version.'</td></tr>
                            <tr><th>Timeout:</th><td>'.$p->timeout.'s</td></tr>
                            <tr><th>Actions:</th><td><code>'.$p->supported_actions.'</code></td></tr>
                        </table>
                    </div>
                </div>

                <hr>
                <h6>Description</h6>
                <p>'.($p->description ?: 'No description.').'</p>

                <hr>
                <h6>JSON Configuration</h6>
                '.$config.'

                <hr>
                <h6>Pricing Plans</h6>
                '.$pricingHtml.'

                <hr>
                <h6>Price Overrides</h6>
                '.$overrideHtml.'
            </div>
        </div>';
    }

    //------------------------------------------------------------------
    // Internal CRUD handlers
    //------------------------------------------------------------------

    private function addProvider(){
        extract($_POST);
        $name = $name ?? '';

        $check=$this->model->addProvider(
            $name,$type ?? 'dataverify',$display_name ?? '',$description ?? '',
            $endpoint_url ?? '','','1.0',30,$supported_actions ?? '',
            (int)($priority ?? 0), isset($is_active) ? 1 : 0,
            $js_file ?? '',$external_script ?? '',
            $start_date ?? '1970-01-01',$end_date ?? null,
            $config ?? null
        );
        if($check == 0){return ['status'=>'success','message'=>'Provider "'.$name.'" added successfully.'];}
        elseif($check == 2){return ['status'=>'error','message'=>'Provider with this name and type already exists.'];}
        else{return ['status'=>'error','message'=>'Unable to add provider. Please try again.'];}
    }

    private function updateProvider($id){
        extract($_POST);
        $id = (int) $id;
        $name = $name ?? '';

        $check=$this->model->updateProvider(
            $id,$name,$type ?? 'dataverify',$display_name ?? '',$description ?? '',
            $endpoint_url ?? '','','1.0',30,$supported_actions ?? '',
            (int)($priority ?? 0), isset($is_active) ? 1 : 0,
            $js_file ?? '',$external_script ?? '',
            $start_date ?? '1970-01-01',$end_date ?? null,
            $config ?? null
        );
        if($check == 0){return ['status'=>'success','message'=>'Provider "'.$name.'" updated successfully.'];}
        else{return ['status'=>'error','message'=>'Unable to update provider. Please try again.'];}
    }

    private function deleteProvider($id){
        $id = (int) $id;
        $check=$this->model->deleteProvider($id);
        if($check == 0){return ['status'=>'success','message'=>'Provider deleted successfully.'];}
        else{return ['status'=>'error','message'=>'Unable to delete provider.'];}
    }

    private function toggleProviderStatus($id){
        $id = (int) $id;
        $check=$this->model->toggleProviderStatus($id);
        if($check == 0){return ['status'=>'success','message'=>'Provider status toggled successfully.'];}
        else{return ['status'=>'error','message'=>'Unable to toggle provider status.'];}
    }

    private function addPricing(){
        extract($_POST);
        $check=$this->model->addPricing(
            (int)$provider_id,$service_type,$base_fee,$cost_price ?? 0,
            $plan_name ?? '',$plan_id ?? '',$plan_duration ?? '',
            (int)($network_id ?? 0), (int)($is_percentage ?? 0), 1,
            'NGN',1.00,0.00,0.00
        );
        if($check == 0){return ['status'=>'success','message'=>'Pricing added successfully.'];}
        elseif($check == 2){return ['status'=>'error','message'=>'Pricing for this service type already exists.'];}
        else{return ['status'=>'error','message'=>'Unable to add pricing.'];}
    }

    private function updatePricing(){
        extract($_POST);
        $check=$this->model->updatePricing((int)$id,$base_fee,$cost_price ?? 0, isset($is_active) ? 1 : 0);
        if($check == 0){return ['status'=>'success','message'=>'Pricing updated successfully.'];}
        else{return ['status'=>'error','message'=>'Unable to update pricing.'];}
    }

    private function deletePricing($id){
        $check=$this->model->deletePricing((int)$id);
        if($check == 0){return ['status'=>'success','message'=>'Pricing deleted successfully.'];}
        else{return ['status'=>'error','message'=>'Unable to delete pricing.'];}
    }

    private function addPriceOverride(){
        extract($_POST);
        $check=$this->model->addPriceOverride(
            (int)$provider_id,$service_type,$user_type,$override_fee,1,
            '1970-01-01',null
        );
        if($check == 0){return ['status'=>'success','message'=>'Price override added successfully.'];}
        elseif($check == 2){return ['status'=>'error','message'=>'Override for this service type and user type already exists.'];}
        else{return ['status'=>'error','message'=>'Unable to add price override.'];}
    }

    private function deletePriceOverride($id){
        $check=$this->model->deletePriceOverride((int)$id);
        if($check == 0){return ['status'=>'success','message'=>'Price override deleted successfully.'];}
        else{return ['status'=>'error','message'=>'Unable to delete price override.'];}
    }

}
