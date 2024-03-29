<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class BookAddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $province = DB::table('provinces')->where('province_id', $this[0]->province_id)->get();
        $district = DB::table('districts')->where('district_id', $this[0]->district_id)->get();
        $ward = DB::table('wards')->where('wards_id', $this[0]->wards_id)->get();
        $address = $this[0]->address . ", " . $ward[0]->name . ", " . $district[0]->name . ", " . $province[0]->name;

        switch ($request->method()) {
            case 'GET':
                return [
                    'id' => $this[0]->id,
                    'status' => $this[0]->status == 1 ? 'Active' : 'Inactive',
                    'fullname' => $this[0]->fullname,
                    'address' => $address,
                    'phone' => $this[0]->phone,
                ];
                break;
            default:
                return [
                    'id' => $this[0]->id,
                    'status' => $this[0]->status == 1 ? 'Active' : 'Inactive',
                    'fullname' => $this[0]->fullname,
                    'province' => $province[0]->name,
                    'district' => $district[0]->name,
                    'ward' => $ward[0]->name,
                    'address' => $address,
                    'phone' => $this[0]->phone,
                ];
                break;
        }
    }
}
