<?php

declare(strict_types=1);

namespace App\Presentation\Http\Api\V1\Resources;

use App\Domain\Projects\Entities\ProjectMember;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ProjectMember
 */
final class ProjectMemberResource extends JsonResource
{
    /**
     * @return array<string, int|string>
     */
    public function toArray(Request $request): array
    {
        /** @var ProjectMember $member */
        $member = $this->resource;

        return [
            'id' => $member->userId(),
            'name' => $member->name(),
            'email' => $member->email(),
            'role' => $member->role()->value,
        ];
    }
}
