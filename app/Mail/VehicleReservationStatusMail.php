<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class VehicleReservationStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $reservation;
    public $status;
    public $reason;

    /**
     * @param  \App\Models\VehicleReservation  $reservation
     * @param  string  $status  pending|approved|rejected
     * @param  string|null  $reason
     */
    public function __construct($reservation, $status, $reason = null)
    {
        $this->reservation = $reservation;
        $this->status = $status;
        $this->reason = $reason;
    }

    public function envelope(): Envelope
    {
        $subject = match ($this->status) {
            'pending' => 'Pickup Vehicle Reservation Received - UCC-ERS',
            'approved' => 'Your Pickup Vehicle Reservation Has Been Approved - UCC-ERS',
            'rejected' => 'Update on Your Pickup Vehicle Reservation - UCC-ERS',
            default => 'Pickup Vehicle Reservation Update - UCC-ERS',
        };

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.vehicle-reservation-status',
        );
    }

    public function attachments(): array
    {
        $data = [
            'reservation' => $this->reservation,
            'generated_date' => now()->format('F d, Y h:i A'),
        ];

        $pdf = Pdf::loadView('reports.single-vehicle-reservation', $data);
        $pdfContent = $pdf->output();

        return [
            \Illuminate\Mail\Mailables\Attachment::fromData(fn () => $pdfContent, 'vehicle_reservation_' . $this->reservation->reservation_code . '.pdf')
                ->withMime('application/pdf'),
        ];
    }
}