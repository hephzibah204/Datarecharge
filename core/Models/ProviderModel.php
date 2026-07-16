<?php

class ProviderModel extends Model{

    //------------------------------------------------------------------
    // Provider CRUD
    //------------------------------------------------------------------

    public function getProviders(){
        $dbh=$this->connect();
        $sql = "SELECT * FROM providers ORDER BY priority DESC, name ASC";
        $query = $dbh->prepare($sql);
        $query->execute();
        $results=$query->fetchAll(PDO::FETCH_OBJ);
        return $results;
    }

    public function getProviderById($id){
        $id=(int) $id;
        $dbh=$this->connect();
        $sql = "SELECT * FROM providers WHERE id=$id";
        $query = $dbh->prepare($sql);
        $query->execute();
        $result=$query->fetch(PDO::FETCH_OBJ);
        return $result;
    }

    public function getActiveProviders(){
        $dbh=$this->connect();
        $sql = "SELECT * FROM providers WHERE is_active=1 ORDER BY priority DESC, name ASC";
        $query = $dbh->prepare($sql);
        $query->execute();
        $results=$query->fetchAll(PDO::FETCH_OBJ);
        return $results;
    }

    public function getProvidersByType($type){
        $dbh=$this->connect();
        $sql = "SELECT * FROM providers WHERE type=:t AND is_active=1 ORDER BY priority DESC";
        $query = $dbh->prepare($sql);
        $query->bindParam(':t',$type,PDO::PARAM_STR);
        $query->execute();
        $results=$query->fetchAll(PDO::FETCH_OBJ);
        return $results;
    }

    public function addProvider($name,$type,$display_name,$description,$endpoint_url,$api_key,$api_version,$timeout,$supported_actions,$priority,$is_active,$js_file,$external_script,$start_date,$end_date,$config){
        $dbh=$this->connect();

        $queryC=$dbh->prepare("SELECT id FROM providers WHERE name=:n AND type=:t");
        $queryC->bindParam(':n',$name,PDO::PARAM_STR);
        $queryC->bindParam(':t',$type,PDO::PARAM_STR);
        $queryC->execute();
        if($queryC->fetch()){return 2;}

        $sql="INSERT INTO providers (name,type,display_name,description,endpoint_url,api_key,api_version,timeout,supported_actions,priority,is_active,js_file,external_script,start_date,end_date,config)
        VALUES(:n,:t,:dn,:d,:eu,:ak,:av,:to,:sa,:pr,:ia,:jf,:es,:sd,:ed,:cf)";
        $query = $dbh->prepare($sql);
        $query->bindParam(':n',$name,PDO::PARAM_STR);
        $query->bindParam(':t',$type,PDO::PARAM_STR);
        $query->bindParam(':dn',$display_name,PDO::PARAM_STR);
        $query->bindParam(':d',$description,PDO::PARAM_STR);
        $query->bindParam(':eu',$endpoint_url,PDO::PARAM_STR);
        $query->bindParam(':ak',$api_key,PDO::PARAM_STR);
        $query->bindParam(':av',$api_version,PDO::PARAM_STR);
        $query->bindParam(':to',$timeout,PDO::PARAM_INT);
        $query->bindParam(':sa',$supported_actions,PDO::PARAM_STR);
        $query->bindParam(':pr',$priority,PDO::PARAM_INT);
        $query->bindParam(':ia',$is_active,PDO::PARAM_INT);
        $query->bindParam(':jf',$js_file,PDO::PARAM_STR);
        $query->bindParam(':es',$external_script,PDO::PARAM_STR);
        $query->bindParam(':sd',$start_date,PDO::PARAM_STR);
        $query->bindParam(':ed',$end_date,PDO::PARAM_STR);
        $query->bindParam(':cf',$config,PDO::PARAM_STR);
        $query->execute();
        $lastInsertId = $dbh->lastInsertId();
        if($lastInsertId){return 0;} else{return 1;}
    }

    public function updateProvider($id,$name,$type,$display_name,$description,$endpoint_url,$api_key,$api_version,$timeout,$supported_actions,$priority,$is_active,$js_file,$external_script,$start_date,$end_date,$config){
        $dbh=$this->connect();
        $id=(int) $id;

        $sql="UPDATE providers SET
            name=:n,type=:t,display_name=:dn,description=:d,
            endpoint_url=:eu,api_key=:ak,api_version=:av,timeout=:to,
            supported_actions=:sa,priority=:pr,is_active=:ia,
            js_file=:jf,external_script=:es,start_date=:sd,end_date=:ed,config=:cf
        WHERE id=$id";
        $query = $dbh->prepare($sql);
        $query->bindParam(':n',$name,PDO::PARAM_STR);
        $query->bindParam(':t',$type,PDO::PARAM_STR);
        $query->bindParam(':dn',$display_name,PDO::PARAM_STR);
        $query->bindParam(':d',$description,PDO::PARAM_STR);
        $query->bindParam(':eu',$endpoint_url,PDO::PARAM_STR);
        $query->bindParam(':ak',$api_key,PDO::PARAM_STR);
        $query->bindParam(':av',$api_version,PDO::PARAM_STR);
        $query->bindParam(':to',$timeout,PDO::PARAM_INT);
        $query->bindParam(':sa',$supported_actions,PDO::PARAM_STR);
        $query->bindParam(':pr',$priority,PDO::PARAM_INT);
        $query->bindParam(':ia',$is_active,PDO::PARAM_INT);
        $query->bindParam(':jf',$js_file,PDO::PARAM_STR);
        $query->bindParam(':es',$external_script,PDO::PARAM_STR);
        $query->bindParam(':sd',$start_date,PDO::PARAM_STR);
        $query->bindParam(':ed',$end_date,PDO::PARAM_STR);
        $query->bindParam(':cf',$config,PDO::PARAM_STR);
        if($query->execute()){return 0;} else{return 1;}
    }

    public function deleteProvider($id){
        $dbh=$this->connect();
        $id=(int) $id;
        $sql = "DELETE FROM providers WHERE id=$id";
        $query = $dbh->prepare($sql);
        $query->execute();
        return 0;
    }

    public function toggleProviderStatus($id){
        $dbh=$this->connect();
        $id=(int) $id;
        $current = $this->getProviderById($id);
        if(!$current){return 1;}
        $newStatus = $current->is_active ? 0 : 1;
        $sql="UPDATE providers SET is_active=$newStatus WHERE id=$id";
        $query = $dbh->prepare($sql);
        if($query->execute()){return 0;} else{return 1;}
    }

    //------------------------------------------------------------------
    // Provider Pricing
    //------------------------------------------------------------------

    public function getProviderPricing($providerId){
        $dbh=$this->connect();
        $providerId=(int) $providerId;
        $sql = "SELECT * FROM provider_pricing WHERE provider_id=$providerId ORDER BY service_type ASC";
        $query = $dbh->prepare($sql);
        $query->execute();
        $results=$query->fetchAll(PDO::FETCH_OBJ);
        return $results;
    }

    public function getPricingByServiceType($providerId,$serviceType){
        $dbh=$this->connect();
        $providerId=(int) $providerId;
        $sql = "SELECT * FROM provider_pricing WHERE provider_id=$providerId AND service_type=:st";
        $query = $dbh->prepare($sql);
        $query->bindParam(':st',$serviceType,PDO::PARAM_STR);
        $query->execute();
        $result=$query->fetch(PDO::FETCH_OBJ);
        return $result;
    }

    public function addPricing($providerId,$serviceType,$baseFee,$costPrice,$planName,$planId,$planDuration,$networkId,$isPercentage,$isActive,$currency,$urgencyMultiplier,$priorityFee,$maxDiscount){
        $dbh=$this->connect();

        $queryC=$dbh->prepare("SELECT id FROM provider_pricing WHERE provider_id=:pi AND service_type=:st");
        $queryC->bindParam(':pi',$providerId,PDO::PARAM_INT);
        $queryC->bindParam(':st',$serviceType,PDO::PARAM_STR);
        $queryC->execute();
        if($queryC->fetch()){return 2;}

        $sql="INSERT INTO provider_pricing (provider_id,service_type,base_fee,cost_price,plan_name,plan_id,plan_duration,network_id,is_percentage,is_active,currency,urgency_multiplier,priority_fee,max_discount)
        VALUES(:pi,:st,:bf,:cp,:pn,:pii,:pd,:ni,:ip,:ia,:cu,:um,:pf,:md)";
        $query = $dbh->prepare($sql);
        $query->bindParam(':pi',$providerId,PDO::PARAM_INT);
        $query->bindParam(':st',$serviceType,PDO::PARAM_STR);
        $query->bindParam(':bf',$baseFee,PDO::PARAM_STR);
        $query->bindParam(':cp',$costPrice,PDO::PARAM_STR);
        $query->bindParam(':pn',$planName,PDO::PARAM_STR);
        $query->bindParam(':pii',$planId,PDO::PARAM_STR);
        $query->bindParam(':pd',$planDuration,PDO::PARAM_STR);
        $query->bindParam(':ni',$networkId,PDO::PARAM_INT);
        $query->bindParam(':ip',$isPercentage,PDO::PARAM_INT);
        $query->bindParam(':ia',$isActive,PDO::PARAM_INT);
        $query->bindParam(':cu',$currency,PDO::PARAM_STR);
        $query->bindParam(':um',$urgencyMultiplier,PDO::PARAM_STR);
        $query->bindParam(':pf',$priorityFee,PDO::PARAM_STR);
        $query->bindParam(':md',$maxDiscount,PDO::PARAM_STR);
        $query->execute();
        $lastInsertId = $dbh->lastInsertId();
        if($lastInsertId){return 0;} else{return 1;}
    }

    public function updatePricing($id,$baseFee,$costPrice,$isActive){
        $dbh=$this->connect();
        $id=(int) $id;
        $sql="UPDATE provider_pricing SET base_fee=:bf,cost_price=:cp,is_active=:ia WHERE id=$id";
        $query = $dbh->prepare($sql);
        $query->bindParam(':bf',$baseFee,PDO::PARAM_STR);
        $query->bindParam(':cp',$costPrice,PDO::PARAM_STR);
        $query->bindParam(':ia',$isActive,PDO::PARAM_INT);
        if($query->execute()){return 0;} else{return 1;}
    }

    public function deletePricing($id){
        $dbh=$this->connect();
        $id=(int) $id;
        $sql = "DELETE FROM provider_pricing WHERE id=$id";
        $query = $dbh->prepare($sql);
        $query->execute();
        return 0;
    }

    //------------------------------------------------------------------
    // Price Overrides
    //------------------------------------------------------------------

    public function getPriceOverrides($providerId,$serviceType=null){
        $dbh=$this->connect();
        $providerId=(int) $providerId;
        if($serviceType){
            $sql = "SELECT * FROM price_overrides WHERE provider_id=$providerId AND service_type=:st ORDER BY user_type ASC";
            $query = $dbh->prepare($sql);
            $query->bindParam(':st',$serviceType,PDO::PARAM_STR);
        } else {
            $sql = "SELECT * FROM price_overrides WHERE provider_id=$providerId ORDER BY service_type, user_type ASC";
            $query = $dbh->prepare($sql);
        }
        $query->execute();
        $results=$query->fetchAll(PDO::FETCH_OBJ);
        return $results;
    }

    public function addPriceOverride($providerId,$serviceType,$userType,$overrideFee,$isActive,$startDate,$endDate){
        $dbh=$this->connect();

        $queryC=$dbh->prepare("SELECT id FROM price_overrides WHERE provider_id=:pi AND service_type=:st AND user_type=:ut");
        $queryC->bindParam(':pi',$providerId,PDO::PARAM_INT);
        $queryC->bindParam(':st',$serviceType,PDO::PARAM_STR);
        $queryC->bindParam(':ut',$userType,PDO::PARAM_STR);
        $queryC->execute();
        if($queryC->fetch()){return 2;}

        $sql="INSERT INTO price_overrides (provider_id,service_type,user_type,override_fee,is_active,start_date,end_date)
        VALUES(:pi,:st,:ut,:of,:ia,:sd,:ed)";
        $query = $dbh->prepare($sql);
        $query->bindParam(':pi',$providerId,PDO::PARAM_INT);
        $query->bindParam(':st',$serviceType,PDO::PARAM_STR);
        $query->bindParam(':ut',$userType,PDO::PARAM_STR);
        $query->bindParam(':of',$overrideFee,PDO::PARAM_STR);
        $query->bindParam(':ia',$isActive,PDO::PARAM_INT);
        $query->bindParam(':sd',$startDate,PDO::PARAM_STR);
        $query->bindParam(':ed',$endDate,PDO::PARAM_STR);
        $query->execute();
        $lastInsertId = $dbh->lastInsertId();
        if($lastInsertId){return 0;} else{return 1;}
    }

    public function deletePriceOverride($id){
        $dbh=$this->connect();
        $id=(int) $id;
        $sql = "DELETE FROM price_overrides WHERE id=$id";
        $query = $dbh->prepare($sql);
        $query->execute();
        return 0;
    }

    //------------------------------------------------------------------
    // Provider Logs
    //------------------------------------------------------------------

    public function getProviderLogs($providerId,$limit=50){
        $dbh=$this->connect();
        $providerId=(int) $providerId;
        $limit=(int) $limit;
        $sql = "SELECT * FROM provider_logs WHERE provider_id=$providerId ORDER BY created_at DESC LIMIT $limit";
        $query = $dbh->prepare($sql);
        $query->execute();
        $results=$query->fetchAll(PDO::FETCH_OBJ);
        return $results;
    }

    //------------------------------------------------------------------
    // Utility
    //------------------------------------------------------------------

    public function getProviderStats(){
        $dbh=$this->connect();
        $sql = "SELECT
            COUNT(*) AS total_providers,
            SUM(CASE WHEN is_active=1 THEN 1 ELSE 0 END) AS active_providers,
            SUM(CASE WHEN is_active=0 THEN 1 ELSE 0 END) AS inactive_providers
        FROM providers";
        $query = $dbh->prepare($sql);
        $query->execute();
        $result=$query->fetch(PDO::FETCH_OBJ);
        return $result;
    }

    public function getPricingStats($providerId){
        $dbh=$this->connect();
        $providerId=(int) $providerId;
        $sql = "SELECT COUNT(*) AS total, SUM(CASE WHEN is_active=1 THEN 1 ELSE 0 END) AS active FROM provider_pricing WHERE provider_id=$providerId";
        $query = $dbh->prepare($sql);
        $query->execute();
        $result=$query->fetch(PDO::FETCH_OBJ);
        return $result;
    }

}
