<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupDayResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'group_id'    => $this->group_id,
            'day_of_week' => $this->day_of_week,
            'start_time'  => $this->start_time ? substr($this->start_time, 0, 5) : null,
            'end_time'    => $this->end_time ? substr($this->end_time, 0, 5) : null,
            'room'        => $this->room,
        ];
    }
}
