<?php
namespace App\Services;

use App\Models\{Registration, Payment};
use Illuminate\Support\Str;

class PaymentService
{
    public function process(Registration $registration, string $gateway, array $data): Payment
    {
        $payment = Payment::create([
            'registration_id' => $registration->id,
            'user_id'         => $registration->user_id,
            'amount'          => $registration->final_amount,
            'gateway'         => $gateway,
            'status'          => 'completed',
            'paid_at'         => now(),
            'gateway_transaction_id' => strtoupper($gateway) . '-' . strtoupper(Str::random(12)),
            'gateway_response' => [
                'status'  => 'completed',
                'message' => 'Payment processed successfully',
                'gateway' => $gateway,
            ],
        ]);

        // Update registration status
        $registration->update([
            'payment_status' => 'paid',
            'status'         => 'confirmed',
        ]);

        // Generate tickets immediately
        app(TicketService::class)->generateTickets($registration);

        // Send confirmation
        try {
            app(NotificationService::class)->sendTicketConfirmation($registration);
        } catch (\Exception $e) {
            // Silently fail if mail not configured
        }

        return $payment;
    }

    public function refund(Payment $payment): Payment
    {
        $payment->update(['status' => 'refunded']);
        $payment->registration->update(['payment_status' => 'refunded']);

        try {
            app(NotificationService::class)->sendRefundConfirmation($payment);
        } catch (\Exception $e) {}

        return $payment;
    }

    public function generateReceiptPdf(Payment $payment)
    {
        $payment->load('registration.event', 'registration.ticketType', 'user');
        $pdf = app('dompdf.wrapper');
        $pdf->loadView('pdf.receipt', compact('payment'));
        return $pdf->download('receipt-' . $payment->transaction_id . '.pdf');
    }
}