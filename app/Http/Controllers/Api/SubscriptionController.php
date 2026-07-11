<?php

namespace App\Http\Controllers\Api;

use App\Services\SubscriptionService;
use App\Support\ApiResponse;

use App\Http\Resources\PlanResource;
use App\Http\Resources\SubscriptionResource;
use App\Http\Request\Subscription\SubscribeRequest;

use Illuminate\Support\Facades\DB;
use App\Models\Plan;
use App\Models\Payment;
use App\Models\Subscription;
use App\Http\Controllers\Controller;

class SubscriptionController extends Controller
{
    protected SubscriptionService $subscriptionService;

    public function __construct(
        SubscriptionService $subscriptionService
    ){
        $this->subscriptionService = $subscriptionService;
    }

    public function plans()
    {
        return ApiResponse::success(
            PlanResource::collection(
                $this->subscriptionService
                    ->plans()
            )
        );
    }

    public function subscribe(
        SubscribeRequest $request
    )
    {
        $subscription =
            $this->subscriptionService
                ->subscribe(
                    auth()->user(),
                    $request->plan_id
                );

        return ApiResponse::success(
            new SubscriptionResource(
                $subscription
            ),
            'Subscription activated'
        );
    }

    public function current()
    {
        return ApiResponse::success(
            new SubscriptionResource(
                $this->subscriptionService
                    ->current(
                        auth()->user()
                    )
            )
        );
    }
}