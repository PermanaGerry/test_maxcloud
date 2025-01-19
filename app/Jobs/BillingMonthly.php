<?php

namespace App\Jobs;

use App\Models\Account;
use App\Models\UserSubscribes;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BillingMonthly implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $userSubscribes = UserSubscribes::with(['user' => ['account']])
            ->where('is_active', true)
            ->get();

            $collecUsersSubscrib = ['payment' => [], 'reminder' => []];
            foreach ($userSubscribes as $userSubscribe) {
                if ($userSubscribe->expired_at > now()) {
                    if (!isset($collecUsersSubscrib['reminder'][$userSubscribe->user->account->id])) {
                        $collecUsersSubscrib['reminder'][$userSubscribe->user->account->id] = [];
                    }
                    $collecUsersSubscrib['reminder'][$userSubscribe->user->account->id][] = $userSubscribe->id;
                } else {
                    if (!isset($collecUsersSubscrib['payment'][$userSubscribe->user->account->id])) {
                        $collecUsersSubscrib['payment'][$userSubscribe->user->account->id] = [];
                    }
                    $collecUsersSubscrib['payment'][$userSubscribe->user->account->id][] = $userSubscribe->id;
                }
            }

            // create job to payment
            foreach ($collecUsersSubscrib['payment'] as $accountId => $userSubscribesId) {
                BillingPaymentProcess::dispatch($accountId, $userSubscribesId);
            }

            // create job to reminder
            foreach ($collecUsersSubscrib['reminder'] as $accountId => $userSubscribesId) {
                ReminderBilling::dispatch($accountId, $userSubscribesId);
            }

            echo "success";
        } catch (\Exception $e) {
            echo $e->getMessage();
        }


    }
}
