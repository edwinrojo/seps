<?php

use App\Models\Attachment;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withSchedule(function (Schedule $schedule) {
        // Attachment Validity
        $schedule->call(function () {
            $expiredAttachments = Attachment::whereDate('validity_date', '<', Carbon::today())
                ->whereHas('statuses', function ($query) {
                    $query->where('id', function ($sub) {
                        $sub->selectRaw('MAX(id)')
                            ->from('statuses as s2')
                            ->whereColumn('s2.statusable_id', 'statuses.statusable_id')
                            ->whereColumn('s2.statusable_type', 'statuses.statusable_type');
                    })->where('status', '!=', 'expired');
                })
                ->get();

            $system_administrator = User::where('role', 'administrator')->first();
            DB::transaction(function () use ($expiredAttachments, $system_administrator) {
                foreach ($expiredAttachments as $attachment) {
                    $attachment->statuses()->create([
                        'user_id' => $system_administrator->id,
                        'status' => 'expired',
                        'remarks' => '<p>The document has expired based on the validity date</p>',
                        'status_date' => now()
                    ]);
                }
            });

            // Notification
            $count = $expiredAttachments->count();
            if ($count > 0) {
                $system_administrator = User::where('role', 'administrator')->first();
                $system_administrator->notify(
                    Notification::make()
                        ->title('Document Expiration')
                        ->body($count . ' ' . ' document/s have expired today. ' . now()->toDateString())
                        ->danger()
                        ->actions([
                            Action::make('view')
                                ->label('View Documents')
                                ->url('/admin/attachments?tab=expired')
                                ->markAsRead()
                                ->button(),
                        ])
                        ->toDatabase(),
                );
            }

            Log::info('Expired attachment scheduler ran', [
                'processed' => $expiredAttachments->count(),
            ]);
        })->daily();
    })
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
