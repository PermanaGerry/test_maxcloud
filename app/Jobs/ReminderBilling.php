<?php

namespace App\Jobs;

use App\Mail\LowBalanceMail;
use App\Models\Account;
use App\Models\User;
use App\Models\UserSubscribes;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class ReminderBilling implements ShouldQueue
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
        $user = User::find($account->user_id);

        $countCoustMounth = 0;
        foreach ($userSubscribes as $userSubscribe) {
            $countCoustMounth += $userSubscribe->packageSubscribe->monthly_rate;
        }

        if ($account->balance < (0.1 * $countCoustMounth)) {
            Mail::to($user->email)
                ->send(new LowBalanceMail($user->toArray()));
        }

        echo "success";
    }
}
