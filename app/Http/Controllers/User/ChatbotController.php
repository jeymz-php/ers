<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Establishment;
use App\Models\Reservation;
use App\Models\Campus;
use App\Models\User;
use App\Models\Notification;
use App\Models\ChatbotSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ChatbotController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $campuses = Campus::where('id', $user->campus_id)->get();
        
        // Clear existing session when loading the page
        ChatbotSession::where('user_id', $user->id)->delete();
        
        return view('user.chatbot', compact('campuses'));
    }
    
    public function processMessage(Request $request)
    {
        $message = strtolower(trim($request->message));
        $user = Auth::user();
        
        // Get or create session from database
        $session = ChatbotSession::firstOrCreate(
            ['user_id' => $user->id],
            ['step' => 'idle', 'data' => []]
        );
        
        // Handle file attachments
        if ($request->hasFile('attachments')) {
            return $this->handleFileUpload($request, $session);
        }
        
        // Handle cancel confirmation
        if ($session->step === 'confirming_cancel') {
            return $this->handleCancelConfirmation($message, $session, $user);
        }
        
        // Handle reservation creation flow
        if ($session->step === 'awaiting_event_name') {
            return $this->handleEventName($message, $session, $user);
        } 
        elseif ($session->step === 'awaiting_event_dates') {
            return $this->handleEventDates($message, $session, $user);
        } 
        elseif ($session->step === 'awaiting_start_time') {
            return $this->handleStartTime($message, $session, $user);
        } 
        elseif ($session->step === 'awaiting_end_time') {
            return $this->handleEndTime($message, $session, $user);
        } 
        elseif ($session->step === 'awaiting_venue') {
            return $this->handleVenue($message, $session, $user);
        } 
        elseif ($session->step === 'awaiting_equipment') {
            return $this->handleEquipment($message, $session, $user);
        }
        elseif ($session->step === 'awaiting_department') {
            return $this->handleDepartment($message, $session, $user);
        }
        elseif ($session->step === 'awaiting_objectives') {
            return $this->handleObjectives($message, $session, $user);
        }
        elseif ($session->step === 'confirming_reservation') {
            return $this->handleConfirmation($message, $session, $user);
        }
        
        // Intent recognition
        if ($this->containsAny($message, ['create reservation', 'new reservation', 'book', 'reserve', 'schedule event', 'make a reservation', 'book venue'])) {
            return $this->startReservationFlow($session, $user);
        }
        
        elseif ($this->containsAny($message, ['status', 'my reservation', 'check reservation', 'reservation status', 'view reservation', 'my bookings'])) {
            return $this->handleCheckStatus($user);
        }
        
        elseif ($this->containsAny($message, ['cancel', 'delete reservation', 'remove reservation'])) {
            return $this->handleCancelReservation($message, $user, $session);
        }
        
        elseif ($this->containsAny($message, ['available', 'venues', 'establishments', 'what venues', 'list venues', 'show venues'])) {
            return $this->handleListVenues($user);
        }
        
        elseif ($this->containsAny($message, ['help', 'what can you do', 'commands', 'how to use', 'help me'])) {
            return $this->handleHelp();
        }
        
        elseif ($this->containsAny($message, ['hello', 'hi', 'hey', 'good morning', 'good afternoon', 'good evening', 'greetings'])) {
            return $this->handleGreeting($user);
        }
        
        else {
            return $this->handleUnknown();
        }
    }
    
    private function handleFileUpload($request, $session)
    {
        $uploadedFiles = [];
        foreach ($request->file('attachments') as $file) {
            $path = $file->store('chatbot_temp/' . Auth::id(), 'public');
            $uploadedFiles[] = $path;
        }
        
        $existingData = $session->data ?? [];
        $existingFiles = $existingData['attachments'] ?? [];
        
        // Merge files without duplicates (based on path)
        $allFiles = array_merge($existingFiles, $uploadedFiles);
        $allFiles = array_unique($allFiles);
        
        $existingData['attachments'] = $allFiles;
        $session->update(['data' => $existingData]);
        
        return response()->json([
            'message' => "✅ " . count($uploadedFiles) . " file(s) attached successfully!\n\n" .
                        "Total files: " . count($allFiles) . " file(s)."
        ]);
    }
    
    private function startReservationFlow($session, $user)
    {
        $session->update([
            'step' => 'awaiting_event_name',
            'data' => []
        ]);
        
        return response()->json([
            'intent' => 'start_reservation',
            'message' => "Let's create a reservation together! 📅\n\nWhat is the name of your event?",
            'action' => 'awaiting_input'
        ]);
    }
    
    private function handleEventName($message, $session, $user)
    {
        $data = $session->data ?? [];
        $data['event_name'] = ucwords($message);
        
        $session->update([
            'step' => 'awaiting_event_dates',
            'data' => $data
        ]);
        
        return response()->json([
            'intent' => 'awaiting_dates',
            'message' => "Great! Event name: *{$data['event_name']}*\n\nNow, let's set the dates.\n\nYou can select:\n• A single date (e.g., June 15, 2026)\n• Multiple dates (e.g., June 15, June 16, June 17)\n• A date range (e.g., June 15 to June 17)\n\nPlease enter the date(s) for your event:",
            'action' => 'awaiting_input'
        ]);
    }
    
    private function handleEventDates($message, $session, $user)
    {
        $dates = $this->parseDates($message);
        
        if (empty($dates)) {
            return response()->json([
                'message' => "I couldn't understand the dates. Please enter valid dates.\n\nExamples:\n• June 15, 2026\n• June 15, June 16, June 17\n• June 15 to June 17\n• tomorrow, next day"
            ]);
        }
        
        $data = $session->data ?? [];
        $data['event_dates'] = $dates;
        $data['is_multi_date'] = count($dates) > 1;
        
        $session->update([
            'step' => 'awaiting_start_time',
            'data' => $data
        ]);
        
        $dateDisplay = count($dates) === 1 
            ? Carbon::parse($dates[0])->format('F d, Y')
            : count($dates) . " dates (" . Carbon::parse($dates[0])->format('M d') . " - " . Carbon::parse(end($dates))->format('M d, Y') . ")";
        
        return response()->json([
            'intent' => 'awaiting_start_time',
            'message' => "Date(s) set: *{$dateDisplay}*\n\nWhat time should the event start?\n\n(Examples: 9:00 AM, 14:00, 2pm)",
            'action' => 'awaiting_input'
        ]);
    }
    
    private function handleStartTime($message, $session, $user)
    {
        $time = $this->parseTime($message);
        
        if (!$time) {
            return response()->json([
                'message' => "I couldn't understand that time. Please enter a valid time.\n\nExamples: 9:00 AM, 14:00, 2pm"
            ]);
        }
        
        $data = $session->data ?? [];
        $data['start_time'] = $time;
        
        $session->update([
            'step' => 'awaiting_end_time',
            'data' => $data
        ]);
        
        return response()->json([
            'intent' => 'awaiting_end_time',
            'message' => "Start time set to *" . Carbon::parse($time)->format('g:i A') . "*\n\nWhat time should the event end?",
            'action' => 'awaiting_input'
        ]);
    }
    
    private function handleEndTime($message, $session, $user)
    {
        $time = $this->parseTime($message);
        
        if (!$time) {
            return response()->json([
                'message' => "I couldn't understand that time. Please enter a valid time."
            ]);
        }
        
        $data = $session->data ?? [];
        $start = Carbon::parse($data['start_time']);
        $end = Carbon::parse($time);
        
        if ($end <= $start) {
            return response()->json([
                'message' => "End time must be after start time. Please enter a later time."
            ]);
        }
        
        $data['end_time'] = $time;
        
        $session->update([
            'step' => 'awaiting_venue',
            'data' => $data
        ]);
        
        $venues = Establishment::where('campus_id', $user->campus_id)
            ->where('is_active', true)
            ->take(10)
            ->get();
        
        $venueList = '';
        foreach ($venues as $venue) {
            $venueList .= "• {$venue->name} (Capacity: {$venue->capacity}, {$venue->type})\n";
        }
        
        return response()->json([
            'intent' => 'awaiting_venue',
            'message' => "Great! Here are the available venues at your campus:\n\n{$venueList}\n\nWhich venue would you like to reserve? Please type the name.",
            'action' => 'awaiting_input'
        ]);
    }
    
    private function handleVenue($message, $session, $user)
    {
        $venue = Establishment::where('campus_id', $user->campus_id)
            ->where('name', 'like', '%' . $message . '%')
            ->where('is_active', true)
            ->first();
        
        if (!$venue) {
            return response()->json([
                'message' => "I couldn't find a venue matching '{$message}'. Please try again."
            ]);
        }
        
        $data = $session->data ?? [];
        $data['establishment_id'] = $venue->id;
        $data['venue_name'] = $venue->name;
        $data['venue_capacity'] = $venue->capacity;
        $data['venue_type'] = $venue->type;
        
        $session->update([
            'step' => 'awaiting_equipment',
            'data' => $data
        ]);
        
        return response()->json([
            'intent' => 'awaiting_equipment',
            'message' => "Great choice! *{$venue->name}* (Capacity: {$venue->capacity}, {$venue->type})\n\nDo you need any equipment? (e.g., sound system, microphone, tables, chairs, projector)\n\nType 'none' if no equipment needed.",
            'action' => 'awaiting_input'
        ]);
    }
    
    private function handleEquipment($message, $session, $user)
    {
        $equipment = $message === 'none' ? [] : explode(',', $message);
        
        $data = $session->data ?? [];
        $data['equipment'] = array_map('trim', $equipment);
        
        $session->update([
            'step' => 'awaiting_department',
            'data' => $data
        ]);
        
        return response()->json([
            'intent' => 'awaiting_department',
            'message' => "Equipment noted! 📝\n\nWhat is your department or college?\n\n(e.g., College of Computer Studies, College of Business and Accountancy, etc.)",
            'action' => 'awaiting_input'
        ]);
    }
    
    private function handleDepartment($message, $session, $user)
    {
        $data = $session->data ?? [];
        $data['department'] = ucwords($message);
        
        $session->update([
            'step' => 'awaiting_objectives',
            'data' => $data
        ]);
        
        return response()->json([
            'intent' => 'awaiting_objectives',
            'message' => "Department set: *{$data['department']}*\n\nNow, please describe the objectives or purpose of your event.\n\n(What is this event about? What do you hope to achieve?)",
            'action' => 'awaiting_input'
        ]);
    }
    
    private function handleObjectives($message, $session, $user)
    {
        $data = $session->data ?? [];
        $data['objectives'] = ucfirst($message);
        
        $session->update([
            'step' => 'confirming_reservation',
            'data' => $data
        ]);
        
        $dates = $data['event_dates'];
        $dateDisplay = count($dates) === 1 
            ? Carbon::parse($dates[0])->format('F d, Y')
            : count($dates) . " dates (" . Carbon::parse($dates[0])->format('M d') . " - " . Carbon::parse(end($dates))->format('M d, Y') . ")";
        
        $start = Carbon::parse($data['start_time'])->format('g:i A');
        $end = Carbon::parse($data['end_time'])->format('g:i A');
        $equipmentList = empty($data['equipment']) ? 'None' : implode(', ', $data['equipment']);
        $attachmentsCount = isset($data['attachments']) ? count($data['attachments']) : 0;
        $attachmentsDisplay = $attachmentsCount > 0 ? "📎 {$attachmentsCount} file(s) attached" : "No attachments";
        $department = $data['department'] ?? 'Not specified';
        $objectives = $data['objectives'] ?? 'Not specified';
        
        return response()->json([
            'intent' => 'confirm_reservation',
            'message' => "📋 *Please confirm your reservation details:*\n\n" .
                        "🏷️ *Event:* {$data['event_name']}\n" .
                        "📅 *Date(s):* {$dateDisplay}\n" .
                        "⏰ *Time:* {$start} - {$end}\n" .
                        "📍 *Venue:* {$data['venue_name']} (Capacity: {$data['venue_capacity']}, {$data['venue_type']})\n" .
                        "🏛️ *Department:* {$department}\n" .
                        "🔧 *Equipment:* {$equipmentList}\n" .
                        "📝 *Objectives:* {$objectives}\n" .
                        "📎 *Attachments:* {$attachmentsDisplay}\n\n" .
                        "Type *CONFIRM* to submit your reservation, or *CANCEL* to start over.",
            'action' => 'confirm',
            'reservation_data' => $data
        ]);
    }
    
    private function handleConfirmation($message, $session, $user)
    {
        if (strtolower($message) === 'confirm') {
            $data = $session->data;
            $dates = $data['event_dates'];
            $attachmentPaths = [];
            
            if (isset($data['attachments']) && !empty($data['attachments'])) {
                foreach ($data['attachments'] as $tempPath) {
                    $newPath = 'reservations/' . date('Y/m') . '/' . basename($tempPath);
                    Storage::disk('public')->move($tempPath, $newPath);
                    $attachmentPaths[] = $newPath;
                }
            }
            
            sort($dates);
            
            $reservation = Reservation::create([
                'user_id' => $user->id,
                'establishment_id' => $data['establishment_id'],
                'campus_id' => $user->campus_id,
                'event_name' => $data['event_name'],
                'description' => $data['objectives'] ?? 'Created via AI Assistant',
                'event_date' => $dates[0],
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'status' => 'pending',
                'remarks' => json_encode([
                    'created_by' => 'chatbot',
                    'equipment' => $data['equipment'],
                    'department' => $data['department'] ?? 'N/A',
                    'user_type' => 'student',
                    'multiple_dates' => $dates,
                    'is_multi_date' => count($dates) > 1,
                    'attachments' => $attachmentPaths
                ])
            ]);
            
            $this->createNotification($reservation);
            
            $session->update([
                'step' => 'idle',
                'data' => []
            ]);
            
            $dateDisplay = count($dates) === 1 
                ? Carbon::parse($dates[0])->format('F d, Y')
                : count($dates) . " dates (" . Carbon::parse($dates[0])->format('M d') . " - " . Carbon::parse(end($dates))->format('M d, Y') . ")";
            
            return response()->json([
                'intent' => 'reservation_created',
                'message' => "✅ *Reservation Created Successfully!*\n\n" .
                            "Your reservation for *{$data['event_name']}* has been submitted for approval.\n\n" .
                            "📋 *Reference:* #{$reservation->id}\n" .
                            "📅 *Date(s):* {$dateDisplay}\n" .
                            "⏰ *Time:* " . Carbon::parse($data['start_time'])->format('g:i A') . " - " . Carbon::parse($data['end_time'])->format('g:i A') . "\n" .
                            "📍 *Venue:* {$data['venue_name']}\n\n" .
                            "You will receive an email notification once your reservation is approved.\n\n" .
                            "You can check your reservation status anytime by saying 'check status'!",
                'action' => 'done'
            ]);
        } 
        elseif (strtolower($message) === 'cancel') {
            if (isset($session->data['attachments'])) {
                foreach ($session->data['attachments'] as $tempPath) {
                    Storage::disk('public')->delete($tempPath);
                }
            }
            
            $session->update([
                'step' => 'idle',
                'data' => []
            ]);
            
            return response()->json([
                'intent' => 'cancelled',
                'message' => "Reservation cancelled. Would you like to start over? Just say 'create reservation'!"
            ]);
        } 
        else {
            return response()->json([
                'message' => "Please type *CONFIRM* to submit your reservation or *CANCEL* to start over."
            ]);
        }
    }
    
    private function handleCancelReservation($message, $user, $session)
    {
        preg_match('/\d+/', $message, $matches);
        $reservationId = $matches[0] ?? null;
        
        if ($reservationId) {
            $reservation = Reservation::where('user_id', $user->id)
                ->where('id', $reservationId)
                ->where('status', 'pending')
                ->first();
            
            if ($reservation) {
                $session->update([
                    'step' => 'confirming_cancel',
                    'data' => ['cancel_reservation_id' => $reservation->id]
                ]);
                
                return response()->json([
                    'intent' => 'confirm_cancel',
                    'message' => "Are you sure you want to cancel your reservation for '{$reservation->event_name}' on " . Carbon::parse($reservation->event_date)->format('M d, Y') . "?\n\nType *YES* to confirm or *NO* to keep it.",
                    'action' => 'confirm_cancel',
                    'reservation_id' => $reservation->id
                ]);
            } else {
                return response()->json([
                    'message' => "I couldn't find a pending reservation with ID #{$reservationId}. Only pending reservations can be cancelled."
                ]);
            }
        }
        
        $pendingReservations = Reservation::where('user_id', $user->id)
            ->where('status', 'pending')
            ->get();
        
        if ($pendingReservations->isEmpty()) {
            return response()->json([
                'message' => "You don't have any pending reservations to cancel."
            ]);
        }
        
        $message = "Here are your pending reservations:\n\n";
        foreach ($pendingReservations as $res) {
            $message .= "• #{$res->id}: {$res->event_name} on " . Carbon::parse($res->event_date)->format('M d, Y') . "\n";
        }
        $message .= "\nTo cancel, say 'cancel reservation #[ID]'";
        
        return response()->json([
            'message' => $message
        ]);
    }
    
    private function handleCancelConfirmation($message, $session, $user)
    {
        if (strtolower($message) === 'yes') {
            $reservationId = $session->data['cancel_reservation_id'] ?? null;
            $reservation = Reservation::where('user_id', $user->id)
                ->where('id', $reservationId)
                ->where('status', 'pending')
                ->first();
            
            if ($reservation) {
                $reservation->delete();
                $session->update(['step' => 'idle', 'data' => []]);
                
                return response()->json([
                    'message' => "✅ Reservation for '{$reservation->event_name}' has been cancelled successfully."
                ]);
            } else {
                $session->update(['step' => 'idle', 'data' => []]);
                
                return response()->json([
                    'message' => "❌ Could not cancel the reservation. It may have already been processed."
                ]);
            }
        } 
        elseif (strtolower($message) === 'no') {
            $session->update(['step' => 'idle', 'data' => []]);
            
            return response()->json([
                'message' => "Cancellation cancelled. Your reservation is safe! 😊"
            ]);
        }
        else {
            return response()->json([
                'message' => "Please type *YES* to confirm cancellation or *NO* to keep your reservation."
            ]);
        }
    }
    
    private function parseDates($input)
    {
        $input = strtolower(trim($input));
        $dates = [];
        
        if (strpos($input, ' to ') !== false) {
            $parts = explode(' to ', $input);
            if (count($parts) === 2) {
                $start = $this->parseDate($parts[0]);
                $end = $this->parseDate($parts[1]);
                if ($start && $end) {
                    $current = Carbon::parse($start);
                    $endDate = Carbon::parse($end);
                    while ($current <= $endDate) {
                        $dates[] = $current->format('Y-m-d');
                        $current->addDay();
                    }
                    return $dates;
                }
            }
        }
        
        if (strpos($input, ',') !== false) {
            $parts = explode(',', $input);
            foreach ($parts as $part) {
                $date = $this->parseDate(trim($part));
                if ($date) {
                    $dates[] = $date;
                }
            }
            if (!empty($dates)) {
                return $dates;
            }
        }
        
        $singleDate = $this->parseDate($input);
        if ($singleDate) {
            return [$singleDate];
        }
        
        return [];
    }
    
    private function parseDate($input)
    {
        $input = strtolower(trim($input));
        
        if ($input === 'tomorrow') {
            return Carbon::tomorrow()->format('Y-m-d');
        }
        
        if ($input === 'next week') {
            return Carbon::now()->addWeek()->format('Y-m-d');
        }
        
        if ($input === 'today') {
            return Carbon::today()->format('Y-m-d');
        }
        
        if (preg_match('/^([a-z]+)\s+(\d{1,2})$/', $input, $matches)) {
            try {
                $date = Carbon::parse($matches[1] . ' ' . $matches[2] . ' ' . date('Y'));
                return $date->format('Y-m-d');
            } catch (\Exception $e) {}
        }
        
        try {
            $date = Carbon::parse($input);
            return $date->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
    
    private function parseTime($input)
    {
        try {
            $time = Carbon::parse($input);
            return $time->format('H:i:s');
        } catch (\Exception $e) {
            return null;
        }
    }
    
    private function handleCheckStatus($user)
    {
        $reservations = Reservation::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        if ($reservations->isEmpty()) {
            return response()->json([
                'intent' => 'no_reservations',
                'message' => "You don't have any reservations yet. Would you like me to help you create one? Just say 'create reservation'!"
            ]);
        }
        
        $message = "📋 *Your Recent Reservations:*\n\n";
        foreach ($reservations as $res) {
            $remarks = json_decode($res->remarks, true);
            $multipleDates = $remarks['multiple_dates'] ?? [$res->event_date];
            $dateDisplay = count($multipleDates) > 1 
                ? count($multipleDates) . " dates"
                : Carbon::parse($res->event_date)->format('M d, Y');
            
            $statusIcon = $res->status == 'approved' ? '✅' : ($res->status == 'pending' ? '⏳' : '❌');
            $message .= "{$statusIcon} *{$res->event_name}*";
            if (count($multipleDates) > 1) {
                $message .= " ({$dateDisplay})";
            }
            $message .= "\n";
            $message .= "   📅 " . Carbon::parse($res->event_date)->format('M d, Y') . "\n";
            $message .= "   🕒 " . Carbon::parse($res->start_time)->format('g:i A') . " - " . Carbon::parse($res->end_time)->format('g:i A') . "\n";
            $message .= "   📍 {$res->establishment->name}\n";
            $message .= "   📊 Status: *" . strtoupper($res->status) . "*\n\n";
        }
        
        $message .= "Want to create a new reservation? Just say 'create reservation'!";
        
        return response()->json([
            'intent' => 'show_status',
            'message' => $message
        ]);
    }
    
    private function handleListVenues($user)
    {
        $establishments = Establishment::where('campus_id', $user->campus_id)
            ->where('is_active', true)
            ->get();
        
        $message = "🏛️ *Available Venues at {$user->campus->name}:*\n\n";
        
        $indoor = $establishments->where('type', 'Indoor');
        $outdoor = $establishments->where('type', 'Outdoor');
        
        if ($indoor->count() > 0) {
            $message .= "🏠 *Indoor Venues:*\n";
            foreach ($indoor->take(10) as $est) {
                $message .= "   • {$est->name} (Capacity: {$est->capacity})\n";
            }
            if ($indoor->count() > 10) {
                $message .= "   • And " . ($indoor->count() - 10) . " more indoor venues\n";
            }
            $message .= "\n";
        }
        
        if ($outdoor->count() > 0) {
            $message .= "🌳 *Outdoor Venues:*\n";
            foreach ($outdoor->take(5) as $est) {
                $message .= "   • {$est->name} (Capacity: {$est->capacity})\n";
            }
            if ($outdoor->count() > 5) {
                $message .= "   • And " . ($outdoor->count() - 5) . " more outdoor venues\n";
            }
            $message .= "\n";
        }
        
        $message .= "To book a venue, just say 'create reservation'!";
        
        return response()->json([
            'intent' => 'list_venues',
            'message' => $message
        ]);
    }
    
    private function handleGreeting($user)
    {
        $hour = Carbon::now()->hour;
        $greeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');
        
        return response()->json([
            'intent' => 'greeting',
            'message' => "{$greeting}, {$user->name}! 👋\n\nI'm your UCC-ERS AI Assistant. I can help you:\n\n✅ *Create reservations*\n✅ *Check status*\n✅ *List venues*\n✅ *Cancel reservations*\n✅ *Upload files*\n\nWhat would you like to do today?"
        ]);
    }
    
    private function handleHelp()
    {
        return response()->json([
            'intent' => 'help',
            'message' => "🤖 *UCC-ERS AI Assistant Help*\n\n📅 *Create Reservation* - 'create reservation'\n📊 *Check Status* - 'my reservation status'\n📍 *List Venues* - 'list venues'\n❌ *Cancel Reservation* - 'cancel reservation #123'\n📎 *Upload Files* - Click the 📎 button\n\nJust follow the prompts!"
        ]);
    }
    
    private function handleUnknown()
    {
        return response()->json([
            'intent' => 'unknown',
            'message' => "I'm not sure I understood that. 🤔\n\nTry:\n• 'create reservation'\n• 'my reservation status'\n• 'list venues'\n• 'help'"
        ]);
    }
    
    private function containsAny($message, $keywords)
    {
        foreach ($keywords as $keyword) {
            if (strpos($message, $keyword) !== false) {
                return true;
            }
        }
        return false;
    }
    
    private function createNotification($reservation)
    {
        try {
            $admins = User::whereIn('role', ['admin', 'super_admin'])->get();
            
            foreach ($admins as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'reservation_id' => $reservation->id,
                    'title' => '🤖 New AI-Assisted Reservation',
                    'message' => "New reservation created via AI Assistant by {$reservation->user->name}",
                    'type' => 'reservation',
                    'is_read' => false,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to create notification: ' . $e->getMessage());
        }
    }
    
    public function cancelReservation(Request $request)
    {
        $reservation = Reservation::where('user_id', Auth::id())
            ->where('id', $request->reservation_id)
            ->where('status', 'pending')
            ->first();
        
        if ($reservation) {
            $reservation->delete();
            return response()->json([
                'message' => "✅ Reservation for '{$reservation->event_name}' has been cancelled successfully."
            ]);
        }
        
        return response()->json([
            'message' => "❌ Could not cancel the reservation."
        ]);
    }
}