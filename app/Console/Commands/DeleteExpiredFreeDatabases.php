<?php

namespace App\Console\Commands;

use App\Models\Crm;
use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DeleteExpiredFreeDatabases extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:expired_databases';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete Databases Older than a week on the free plan';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        date_default_timezone_set('America/New_York');
       // $date = \Carbon\Carbon::today('UTC')->subDays(0);
        $date=date('Y-m-d H:i:s', strtotime('7 Day'));
        $users=User::all();
        foreach($users as $user)
        {
            if(!$user->is_subscribed())
            {

                Crm::where('user_id', $user->id)
                    ->where('created_at','<=',$date)->each(function ($db) {
                    DB::statement("DROP DATABASE " . $db->name);
                });
                Crm::where('user_id', $user->id)
                    ->where('created_at','<=',$date)
                    ->delete();
            }
        }
        return Command::SUCCESS;
    }
}
