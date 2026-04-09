<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesRestaurant;
use App\Models\PrintJob;
use Illuminate\Http\Request;

class PrintJobController extends Controller
{
    use ResolvesRestaurant;

    /**
     * Tablet polls this endpoint to fetch the next pending job.
     * It will be marked as claimed to avoid duplicates across devices.
     */
    public function next(Request $request)
    {
        $restaurantId = $this->currentRestaurantId();
        if (! $restaurantId) {
            return response()->json(['error' => 'No restaurant selected.'], 403);
        }

        $job = PrintJob::where('restaurant_id', $restaurantId)
            ->where('status', 'pending')
            ->orderBy('id')
            ->first();

        if (! $job) {
            return response()->json(['job' => null]);
        }

        $job->status = 'claimed';
        $job->claimed_at = now();
        $job->save();

        return response()->json([
            'job' => [
                'id' => $job->id,
                'type' => $job->type,
                'payload' => $job->payload,
                'created_at' => optional($job->created_at)->toIso8601String(),
            ],
        ]);
    }

    public function markPrinted(Request $request, PrintJob $job)
    {
        $restaurantId = $this->currentRestaurantId();
        if (! $restaurantId || (int) $job->restaurant_id !== (int) $restaurantId) {
            abort(403);
        }

        $job->status = 'printed';
        $job->printed_at = now();
        $job->error = null;
        $job->save();

        return response()->json(['success' => true]);
    }

    public function markFailed(Request $request, PrintJob $job)
    {
        $restaurantId = $this->currentRestaurantId();
        if (! $restaurantId || (int) $job->restaurant_id !== (int) $restaurantId) {
            abort(403);
        }

        $request->validate(['error' => 'nullable|string|max:5000']);

        $job->status = 'failed';
        $job->error = $request->input('error');
        $job->save();

        return response()->json(['success' => true]);
    }
}

