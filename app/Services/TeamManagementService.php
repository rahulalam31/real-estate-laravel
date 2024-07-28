<?php

namespace App\Services;

use App\Models\Team;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TeamManagementService
{
    public function createDefaultTeamForUser(User $user): Team
    {
        try {
            $defaultBranch = Branch::firstOrFail();
        } catch (ModelNotFoundException $e) {
            throw new \Exception('No default branch found. Please set up at least one branch.');
        }

        return $user->ownedTeams()->create([
            'name' => $defaultBranch->name . ' Team',
            'personal_team' => false,
            'branch_id' => $defaultBranch->id,
        ]);
    }

    public function createPersonalTeamForUser(User $user): Team
    {
        return $user->ownedTeams()->create([
            'name' => $user->name . "'s Team",
            'personal_team' => true,
        ]);
    }

    public function assignUserToDefaultTeam(User $user): void
    {
        try {
            $defaultTeam = Team::where('personal_team', false)->first();

            if (!$defaultTeam) {
                $defaultTeam = $this->createDefaultTeamForUser($user);
            }

            $this->assignUserToTeam($user, $defaultTeam);
        } catch (\Exception $e) {
            Log::error('Failed to assign user to default team', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            throw new \Exception('Failed to assign user to default team: ' . $e->getMessage());
        }
    }

    public function assignUserToTeam(User $user, Team $team): void
    {
        $user->teams()->syncWithoutDetaching([$team->id]);
        $this->switchTeam($user, $team);
    }

    public function switchTeam(User $user, Team $team): void
    {
        if (!$user->belongsToTeam($team)) {
            throw new \Exception('User does not belong to the specified team.');
        }
        $user->switchTeam($team);
    }
}