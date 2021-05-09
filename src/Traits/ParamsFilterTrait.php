<?php


namespace App\Traits;


trait ParamsFilterTrait
{
    public function applyFilter($keysFilter, $paramsURL, $default){
        if($paramsURL != null && isset($paramsURL["order"]) && in_array(key($paramsURL["order"]), $keysFilter))
        {
            $keyOrder = key($paramsURL["order"]);
            if($paramsURL["order"][$keyOrder] == "desc" || $paramsURL["order"][$keyOrder] == "asc")
            {
                return  $paramsURL["order"];
            }

        }
        return $default;
    }
}
