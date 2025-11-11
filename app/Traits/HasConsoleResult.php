<?php

namespace App\Traits;

trait HasConsoleResult
{
    protected $totalProcessed = 0;
    protected $totalCreated = 0;
    protected $totalUpdated = 0;
    protected $totalFailed = 0;

    public function incrementProcessed(): void
    {
        $this->totalProcessed++;
    }

    public function incrementCreated(): void
    {
        $this->totalCreated++;
    }

    public function incrementUpdated(): void
    {
        $this->totalUpdated++;
    }

    public function incrementFailed(): void
    {
        $this->totalFailed++;
    }

    public function getTotalProcessed(): int
    {
        return $this->totalProcessed;
    }

    public function getTotalCreated(): int
    {
        return $this->totalCreated;
    }

    public function getTotalUpdated(): int
    {
        return $this->totalUpdated;
    }

    public function getTotalFailed(): int
    {
        return $this->totalFailed;
    }


    /**
     * Display detailed sync results
     */
    protected function displaySyncResults(array $result): void
    {
        $this->newLine();
        $this->info('=== SYNCHRONIZATION SUMMARY ===');

        // Progress bar
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Records', $result['total_records']],
                ['Processed Records', $result['processed_records']],
                ['Successful Records', $result['successful_records']],
                ['Failed Records', $result['failed_records']],
                ['Success Rate', $result['success_rate'] . '%'],
                ['Execution Time', number_format($result['execution_time'], 2) . ' seconds'],
                ['Timestamp', $result['timestamp']],
            ]
        );

        // Show errors if any
        if (!empty($result['errors'])) {
            $this->newLine();
            $this->error('=== ERRORS ENCOUNTERED ===');

            foreach ($result['errors'] as $error) {
                $this->error(sprintf(
                    'Record: %s - Error: %s',
                    $error['record_id'] ?? 'Unknown',
                    $error['error']
                ));

                if ($this->option('verbose')) {
                    $this->line($error['trace'] ?? '');
                }
            }
        }

        // Show processing log if verbose
        if ($this->option('verbose') && !empty($result['processing_log'])) {
            $this->newLine();
            $this->info('=== DETAILED PROCESSING LOG ===');

            foreach ($result['processing_log'] as $log) {
                $statusColor = match ($log['status']) {
                    'success' => 'info',
                    'failed' => 'error',
                    'error' => 'error',
                    default => 'line'
                };

                $this->$statusColor(sprintf(
                    '[%s] %s: %s',
                    $log['timestamp'],
                    strtoupper($log['status']),
                    $log['message']
                ));
            }
        }

        // Final status
        $this->newLine();
        if ($result['failed_records'] > 0) {
            $this->warn(sprintf(
                'Synchronization completed with %d error(s).',
                $result['failed_records']
            ));
        } else {
            $this->info('All records synchronized successfully!');
        }
    }
}
