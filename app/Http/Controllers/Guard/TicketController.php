<?php

namespace App\Http\Controllers\Guard;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\ParkingSlot;
use App\Models\Vehicle;
use App\Models\Transaction;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Http\UploadedFile;

class TicketController extends Controller
{
    public function dashboard()
    {
        $today = now()->startOfDay();

        $totalInside = Ticket::where('status', 'active')->count();
        $todayEntries = Ticket::where('entry_time', '>=', $today)->count();
        $availableSlots = ParkingSlot::where('status', 'available')->where('is_active', true)->count();
        $totalCapacity = ParkingSlot::where('is_active', true)->count();
        $feesToday = Transaction::where('created_at', '>=', $today)
            ->where('status', 'completed')
            ->sum('amount');

        $peakLabels = [];
        $peakData = [];
        for ($hour = 6; $hour <= 22; $hour++) {
            $peakLabels[] = sprintf('%02d:00', $hour);
            $start = $today->copy()->setHour($hour)->setMinute(0)->setSecond(0);
            $end = $start->copy()->addHour();
            $peakData[] = Ticket::where('entry_time', '>=', $start)
                ->where('entry_time', '<', $end)
                ->count();
        }

        $activeTickets = Ticket::where('status', 'active')->with('parkingSlot')->get();
        $typeCounts = ['Standard' => 0, 'Wheelchair' => 0, 'Delivery' => 0];
        foreach ($activeTickets as $ticket) {
            if ($ticket->is_delivery) {
                $typeCounts['Delivery']++;
                continue;
            }
            $slotType = $ticket->parkingSlot?->type ?? 'standard';
            if ($slotType === 'wheelchair') {
                $typeCounts['Wheelchair']++;
            } else {
                $typeCounts['Standard']++;
            }
        }

        $recentActivity = Ticket::with('parkingSlot')
            ->latest()
            ->take(5)
            ->get();

        return view('guard.dashboard', compact(
            'totalInside',
            'todayEntries',
            'availableSlots',
            'totalCapacity',
            'feesToday',
            'peakLabels',
            'peakData',
            'typeCounts',
            'recentActivity'
        ));
    }

    public function entryForm()
    {
        $availableSlots = ParkingSlot::where('status', 'available')
            ->where('is_active', true)
            ->get();
        
        return view('guard.entry', compact('availableSlots'));
    }

    public function processEntry(Request $request)
    {
        $request->validate([
            'plate_number' => 'required|string|max:20',
            'is_delivery' => 'boolean',
        ]);

        DB::beginTransaction();

        try {
            // Get or create vehicle
            $vehicle = Vehicle::firstOrCreate(
                ['plate_number' => $request->plate_number],
                ['first_seen_at' => now(), 'total_visits' => 1]
            );

            if ($vehicle->wasRecentlyCreated === false) {
                $vehicle->increment('total_visits');
            }

            // Auto-assign available slot
            $slot = ParkingSlot::where('status', 'available')
                ->where('is_active', true)
                ->first();

            if (!$slot) {
                return back()->with('error', 'No available parking slots!');
            }

            // Get current settings
            $hourlyRate = \App\Models\Setting::get('hourly_rate', 20);
            $gracePeriod = \App\Models\Setting::get('grace_period_minutes', 30);

            // Generate unique QR code
            $qrCode = Str::random(32) . time();

            // Create ticket
            $ticket = Ticket::create([
                'qr_code' => $qrCode,
                'vehicle_id' => $vehicle->id,
                'plate_number' => $request->plate_number,
                'entry_time' => now(),
                'rate_at_entry' => $hourlyRate,
                'grace_period_at_entry' => $gracePeriod,
                'is_delivery' => $request->has('is_delivery'),
                'status' => 'active',
                'parking_slot_id' => $slot->id,
                'issued_by' => auth()->id(),
            ]);

            // Mark slot as occupied
            $slot->update(['status' => 'occupied']);

            // Log activity
            ActivityLog::log(
                auth()->id(),
                'issue_ticket',
                $ticket->id,
                ['plate_number' => $request->plate_number, 'slot' => $slot->slot_number]
            );

            DB::commit();

            // Store data in session with flash
            return redirect()->route('guard.entry.form')
                ->with('success', 'Ticket issued successfully!')
                ->with('ticket_data', [
                    'ticket' => $ticket,
                    'slot' => $slot
                ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to issue ticket: ' . $e->getMessage());
        }
    }
    public function exitForm()
    {
        return view('guard.exit');
    }

    public function scanTicket(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string',
        ]);

        $ticket = Ticket::where('qr_code', $request->qr_code)
            ->where('status', 'active')
            ->first();

        if (!$ticket) {
            return back()->with('error', 'Invalid or already exited ticket!');
        }

        // Calculate fee
        $fee = $ticket->calculateFee();

        return view('guard.payment', compact('ticket', 'fee'));
    }

    public function processPayment(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
            'payment_method' => 'required|in:cash,card,e_wallet',
            'amount' => 'required|numeric|min:0',
        ]);

        $ticket = Ticket::findOrFail($request->ticket_id);

        if ($ticket->status !== 'active') {
            return back()->with('error', 'Ticket is not active!');
        }

        DB::beginTransaction();

        try {
            // Update ticket
            $ticket->update([
                'exit_time' => now(),
                'fee' => $request->amount,
                'status' => 'exited',
                'exited_by' => auth()->id(),
            ]);

            // Create transaction
            Transaction::create([
                'ticket_id' => $ticket->id,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'gateway_reference' => $request->payment_method === 'card' ? 'REF_' . Str::random(10) : null,
                'status' => 'completed',
                'processed_by' => auth()->id(),
            ]);

            // Free up parking slot
            $ticket->parkingSlot->update(['status' => 'available']);

            // Log activity
            ActivityLog::log(
                auth()->id(),
                'process_payment',
                $ticket->id,
                ['amount' => $request->amount, 'method' => $request->payment_method]
            );

            DB::commit();

            return redirect()->route('guard.exit.form')
                ->with('success', 'Payment processed successfully! Gate opening...')
                ->with('receipt', $ticket);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Payment failed: ' . $e->getMessage());
        }
    }

    public function searchByPlate(Request $request)
    {
        $request->validate([
            'plate_number' => 'required|string|max:20',
        ]);

        $ticket = Ticket::where('plate_number', $request->plate_number)
            ->where('status', 'active')
            ->first();

        if (!$ticket) {
            return back()->with('error', 'No active ticket found for plate number: ' . $request->plate_number);
        }

        $fee = $ticket->calculateFee();

        return view('guard.payment', compact('ticket', 'fee'));
    }    

    public function uploadQRCode(Request $request)
    {
        $request->validate([
            'qr_image' => 'required|image|mimes:png,jpg,jpeg|max:5120',
        ]);
        
        try {
            // Get the uploaded file
            $file = $request->file('qr_image');
            
            // Use an external API to decode the QR code (free, no PHP extensions needed)
            $qrCodeValue = $this->decodeQRCodeViaAPI($file);
            
            if (!$qrCodeValue) {
                return response()->json([
                    'success' => false,
                    'message' => 'Could not read QR code from image. Please try manual entry.'
                ], 400);
            }
            
            // Find the ticket
            $ticket = Ticket::where('qr_code', $qrCodeValue)
                ->where('status', 'active')
                ->first();
            
            if (!$ticket) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid QR code or ticket already exited.'
                ], 404);
            }
            
            // Store in session for payment processing
            session(['scanned_ticket_id' => $ticket->id]);
            
            return response()->json([
                'success' => true,
                'redirect_url' => route('guard.exit.payment.form', ['ticket' => $ticket->id])
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing image: ' . $e->getMessage()
            ], 500);
        }
    }

    private function decodeQRCodeViaAPI(UploadedFile $file)
    {
        // Free QR code decoding API (no API key required, rate limited)
        // Alternative: Use a local PHP QR decoder if available
        
        $imageData = base64_encode(file_get_contents($file->getRealPath()));
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.qrserver.com/v1/read-qr-code/');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, ['file' => new \CURLFile($file->getRealPath())]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200 && $response) {
            $data = json_decode($response, true);
            if (isset($data[0]['symbol'][0]['data']) && !empty($data[0]['symbol'][0]['data'])) {
                return $data[0]['symbol'][0]['data'];
            }
        }
        
        return null;
    }

    public function paymentForm($ticketId)
    {
        $ticket = Ticket::findOrFail($ticketId);
        
        if ($ticket->status !== 'active') {
            return redirect()->route('guard.exit.form')
                ->with('error', 'This ticket is no longer active.');
        }
        
        $fee = $ticket->calculateFee();
        
        return view('guard.payment', compact('ticket', 'fee'));
    }    
}