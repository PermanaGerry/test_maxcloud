<?php

namespace App\Jobs;

use App\Models\Account;
use App\Models\Billing;
use App\Models\UserSubscribes;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BillingPaymentProcess implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $accountId;

    protected $userSubscribesId;

    /**
     * Create a new job instance.
     */
    public function __construct($accountId, $userSubscribesId)
    {
        $this->accountId = $accountId;
        $this->userSubscribesId = $userSubscribesId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $account = Account::find($this->accountId);
        $userSubscribes = UserSubscribes::with('packageSubscribe')->whereIn('id', $this->userSubscribesId)->get();

        $coust = 0;
        $bill = collect();
        $now = now()->format('Y-m-d H:i:s');
        foreach ($userSubscribes as $userSubscribe) {
            $coust += $userSubscribe->packageSubscribe->monthly_rate;

            $bill->push([
                'account_id' => $account->id,
                'user_subscribes_id' => $userSubscribe->id,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        DB::beginTransaction();

        try {
            Billing::insert($bill->toArray());

            $account->balance = $account->balance - $coust;
            $account->save();

            foreach ($userSubscribes as $userSubscribe) {
                if($account->balance < 0) {
                    $userSubscribe->is_suspend = true;
                }

                $userSubscribe->expired_at = now()->addDays(30);
                $userSubscribe->save();

            }

            DB::commit();

            echo "success";
        } catch (Exception $e) {
            DB::rollBack();

            Log::error($e->getMessage());

            echo "failed";
        }
    }
}
