<?php

namespace App\Policies;

use App\Models\DonationDistribution;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DonationDistributionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, DonationDistribution $donationDistribution): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, DonationDistribution $donationDistribution): bool
    {
        return $user->id === $donationDistribution->created_by;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DonationDistribution $donationDistribution): bool
    {
        return $user->id === $donationDistribution->created_by;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, DonationDistribution $donationDistribution): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, DonationDistribution $donationDistribution): bool
    {
        return false;
    }
}
