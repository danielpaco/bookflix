<?php

namespace App\Http\Controllers\Api;

use App\Models\Plan;
use App\Models\Payment;
use App\Models\Subscription;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SubscriptionController extends Controller
{
    public function plans()
    {
        return Plan::where('is_active', true)
            ->get();
    }

    public function subscribe(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id'
        ]);

        $plan = Plan::findOrFail(
            $request->plan_id
        );

        $subscription = Subscription::create([

            'user_id' => auth()->id(),

            'plan_id' => $plan->id,

            'starts_at' => now(),

            'expires_at' => now()
                ->addDays($plan->duration_days),

            'status' => 'active',

            'provider' => 'manual'
        ]);

        Payment::create([

            'user_id' => auth()->id(),

            'subscription_id' => $subscription->id,

            'amount' => $plan->price,

            'status' => 'paid',

            'provider' => 'manual'
        ]);

        return [
            'message' => 'Subscription activated',
            'subscription' => $subscription
        ];
    }

    public function current()
    {
        return auth()
            ->user()
            ->activeSubscription;
    }
}