<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Zone;
use App\Models\ParkingSlot;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ZoneController extends Controller
{
    public function index()
    {
        $zones = Zone::orderBy('sort_order')->orderBy('name')->get();
        
        // If no zones in zones table, get from parking_slots zone_name
        if ($zones->isEmpty()) {
            // Create virtual zones from existing parking slots
            $zoneNames = ParkingSlot::select('zone_name')
                ->whereNotNull('zone_name')
                ->distinct()
                ->orderBy('zone_name')
                ->get()
                ->pluck('zone_name');
            
            foreach ($zoneNames as $zoneName) {
                $zone = new Zone();
                $zone->name = $zoneName;
                $zone->description = "Zone {$zoneName}";
                $zone->total_slots = ParkingSlot::where('zone_name', $zoneName)->count();
                $zone->pwd_slots = ParkingSlot::where('zone_name', $zoneName)->where('type', 'wheelchair')->count();
                $zone->delivery_slots = ParkingSlot::where('zone_name', $zoneName)->where('type', 'delivery')->count();
                $zones->push($zone);
            }
        }
        
        // Calculate stats for each zone
        foreach ($zones as $zone) {
            $zone->total_slots_count = ParkingSlot::where('zone_name', $zone->name)->count();
            $zone->available_slots = ParkingSlot::where('zone_name', $zone->name)->where('status', 'available')->count();
            $zone->occupied_slots = ParkingSlot::where('zone_name', $zone->name)->where('status', 'occupied')->count();
            $zone->pwd_occupied = ParkingSlot::where('zone_name', $zone->name)->where('type', 'wheelchair')->where('status', 'occupied')->count();
        }
        
        return view('admin.zones.index', compact('zones'));
    }

    public function create()
    {
        return view('admin.zones.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:10|unique:zones',
            'description' => 'nullable|string',
            'total_slots' => 'required|integer|min:1',
            'pwd_slots' => 'required|integer|min:0',
            'delivery_slots' => 'required|integer|min:0',
            'sort_order' => 'integer',
        ]);

        $zone = Zone::create($request->all());

        // Auto-create parking slots for the zone
        $this->generateSlotsForZone($zone);

        ActivityLog::log(auth()->id(), 'create_zone', null, [
            'zone_name' => $zone->name,
            'total_slots' => $zone->total_slots
        ]);

        return redirect()->route('admin.zones.index')
            ->with('success', "Zone {$zone->name} created with {$zone->total_slots} slots.");
    }

    public function edit(Zone $zone)
    {
        return view('admin.zones.edit', compact('zone'));
    }

    public function update(Request $request, Zone $zone)
    {
        $request->validate([
            'name' => 'required|string|max:10|unique:zones,name,' . $zone->id,
            'description' => 'nullable|string',
            'total_slots' => 'required|integer|min:' . $zone->parkingSlots()->count(),
            'pwd_slots' => 'required|integer|min:0',
            'delivery_slots' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        $oldName = $zone->name;
        $zone->update($request->all());

        // Update slot numbers if zone name changed
        if ($oldName !== $zone->name) {
            ParkingSlot::where('zone_id', $zone->id)->update(['zone_name' => $zone->name]);
            foreach (ParkingSlot::where('zone_id', $zone->id)->get() as $slot) {
                $slot->slot_number = $zone->name . substr($slot->slot_number, 1);
                $slot->save();
            }
        }

        ActivityLog::log(auth()->id(), 'update_zone', null, [
            'zone_name' => $zone->name,
            'total_slots' => $zone->total_slots
        ]);

        return redirect()->route('admin.zones.index')
            ->with('success', "Zone {$zone->name} updated successfully.");
    }

    public function destroy(Zone $zone)
    {
        // Delete all parking slots in this zone first
        ParkingSlot::where('zone_id', $zone->id)->delete();
        $zoneName = $zone->name;
        $zone->delete();

        ActivityLog::log(auth()->id(), 'delete_zone', null, ['zone_name' => $zoneName]);

        return redirect()->route('admin.zones.index')
            ->with('success', "Zone {$zoneName} deleted.");
    }

    private function generateSlotsForZone(Zone $zone)
    {
        // First, delete any existing slots for this zone to avoid duplicates
        ParkingSlot::where('zone_id', $zone->id)->delete();
        
        $slotNumber = 1;
        
        // Create PWD slots first (5 per zone as requested)
        for ($i = 1; $i <= $zone->pwd_slots; $i++) {
            ParkingSlot::create([
                'slot_number' => $zone->name . str_pad($slotNumber++, 2, '0', STR_PAD_LEFT),
                'type' => 'wheelchair',
                'status' => 'available',
                'is_active' => true,
                'zone_id' => $zone->id,
                'zone_name' => $zone->name,
            ]);
        }

        // Create Delivery slots
        for ($i = 1; $i <= $zone->delivery_slots; $i++) {
            ParkingSlot::create([
                'slot_number' => $zone->name . str_pad($slotNumber++, 2, '0', STR_PAD_LEFT),
                'type' => 'delivery',
                'status' => 'available',
                'is_active' => true,
                'zone_id' => $zone->id,
                'zone_name' => $zone->name,
            ]);
        }

        // Create Standard slots for remaining
        $standardSlots = $zone->total_slots - $zone->pwd_slots - $zone->delivery_slots;
        for ($i = 1; $i <= $standardSlots; $i++) {
            ParkingSlot::create([
                'slot_number' => $zone->name . str_pad($slotNumber++, 2, '0', STR_PAD_LEFT),
                'type' => 'standard',
                'status' => 'available',
                'is_active' => true,
                'zone_id' => $zone->id,
                'zone_name' => $zone->name,
            ]);
        }
    }
    public function regenerateSlots(Zone $zone)
    {
        DB::beginTransaction();
        
        try {
            ParkingSlot::where('zone_id', $zone->id)->delete();
            $this->generateSlotsForZone($zone);
            
            DB::commit();

            return redirect()->route('admin.zones.index')
                ->with('success', "Zone {$zone->name} slots regenerated successfully.");
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to regenerate slots: ' . $e->getMessage());
        }
    }

    // Archive (Soft Delete) a zone
    public function archive(Zone $zone)
    {
        // Also archive all parking slots in this zone
        ParkingSlot::where('zone_id', $zone->id)->delete();
        
        $zoneName = $zone->name;
        $zone->delete();
        
        ActivityLog::log(
            auth()->id(),
            'archive_zone',
            null,
            ['zone_name' => $zoneName, 'action' => 'archived']
        );
        
        return redirect()->route('admin.zones.index')
            ->with('success', "Zone {$zoneName} has been archived along with its parking slots.");
    }

    // Restore a soft-deleted zone
    public function restore($id)
    {
        $zone = Zone::withTrashed()->findOrFail($id);
        $zoneName = $zone->name;
        $zone->restore();
        
        // Restore all parking slots in this zone
        ParkingSlot::withTrashed()->where('zone_id', $zone->id)->restore();
        
        ActivityLog::log(
            auth()->id(),
            'restore_zone',
            null,
            ['zone_name' => $zoneName, 'action' => 'restored']
        );
        
        return redirect()->route('admin.zones.index')
            ->with('success', "Zone {$zoneName} has been restored along with its parking slots.");
    }

    // View archived zones
    public function archived()
    {
        $archivedZones = Zone::onlyTrashed()->orderBy('deleted_at', 'desc')->paginate(10);
        
        // Calculate stats for archived zones
        foreach ($archivedZones as $zone) {
            $zone->total_slots_count = ParkingSlot::withTrashed()->where('zone_id', $zone->id)->count();
            $zone->available_slots = ParkingSlot::withTrashed()->where('zone_id', $zone->id)->where('status', 'available')->count();
            $zone->occupied_slots = ParkingSlot::withTrashed()->where('zone_id', $zone->id)->where('status', 'occupied')->count();
            $zone->pwd_occupied = ParkingSlot::withTrashed()->where('zone_id', $zone->id)->where('type', 'wheelchair')->where('status', 'occupied')->count();
        }
        
        return view('admin.zones.archived', compact('archivedZones'));
    }

    // Permanently delete (force delete) an archived zone
    public function forceDelete($id)
    {
        $zone = Zone::withTrashed()->findOrFail($id);
        $zoneName = $zone->name;
        
        // Permanently delete all parking slots in this zone
        ParkingSlot::withTrashed()->where('zone_id', $zone->id)->forceDelete();
        
        $zone->forceDelete();
        
        ActivityLog::log(
            auth()->id(),
            'force_delete_zone',
            null,
            ['zone_name' => $zoneName, 'action' => 'permanently_deleted']
        );
        
        return redirect()->route('admin.zones.archived')
            ->with('success', "Zone {$zoneName} has been permanently deleted.");
    }    
}