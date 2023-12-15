<?php

namespace App\Console\Commands;

use App\Models\Inquiry;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DeleteOldInquiry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete-old-inquiry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
          
            $oldInquiries = Inquiry::where('created_at', '<', Carbon::now()->subMonths(2))
            ->get();
        
            foreach ($oldInquiries as $inquiry) {
                $inquiry->delete();
            }
            $this->info('Old inquries deleted');

        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }
    }
}
