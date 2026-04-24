<?php

namespace App\Helpers;

use App\Enums\UserRole;
use App\Models\Supplier;
use App\Models\User;

class SupplierNotificationRecipient
{
    public static function resolve(Supplier $supplier): ?User
    {
        if ($supplier->relationLoaded('user') && $supplier->user) {
            return $supplier->user;
        }

        if ($supplier->user_id) {
            $linkedUser = User::query()->find($supplier->user_id);

            if ($linkedUser) {
                return $linkedUser;
            }
        }

        if (! $supplier->email) {
            return null;
        }

        $matchedUser = User::query()
            ->where('role', UserRole::Supplier)
            ->where('email', $supplier->email)
            ->first();

        if (! $matchedUser) {
            return null;
        }

        if (! $supplier->user_id) {
            $supplier->forceFill(['user_id' => $matchedUser->id])->save();
        }

        return $matchedUser;
    }
}
