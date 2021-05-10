<?php


namespace App\Service;


class RecetteFilters
{
    /**
     * @param $params
     * @param $keyFilters
     * @param array $default
     * @return array
     */
    public function getOrderBy($params, $keyFilters, $default = []): array
    {
        $orderBy = [];

        // On vérifie que les clés et les valeurs des paramètres orderBy sont valides
        if(isset($params["orderBy"]))
        {
            foreach ($params["orderBy"] as $key => $value)
            {
                if(in_array($key, $keyFilters) && in_array(strtoupper($value), ["ASC","DESC"]))
                {
                    $orderBy[$key] = $value;
                }
            }
        }

        if (empty($orderBy)) {
            $orderBy = $default;
        }

        return $orderBy;
    }

    /**
     * @param $params
     * @return array|null
     */
    public function getQuery($params) : ?array
    {
        $query = isset($params["query"]) ? strtolower($params["query"]) : null;

        if ($query != null)
        {
            $query = explode(" ", $query);
        }

        return $query;
    }

    /**
     * @param $params
     * @return int
     */
    public function getPage($params): int
    {
        return (isset($params["page"]) && $params["page"] >=0) ? (int)$params["page"] : 1;
    }

    /**
     * @param $params
     * @param int $default
     * @return int
     */
    public function getLimit($params, $default = 3): int
    {
        return (isset($params["limit"]) && $params["limit"] >=0) ? (int)$params["limit"] : $default;
    }

}