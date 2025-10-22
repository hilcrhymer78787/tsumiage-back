<?php

namespace App\Domains\InvitationDelete\Queries;

use App\Models\Invitation;

class InvitationDeleteQuery
{
    public function deleteInvitation(int $invitationId, int $myUserId): int
    {
        return Invitation::where('invitation_id', $invitationId)
            ->where(function ($query) use ($myUserId) {
                $query->where('invitation_to_user_id', $myUserId)
                    ->orWhere('invitation_from_user_id', $myUserId);
            })
            ->delete();
    }
}
