<?php

namespace App\Filament\Resources\TeamInvitationResource\Pages;

use App\Filament\Resources\TeamInvitationResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Filament\Notifications\Notification;

class CreateTeamInvitation extends CreateRecord
{
    protected static string $resource = TeamInvitationResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // For bulk invitations, we don't need to create a record immediately
        if (($data['invite_type'] ?? 'single') === 'bulk') {
            $this->handleBulkInvitations($data);
            return [];
        }

        // For single invitations, ensure all required fields are present
        if (!isset($data['team_id']) || !isset($data['role'])) {
            throw new \Exception('Missing required fields for team invitation.');
        }

        // Get the team and find users with the selected role
        $team = Team::find($data['team_id']);
        if (!$team) {
            throw new \Exception('Team not found.');
        }

        // Find users with the selected role
        $users = $team->users()->role($data['role'])->get();

        // If no users found, create record with placeholder but show warning later
        if ($users->isEmpty()) {
            $data['email'] = 'no_users_found@placeholder.com';
        } else {
            // Use the first user's email as the "representative" for this role invitation
            $data['email'] = $users->first()->email;
        }

        // Set default values for required fields if not present
        $data['invited_by'] = $data['invited_by'] ?? Auth::id();
        $data['token'] = $data['token'] ?? Str::random(64);
        $data['expires_at'] = $data['expires_at'] ?? now()->addDays(7);

        return $data;
    }

    protected function handleBulkInvitations(array $data): void
    {
        $team = Team::findOrFail($data['team_id']);
        $bulkOption = $data['bulk_option'];
        $expiresAt = $data['expires_at'];
        $users = [];

        // Get users based on selection
        if ($bulkOption === 'all') {
            $users = $team->users;
        } else {
            $role = $bulkOption;
            $users = $team->users()->role($role)->get();
        }

        // Create invitations
        $count = 0;
        foreach ($users as $user) {
            // Skip creation if no email
            if (empty($user->email)) continue;

            // Get the user's role
            $userRole = $user->roles->first() ? $user->roles->first()->name : 'student';

            // Create the invitation
            $invitation = \App\Models\TeamInvitation::create([
                'team_id' => $team->id,
                'email' => $user->email,
                'role' => $userRole,
                'invited_by' => Auth::id(),
                'expires_at' => $expiresAt,
                'token' => Str::random(64),
            ]);

            // Set skip_observer_email to true to prevent duplicate emails
            $invitation->setSkipObserverEmail(true);
            $invitation->save();

            // Prevent duplicate emails by using cache
            $cacheKey = 'invitation_email_sent_' . $invitation->id;

            // Only send if not already processed
            if (!\Illuminate\Support\Facades\Cache::has($cacheKey)) {
                // Send the email invitation
                \Illuminate\Support\Facades\Mail::to($user->email)
                    ->queue(new \App\Mail\TeamInvitationMail($invitation));

                // Mark as processed
                \Illuminate\Support\Facades\Cache::put($cacheKey, true, now()->addHour());
            }

            $count++;
        }

        // Show notification about bulk invitations
        if ($count > 0) {
            Notification::make()
                ->success()
                ->title('Bulk Invitations Sent')
                ->body("Successfully sent {$count} invitations to members of {$team->name} team.")
                ->send();
        }
    }

    protected function afterCreate(): void
    {
        // For single invitations, we need to send to all users with the selected role
        if (($this->data['invite_type'] ?? 'single') === 'single') {
            $invitation = $this->record;

            // Set skip_observer_email to true to prevent duplicate emails
            $invitation->setSkipObserverEmail(true);
            $invitation->save();

            $team = Team::find($invitation->team_id);

            if (!$team) {
                Notification::make()
                    ->warning()
                    ->title('Warning')
                    ->body('Team not found.')
                    ->send();
                return;
            }

            // Find all users with the selected role in this team
            $users = $team->users()->role($invitation->role)->get();
            $count = 0;

            foreach ($users as $user) {
                if (!$user->email) continue;

                // Use cache to prevent duplicate emails
                $cacheKey = 'invitation_email_sent_' . $invitation->id . '_' . $user->id;

                // Only send if not already processed
                if (!\Illuminate\Support\Facades\Cache::has($cacheKey)) {
                    // Send the invitation email
                    \Illuminate\Support\Facades\Mail::to($user->email)
                        ->queue(new \App\Mail\TeamInvitationMail($invitation));

                    // Mark as processed
                    \Illuminate\Support\Facades\Cache::put($cacheKey, true, now()->addHour());

                    $count++;
                }
            }

            if ($count > 0) {
                Notification::make()
                    ->success()
                    ->title('Success')
                    ->body("Invitation sent to {$count} users with the role {$invitation->role}")
                    ->send();
            } else {
                Notification::make()
                    ->warning()
                    ->title('Warning')
                    ->body('No users found with this role in the selected team.')
                    ->send();
            }
        }
    }

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        // For bulk invitations, handle with the bulk method
        if (($data['invite_type'] ?? 'single') === 'bulk') {
            $this->handleBulkInvitations($data);

            // Create a temporary model with the necessary fields to prevent DB errors
            // This model will never be saved to the database
            $tempModel = new \App\Models\TeamInvitation();

            // Set required fields based on data provided
            if (isset($data['team_id'])) {
                $tempModel->team_id = $data['team_id'];
            }

            $tempModel->email = 'bulk_process@example.com';
            $tempModel->role = 'bulk';
            $tempModel->token = \Illuminate\Support\Str::random(64);
            $tempModel->expires_at = now()->addDays(7);
            $tempModel->invited_by = \Illuminate\Support\Facades\Auth::id();

            // We're just using this as a dummy record, so mark it as exists
            // to prevent accidental saves
            $tempModel->exists = true;

            return $tempModel;
        }

        // For single invitations, ensure all required fields are present
        // Check if team_id is valid - it cannot be null
        if (!isset($data['team_id']) || $data['team_id'] === null) {
            // Find the first team if none specified
            $team = Team::first();

            // If no teams in database, throw an exception
            if (!$team) {
                throw new \Exception('No team found. Please create a team first.');
            }

            // Use the first team's ID
            $data['team_id'] = $team->id;
        }

        // Ensure all other required fields are set with defaults if missing
        $data['invited_by'] = $data['invited_by'] ?? \Illuminate\Support\Facades\Auth::id();
        $data['email'] = $data['email'] ?? 'default@example.com';
        $data['role'] = $data['role'] ?? 'student';
        $data['token'] = $data['token'] ?? \Illuminate\Support\Str::random(64);
        $data['expires_at'] = $data['expires_at'] ?? now()->addDays(7);

        // Using the resourceRecord which ensures the record is created with proper data
        return static::getResource()::getModel()::create($data);
    }
}
