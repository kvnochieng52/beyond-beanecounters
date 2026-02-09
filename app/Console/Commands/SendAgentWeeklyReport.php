<?php

namespace App\Console\Commands;

use App\Exports\AgentWeeklyReportExport;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class SendAgentWeeklyReport extends Command
{
    protected $signature = 'report:agent-weekly';
    protected $description = 'Generate and send Agent Weekly Report to all active users';

    public function handle()
    {
        $this->info('Generating Agent Weekly Report...');

        try {
            // Get report data for the past 7 days (Sunday to Sunday)
            $today = Carbon::now();
            // Get this week's Sunday (or today if today is Sunday)
            $thisSunday = $today->copy()->startOfWeek(Carbon::SUNDAY);
            // Get the Sunday from 7 days ago
            $lastSunday = $thisSunday->copy()->subDays(7);

            $startOfWeek = $lastSunday->startOfDay();
            $endOfDay = $thisSunday->endOfDay();

            $data = $this->generateWeeklyReportData($startOfWeek, $endOfDay);

            // If no agents, skip sending
            if ($data['agents']->isEmpty()) {
                $this->warn('No agents with activity found for the week.');
                return;
            }

            // Get all active users
            $activeUsers = User::where('is_active', 1)->get();

            if ($activeUsers->isEmpty()) {
                $this->warn('No active users found to send report to.');
                return;
            }

            // Generate Excel file
            $fileName = 'agent_weekly_report_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
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
                Excel::store(new AgentWeeklyReportExport($data), $relativeFolder . '/' . $fileName, 'local');
                $this->info('Weekly Excel file created');
            } catch (\Exception $e) {
                $this->error('Error creating Excel file: ' . $e->getMessage());
                \Log::error('Weekly Excel Generation Error', ['error' => $e->getMessage()]);
                return;
            }

            // Verify file was created (wait a moment for file to be written)
            sleep(2);

            // Check if file exists with multiple strategies
            if (!file_exists($filePath)) {
                // Try glob pattern with forward slashes
                $pattern = $tempPath . '/agent_weekly_report_*.xlsx';
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
                    Mail::send('emails.agent-weekly-report', [
                        'user' => $user,
                        'generatedAt' => now(),
                        'startDate' => $startOfWeek->format('d M Y'),
                        'endDate' => $endOfDay->format('d M Y')
                    ], function ($message) use ($user, $filePath, $fileName) {
                        $message->to($user->email)
                            ->subject('Agent Weekly Report - Week of ' . now()->copy()->subDays(6)->format('d M Y'));

                        // Attach Excel file
                        $message->attach($filePath, [
                            'as' => $fileName,
                            'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                        ]);
                    });

                    $this->info("Weekly report sent to: {$user->email}");
                } catch (\Exception $e) {
                    $this->error("Failed to send report to {$user->email}: " . $e->getMessage());
                    \Log::error("Agent Weekly Report sending failed for {$user->email}", ['error' => $e->getMessage()]);
                }
            }

            // Clean up temp file
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            $this->info('Agent Weekly Report generation and distribution completed successfully!');
        } catch (\Exception $e) {
            $this->error('Error generating Agent Weekly Report: ' . $e->getMessage());
            \Log::error('Agent Weekly Report Error', ['error' => $e->getMessage()]);
        }
    }

    private function generateWeeklyReportData($startDate, $endDate)
    {
        // Get all agents with call disposition within the week
        $agents = DB::table('users')
            ->join('activities', 'users.id', '=', 'activities.created_by')
            ->where('activities.act_call_disposition_id', '!=', null)
            ->whereBetween('activities.created_at', [$startDate, $endDate])
            ->distinct()
            ->pluck('users.id');

        if ($agents->isEmpty()) {
            return [
                'agents' => collect(),
                'institutions' => collect(),
                'period' => $startDate->format('d M Y') . ' - ' . $endDate->format('d M Y')
            ];
        }

        // Get all institutions (active institutions only)
        $institutions = DB::table('institutions')
            ->where('is_active', 1)
            ->orderBy('institution_name')
            ->pluck('institution_name', 'id')
            ->toArray();

        // Build agent data
        $agentData = [];

        foreach ($agents as $agentId) {
            $agent = DB::table('users')->find($agentId);

            // Get agent code - handle null values
            $agentCode = $agent->agent_code ?? $agent->code ?? '-';

            // Calls made - total call_dispositions for the week
            $callsMade = DB::table('activities')
                ->where('created_by', $agentId)
                ->where('act_call_disposition_id', '!=', null)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();

            // PTP Count - where disposition = 3 for the week
            $ptpCount = DB::table('activities')
                ->where('created_by', $agentId)
                ->where('act_call_disposition_id', 3)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();

            // PTP Value - sum of amounts where disposition = 3
            $ptpValue = DB::table('activities')
                ->where('created_by', $agentId)
                ->where('act_call_disposition_id', 3)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('act_ptp_amount') ?? 0;

            // Total Collected for the week - from transactions created by agent
            $totalCollected = DB::table('transactions')
                ->where('created_by', $agentId)
                ->where('transaction_type', 2) // PAYMENT
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('amount') ?? 0;

            // MTD Collected for the week - from mtbs table created by agent
            $mtdCollected = DB::table('mtbs')
                ->where('created_by', $agentId)
                ->whereBetween('created_at', [$startDate, $endDate])
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

            // Add collections by institution (for that week only, matching MTD Collected period)
            foreach ($institutions as $instId => $instName) {
                // Get MTD collected for this agent for this institution for that week
                $institutionCollection = DB::table('mtbs')
                    ->join('leads', 'mtbs.lead_id', '=', 'leads.id')
                    ->where('mtbs.created_by', $agentId)
                    ->where('leads.institution_id', $instId)
                    ->whereBetween('mtbs.created_at', [$startDate, $endDate])
                    ->sum('mtbs.amount_paid');

                $row['inst_' . $instId] = $institutionCollection ?? 0;
            }

            $agentData[] = $row;
        }

        return [
            'agents' => collect($agentData),
            'institutions' => $institutions,
            'period' => $startDate->format('d M Y') . ' - ' . $endDate->format('d M Y')
        ];
    }
}
