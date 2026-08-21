<?php

declare(strict_types=1);

namespace App\Presentation\Http\Api\V1\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

final class StoreProjectMemberRequest extends FormRequest
{
    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'max:255', 'exists:users,email'],
        ];
    }

    public function member(): User
    {
        return User::query()
            ->where('email', $this->string('email')->toString())
            ->firstOrFail();
    }
}
