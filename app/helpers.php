<?php
// use DB;
if (! function_exists('convertYmdToMdy')) {
    function hasPermission($id,$objective){
        $hasAcess=DB::select("SELECT $objective FROM CRM.dbo.crm_hasAccess WHERE adminId=".$id)[0]->$objective;
        return $hasAcess;
    }
}
?>