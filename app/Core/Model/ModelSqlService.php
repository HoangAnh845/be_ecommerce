<?php

/**
 * Created By PhpStorm
 * User: trungphuna
 * Date: 4/4/24
 * Time: 3:36 PM
 */

namespace App\Core\Model;

use App\Core\ConstantGlobal;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ModelSqlService
{
    const LIKE = [];
    const EQUAL = [];
    const PAGE_SIZE_DEFAULT = 20;
    const MAX_PAGE_SIZE = 200;
    const SORT = [];
    const IGNORE = [];
    const IN = [];
    const LIKE_FULL = [];
    const BOOLEAN = [];
    const LIKE_DATE = [];
    const BETWEEN = [];
    const BETWEEN_DATE = [];

    /**
     * Get all
     *
     * @param array $params
     * @param null $query
     * @return mixed
     */
    public static function getAll($params = [], $query = null)
    {
        if (!$query) $query = DB::table(static::getTableName());
        $query = self::setQuery($params, $query);
        $pageSize = $params[ConstantGlobal::PAGE_SIZE] ?? static::PAGE_SIZE_DEFAULT;
        $pageSize = $pageSize > self::MAX_PAGE_SIZE ? self::MAX_PAGE_SIZE : $pageSize;
        return $query->paginate($pageSize);
    }

    /**
     * Get all
     *
     * @param array $params
     * @param null $query
     * @return mixed
     */
    public static function setQuery($params, $query)
    {
        $filters = $params[ConstantGlobal::FILTERS] ?? null;
    
        $sort = $params[ConstantGlobal::SORT] ?? null;
        self::queryList($query, $filters, $sort);
        return $query;
    }

    /**
     * Get all no pagination
     *
     * @param array $params
     * @param null $query
     * @return mixed
     */
    public static function getAllNoPagination($params = [], $query = null)
    {
        $filters = $params[ConstantGlobal::FILTERS] ?? null;
        $sort = $params[ConstantGlobal::SORT] ?? null;

        if (!$query) $query = DB::table(static::getTableName());
        self::queryList($query, $filters, $sort);

        return $query->get();
    }


    /**
     * Get table name
     *
     * @return null
     */
    public static function getTableName()
    {
        return null;
    }

    /**
     * @param $items
     * @param array |null$filters
     * @param string|null $sort
     */
    private static function queryList(&$items, array $filters = null, string $sort = null): void
    {
        if ($filters) {
            foreach ($filters as $key => $value) {
                $continue = in_array($key, static::IGNORE) || $value == "" ||
                    !in_array($key, array_merge(static::LIKE, static::LIKE_FULL, static::EQUAL, static::IN, static::BOOLEAN, static::LIKE_DATE, static::BETWEEN, static::BETWEEN_DATE));

                if ($continue) {
                    continue;
                }

                if (in_array($key, static::LIKE)) {
                    $lowerValue = strtolower($value);
                    $items->whereRaw("LOWER({$key}) like '{$lowerValue}%'");

                    continue;
                }
                if (in_array($key, static::LIKE_FULL)) {
                    $lowerValue = strtolower($value);
                    $items->whereRaw("LOWER({$key}) like '%{$lowerValue}%'");
                    continue;
                }
                if (in_array($key, static::LIKE_DATE)) {
                    $items->whereRaw("CAST({$key} AS VARCHAR) like '{$value}%'");
                    continue;
                }
                if (in_array($key, static::EQUAL)) {
                    $items->where($key, "=", $value);
                    continue;
                }
                if (in_array($key, static::BETWEEN)) {
                    $start = Carbon::parse($value['start'])->startOfDay()->toDateTimeString();
                    $end = Carbon::parse($value['end'])->endOfDay()->toDateTimeString();
                    $items->where($key, ">=", $start);
                    $items->where($key, "<=", $end);
                    continue;
                }
                if (in_array($key, static::BETWEEN_DATE)) {
                    $start = Carbon::parse($value['start'])->startOfDay()->toDateTimeString();
                    $end = Carbon::parse($value['end'])->endOfDay()->toDateTimeString();
                    $items->whereDate($key, ">=", $start);
                    $items->whereDate($key, "<=", $end);
                    continue;
                }
                if (in_array($key, static::BOOLEAN)) {
                    if ($value == 0) {
                        $items->where(function ($query) use ($key) {
                            $query->whereRaw("{$key} is false")->orWhereRaw("{$key} is null");
                        });
                    } else {
                        $items->whereRaw("{$key} is true");
                    }
                    continue;
                }
                if (in_array($key, static::IN)) {
                    $value = is_string($value) ? explode(",", $value) : $value;
                    $items->whereIn($key, $value);
                    continue;
                }
            }
        }

        if ($sort) {
            $sort = explode(',', $sort);
            foreach ($sort as $itemSort) {
                $asc = true;

                if (strpos($itemSort, "-") === 0) {
                    $asc = false;
                    $itemSort = substr($itemSort, 1);
                }
                if (in_array($itemSort, static::SORT)) {
                    $asc = $asc ? 'asc' : 'desc';
                    $items->orderBy($itemSort, $asc);
                }
            }
        }
    }

    /**
     * Get LengthAwarePaginator
     *
     * @param LengthAwarePaginator $paginator
     * @param boolean $convertTime
     * @return array
     */
    public static function getLengthAwarePaginatorData(LengthAwarePaginator $paginator): array
    {
        $collections = $paginator->getCollection();
        $meta = [
            "total" => (int)$paginator->total(),
            "total_page" => (int)$paginator->lastPage(),
            "page" => (int)$paginator->currentPage(),
            "page_size" => (int) $paginator->perPage(),
        ];

        return [$collections, $meta];
    }
}
