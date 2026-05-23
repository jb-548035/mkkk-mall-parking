<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ParkingSlot;
use Illuminate\Http\Request;
use App\Models\ActivityLog;
use App\Models\Zone;

class ParkingSlotController extends Controller
{
    public function index()
    {
        $zones = [];
        
        // Get all unique zone names from parking_slots (excluding 'Other' which we'll add later)
        $uniqueZones = ParkingSlot::select('zone_name')
            ->whereNotNull('zone_name')
            ->distinct()
            ->orderBy('zone_name')
            ->get()
            ->pluck('zone_name')
            ->toArray();
        
        // For each unique zone, get its slots
        foreach ($uniqueZones as $zoneName) {
            $slots = ParkingSlot::where('zone_name', $zoneName)
                ->orderBy('slot_number', 'asc')
                ->get();
            
            $zoneTotal = $slots->count();
            $zoneAvailable = $slots->where('status', 'available')->count();
            
            $zones[] = [
                'name' => $zoneName,
                'total' => $zoneTotal,
                'available' => $zoneAvailable,
                'percentage' => $zoneTotal > 0 ? round(($zoneAvailable / $zoneTotal) * 100, 1) : 0,
                'slots' => $slots
            ];
        }
        
        // Check for any slots without a zone_name (legacy data)
        $otherSlots = ParkingSlot::whereNull('zone_name')
            ->orWhere('zone_name', '')
            ->get();
        
        if ($otherSlots->count() > 0) {
            $zones[] = [
                'name' => 'Other',
                'total' => $otherSlots->count(),
                'available' => $otherSlots->where('status', 'available')->count(),
                'percentage' => $otherSlots->count() > 0 ? round(($otherSlots->where('status', 'available')->count() / $otherSlots->count()) * 100, 1) : 0,
                'slots' => $otherSlots
            ];
        }
        
        // Sort zones by name (natural order)
        usort($zones, function($a, $b) {
            return strnatcmp($a['name'], $b['name']);
        });
        
        $zonesList = Zone::orderBy('sort_order')->orderBy('name')->get();
        return view('admin.slots.index', compact('zones', 'zonesList'));
    }

    public function create()
    {
        // Get all zones for the dropdown
        $zonesList = Zone::orderBy('sort_order')->orderBy('name')->get();
        
        // If no zones exist, show message
        if ($zonesList->isEmpty()) {
            return redirect()->route('admin.zones.index')
                ->with('error', 'Please create a zone first before adding parking slots.');
        }
        
        return view('admin.slots.create', compact('zonesList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'slot_number' => 'required|string|max:10|unique:parking_slots',
            'zone_name' => 'required|string|exists:zones,name',
            'type' => 'required|in:standard,wheelchair,delivery',
            'status' => 'required|in:available,occupied',
            'is_active' => 'boolean',
        ]);

        // Find the zone ID from the zone name
        $zone = Zone::where('name', $request->zone_name)->first();
        
        ParkingSlot::create([
            'slot_number' => $request->slot_number,
            'zone_id' => $zone->id,
            'zone_name' => $request->zone_name,
            'type' => $request->type,
            'status' => $request->status,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.slots.index')
            ->with('success', 'Parking slot created successfully and assigned to Zone ' . $request->zone_name);
    }

    public function edit(ParkingSlot $slot)
    {
        $zonesList = Zone::orderBy('sort_order')->orderBy('name')->get();
        return view('admin.slots.edit', compact('slot', 'zonesList'));
    }

    public function show(ParkingSlot $slot)
    {
        // Optional: redirect to edit page or show details
        return redirect()->route('admin.slots.edit', $slot);
    }    

    public function update(Request $request, ParkingSlot $slot)
    {
        $validated = $request->validate([
            'slot_number' => 'required|string|max:10|unique:parking_slots,slot_number,' . $slot->id,
            'zone_name' => 'required|string|exists:zones,name',
            'type' => 'required|in:standard,wheelchair,delivery',
            'status' => 'required|in:available,occupied',
            'is_active' => 'boolean',
        ]);

        // Find the zone ID from the zone name
        $zone = Zone::where('name', $request->zone_name)->first();

        $slot->update([
            'slot_number' => $request->slot_number,
            'zone_id' => $zone->id,
            'zone_name' => $request->zone_name,
            'type' => $request->type,
            'status' => $request->status,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.slots.index')
            ->with('success', 'Parking slot updated successfully.');
    }

    public function destroy(ParkingSlot $slot)
    {
        $slot->delete();
        return redirect()->route('admin.slots.index')
            ->with('success', 'Parking slot deleted successfully.');
    }

    // Archive (Soft Delete) a slot
    public function archive(ParkingSlot $slot)
    {
        $slot->delete();
        
        ActivityLog::log(
            auth()->id(),
            'archive_slot',
            null,
            ['slot_number' => $slot->slot_number, 'action' => 'archived']
        );
        
        return redirect()->route('admin.slots.index')
            ->with('success', "Slot {$slot->slot_number} has been archived.");
    }

    // Restore a soft-deleted slot
    public function restore($id)
    {
        $slot = ParkingSlot::withTrashed()->findOrFail($id);
        $slotNumber = $slot->slot_number;
        $slot->restore();
        
        ActivityLog::log(
            auth()->id(),
            'restore_slot',
            null,
            ['slot_number' => $slotNumber, 'action' => 'restored']
        );
        
        return redirect()->route('admin.slots.index')
            ->with('success', "Slot {$slotNumber} has been restored.");
    }

    // View archived slots
    public function archived()
    {
        $archivedSlots = ParkingSlot::onlyTrashed()->orderBy('deleted_at', 'desc')->paginate(20);
        
        return view('admin.slots.archived', compact('archivedSlots'));
    }

        // Permanently delete (force delete) an archived slot
        public function forceDelete($id)
        {
            $slot = ParkingSlot::withTrashed()->findOrFail($id);
            $slotNumber = $slot->slot_number;
            $slot->forceDelete();
            
            ActivityLog::log(
                auth()->id(),
                'force_delete_slot',
                null,
                ['slot_number' => $slotNumber, 'action' => 'permanently_deleted']
            );
            
            return redirect()->route('admin.slots.archived')
                ->with('success', "Slot {$slotNumber} has been permanently deleted.");
        }    
    }