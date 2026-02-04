<!DOCTYPE html>
<html>
<head>
    <title>Invoice Received</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    
    <h2>Hello, {{ $invoice->client->name }}</h2>

    <p>Please find attached the details for your latest invoice.</p>

    <div style="background: #f3f4f6; padding: 15px; border-radius: 8px; margin: 20px 0;">
        <p><strong>Invoice ID:</strong> #{{ str_pad($invoice->id, 4, '0', STR_PAD_LEFT) }}</p>
        <p><strong>Project:</strong> {{ $invoice->project->project_name }}</p>
        <p><strong>Due Date:</strong> {{ \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') }}</p>
        <h3 style="color: #2563eb;">Total Due: ₱{{ number_format($invoice->total_amount, 2) }}</h3>
    </div>

    <p>Please arrange payment by the due date. If you have any questions, feel free to reply to this email.</p>

    <p>Thank you,<br>
    <strong>Tanaman Team</strong></p>

</body>
</html>