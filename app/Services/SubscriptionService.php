<?php

namespace App\Services;

use App\Models\Plan;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SubscriptionService
{
    public function plans()
    {
        return Plan::query()
            ->where('is_active', true)
            ->orderBy('price')
            ->get();
    }

    public function subscribe(
        User $user,
        int $planId
    ): Subscription
    {
        $plan = Plan::findOrFail($planId);

        return DB::transaction(function () use ($user, $plan) {
            $subscription = Subscription::create([
                'user_id'=>$user->id,
                'plan_id'=>$plan->id,
                'starts_at'=>now(),
                'expires_at'=>now()
                    ->addDays($plan->duration_days),
                'status'=>'active',
                'provider'=>'manual'
            ]);

            Payment::create([
                'user_id'=>$user->id,
                'subscription_id'=>$subscription->id,
                'amount'=>$plan->price,
                'status'=>'paid',
                'provider'=>'manual'

            ]);

            return $subscription->fresh([
                'plan'
            ]);

        });
    }

    public function current(User $user)
    {
        return $user
            ->activeSubscription()
            ->with('plan')
            ->first();
    }
}