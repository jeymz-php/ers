<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
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
        $subject = $this->status === 'approved' 
            ? 'Your Reservation Has Been Approved - UCC-ERS'
            : 'Update on Your Reservation - UCC-ERS';
            
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
        
        // Attach PDF report if status is approved
        if ($this->status === 'approved') {
            $remarks = json_decode($this->reservation->remarks, true);
            $equipment = $remarks['equipment'] ?? [];
            $userType = $remarks['user_type'] ?? 'N/A';
            $department = $remarks['department'] ?? 'N/A';
            
            $nameParts = explode(' ', $this->reservation->user->name);
            $firstName = $nameParts[0] ?? '';
            $lastName = end($nameParts) ?? '';
            
            $data = [
                'reservation' => $this->reservation,
                'equipment' => $equipment,
                'userType' => $userType,
                'department' => $department,
                'firstName' => $firstName,
                'lastName' => $lastName,
                'generated_date' => now()->format('F d, Y h:i A'),
            ];
            
            $pdf = Pdf::loadView('reports.single-reservation', $data);
            $pdfContent = $pdf->output();
            
            $attachments[] = Attachment::fromData(fn () => $pdfContent, 'reservation_' . $this->reservation->id . '.pdf')
                ->withMime('application/pdf');
        }
        
        return $attachments;
    }
}