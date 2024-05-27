<?php

namespace App\Service;

use App\Core\Model\ModelSqlService;
use App\Models\User;
use App\Core\ConstantGlobal;
use Faker\Core\Number;

class UserService extends ModelSqlService
{
    const EQUAL = [
        'email',
    ];

    const LIKE_FULL = [
        'username'
    ];

    const BETWEEN_DATE = [
        'created_at'
    ];

    public static function getAll($params = [], $query = null)
    {
        $query = User::query()->orderBy('id', 'desc');
        // Kiểm tra xem có tham số created_at không?
        if (isset($params[ConstantGlobal::FILTERS]['created_at'])) {
            $created_at = $params[ConstantGlobal::FILTERS]['created_at'];
            $params[ConstantGlobal::FILTERS]['created_at'] = json_decode($created_at, true);
        }
        return parent::getAll($params, $query);
    }

    public static function getById(String $id)
    {
        $user = User::query()->where('id', $id)->first();
        return $user;
    }

    public static function insert(array $data = [])
    {
        $user = User::create($data);
        return $user;
    }

    public static function update(array $data = [], String $id)
    {
        $user = User::find($id);
        $user->update($data);
        return $user;
    }

    public static function delete(String $id)
    {
        $user = User::find($id);
        $user->delete();
        return $user;
    }
}
