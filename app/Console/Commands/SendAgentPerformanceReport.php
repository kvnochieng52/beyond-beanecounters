<?php

namespace App\Console\Commands;

use App\Exports\AgentPerformanceExport;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class SendAgentPerformanceReport extends Command
{
    protected $signature = 'report:agent-performance';
    protected $description = 'Generate and send Agent Performance Report to all active users';

    public function handle()
    {
        $this->info('Generating Agent Performance Report...');

        try {
            // Get report data for today
            $today = Carbon::now()->toDateString();
            $data = $this->generateReportData($today);

            // If no agents, skip sending
            if ($data['agents']->isEmpty()) {
                $this->warn('No agents with activity found for today.');
                return;
            }

            // Get all active users
            $activeUsers = User::where('is_active', 1)->get();

            if ($activeUsers->isEmpty()) {
                $this->warn('No active users found to send report to.');
                return;
            }

            // Generate Excel file
            $fileName = 'agent_performance_report_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
            $relativeFolder = 'temp';
            $tempPath = storage_path('app/' . $relativeFolder);

            // Create temp directory with proper permissions for Linux
            if (!is_dir($tempPath)) {
                @mkdir($tempPath, 0777, true);
                @chmod($tempPath, 0777);
            }

            $filePath = $tempPath . '/' . $fileName;

            // Export Excel file
            try {
                Excel::store(new AgentPerformanceExport($data), $relativeFolder . '/' . $fileName, 'local');
                $this->info('Excel file created');
            } catch (\Exception $e) {
                $this->error('Error creating Excel file: ' . $e->getMessage());
                \Log::error('Excel Generation Error', ['error' => $e->getMessage()]);
                return;
            }

            // Verify file was created (wait a moment for file to be written)
            sleep(2);

            // Check if file exists with multiple strategies
            if (!file_exists($filePath)) {
                // Try glob pattern with forward slashes
                $pattern = $tempPath . '/agent_performance_report_*.xlsx';
                $files = glob($pattern);

                if (!empty($files)) {
                    // Sort by modification time, get newest
                    usort($files, function ($a, $b) {
                        return filemtime($b) - filemtime($a);
                    });
                    $filePath = $files[0];
                    $this->info('Located file: ' . basename($filePath));
                } else {
                    $this->error('Failed to create file at: ' . $filePath);
                    // Debug info
                    if (is_dir($tempPath)) {
                        $contents = array_slice(scandir($tempPath), 2);
                        $this->error('Directory exists but contains: ' . implode(', ', array_slice($contents, 0, 3)));
                    } else {
                        $this->error('Temp directory does not exist: ' . $tempPath);
                    }
                    return;
                }
            }

            // Send report to each active user
            foreach ($activeUsers as $user) {
                try {
                    Mail::send('emails.agent-performance-report', ['user' => $user, 'generatedAt' => now()], function ($message) use ($user, $filePath, $fileName) {
                        $message->to($user->email)
                            ->subject('Agent Performance Report - ' . now()->format('d M Y g:i A'));

                        // Attach Excel file
                        $message->attach($filePath, [
                            'as' => $fileName,
                            'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                        ]);
                    });

                    $this->info("Report sent to: {$user->email}");
                } catch (\Exception $e) {
                    $this->error("Failed to send report to {$user->email}: " . $e->getMessage());
                    \Log::error("Agent Performance Report sending failed for {$user->email}", ['error' => $e->getMessage()]);
                }
            }

            // Clean up temp file
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            $this->info('Agent Performance Report generation and distribution completed successfully!');
        } catch (\Exception $e) {
            $this->error('Error generating Agent Performance Report: ' . $e->getMessage());
            \Log::error('Agent Performance Report Error', ['error' => $e->getMessage()]);
        }
    }

    private function generateReportData($date)
    {
        $startOfDay = Carbon::createFromFormat('Y-m-d', $date)->startOfDay();
        $endOfDay = Carbon::createFromFormat('Y-m-d', $date)->endOfDay();

        // Get all agents with call disposition on this day
        $agents = DB::table('users')
            ->join('activities', 'users.id', '=', 'activities.created_by')
            ->where('activities.act_call_disposition_id', '!=', null)
            ->whereBetween('activities.created_at', [$startOfDay, $endOfDay])
            ->distinct()
            ->pluck('users.id');

        if ($agents->isEmpty()) {
            return [
                'agents' => collect(),
                'institutions' => collect(),
                'date' => $date
            ];
        }

        // Get all institutions (active institutions only)
        $institutions = DB::table('institutions')
            ->where('is_active', 1)
            ->orderBy('institution_name')
            ->pluck('institution_name', 'id')
            ->toArray();

        // If no institutions, return empty data

        // Build agent data
        $agentData = [];

        foreach ($agents as $agentId) {
            $agent = DB::table('users')->find($agentId);

            // Get agent code - handle null values
            $agentCode = $agent->agent_code ?? $agent->code ?? '-';

            // Calls made - total call_dispositions
            $callsMade = DB::table('activities')
                ->where('created_by', $agentId)
                ->where('act_call_disposition_id', '!=', null)
                ->whereBetween('created_at', [$startOfDay, $endOfDay])
                ->count();

            // PTP Count - where disposition = 3
            $ptpCount = DB::table('activities')
                ->where('created_by', $agentId)
                ->where('act_call_disposition_id', 3)
                ->whereBetween('created_at', [$startOfDay, $endOfDay])
                ->count();

            // PTP Value - sum of amounts where disposition = 3
            $ptpValue = DB::table('activities')
                ->where('created_by', $agentId)
                ->where('act_call_disposition_id', 3)
                ->whereBetween('created_at', [$startOfDay, $endOfDay])
                ->sum('act_ptp_amount') ?? 0;

            // Total Collected Today - from transactions created by agent
            $totalCollected = DB::table('transactions')
                ->where('created_by', $agentId)
                ->where('transaction_type', 2) // PAYMENT
                ->whereBetween('created_at', [$startOfDay, $endOfDay])
                ->sum('amount') ?? 0;

            // MTD Collected Today - from mtbs table created by agent (using created_at timestamp)
            $mtdCollected = DB::table('mtbs')
                ->where('created_by', $agentId)
                ->whereBetween('created_at', [$startOfDay, $endOfDay])
                ->sum('amount_paid') ?? 0;

            $row = [
                'agent_name' => $agent->name ?? 'Unknown',
                'agent_code' => $agentCode,
                'calls_made' => $callsMade,
                'ptp_count' => $ptpCount,
                'ptp_value' => $ptpValue,
                'total_collected' => $totalCollected,
                'mtd_collected' => $mtdCollected,
            ];

            // Add collections by institution
            foreach ($institutions as $instId => $instName) {
                // Get transaction total amount by this agent for this institution
                // Join transactions with leads to get institution_id
                $institutionCollection = DB::table('transactions')
                    ->join('leads', 'transactions.lead_id', '=', 'leads.id')
                    ->where('transactions.created_by', $agentId)
                    ->where('leads.institution_id', $instId)
                    ->where('transactions.transaction_type', 2) // PAYMENT
                    ->whereBetween('transactions.created_at', [$startOfDay, $endOfDay])
                    ->sum('transactions.amount') ?? 0;

                $row['inst_' . $instId] = $institutionCollection;
            }

            $agentData[] = $row;
        }

        return [
            'agents' => collect($agentData),
            'institutions' => $institutions,
            'date' => $date
        ];
    }
}
