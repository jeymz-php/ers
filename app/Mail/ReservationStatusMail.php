<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class ReservationStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $reservation;
    public $status;
    public $reason;

    public function __construct($reservation, $status, $reason = null)
    {
        $this->reservation = $reservation;
        $this->status = $status;
        $this->reason = $reason;
    }

    public function envelope(): Envelope
    {
        $subject = match ($this->status) {
            'approved' => 'Your Reservation Has Been Approved - UCC-ERS',
            'updated' => 'Your Reservation Has Been Updated - UCC-ERS',
            'rejected' => 'Update on Your Reservation - UCC-ERS',
            default => 'Reservation Update - UCC-ERS',
        };
            
        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reservation-status',
        );
    }

    public function attachments(): array
    {
        $attachments = [];
        
        // Attach PDF report for approved or updated emails
        if (in_array($this->status, ['approved', 'updated'])) {
            $remarks = json_decode($this->reservation->remarks, true);
            $equipment = $remarks['equipment'] ?? [];
            $userType = $remarks['user_type'] ?? 'N/A';
            $department = $remarks['department'] ?? 'N/A';
            $multipleDates = $remarks['multiple_dates'] ?? [$this->reservation->event_date];
            
            $data = [
                'reservation' => $this->reservation,
                'equipment' => $equipment,
                'userType' => $userType,
                'department' => $department,
                'multipleDates' => $multipleDates,
                'generated_date' => now()->format('F d, Y h:i A'),
            ];
            
            $pdf = Pdf::loadView('reports.single-reservation', $data);
            $pdfContent = $pdf->output();
            
            $attachments[] = \Illuminate\Mail\Mailables\Attachment::fromData(fn () => $pdfContent, 'reservation_' . $this->reservation->reservation_code . '.pdf')
                ->withMime('application/pdf');
        }
        
        return $attachments;
    }
}