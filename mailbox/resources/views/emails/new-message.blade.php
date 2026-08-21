<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau message - Mailbox</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 20px;
            line-height: 1.6;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .content {
            padding: 30px;
        }
        .message-info {
            background-color: #f9fafb;
            border-left: 4px solid #4f46e5;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .message-info p {
            margin: 5px 0;
            color: #374151;
        }
        .message-info strong {
            color: #111827;
        }
        .message-body {
            color: #4b5563;
            white-space: pre-wrap;
            line-height: 1.8;
            margin: 20px 0;
        }
        .button {
            display: inline-block;
            background-color: #4f46e5;
            color: white;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 6px;
            font-weight: 500;
            margin-top: 20px;
            transition: background-color 0.3s;
        }
        .button:hover {
            background-color: #4338ca;
        }
        .footer {
            background-color: #f9fafb;
            padding: 20px;
            text-align: center;
            color: #6b7280;
            font-size: 12px;
            border-top: 1px solid #e5e7eb;
        }
        .footer a {
            color: #4f46e5;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📧 Nouveau Message</h1>
        </div>
        
        <div class="content">
            <p style="color: #374151; margin-bottom: 20px;">
                Bonjour, vous avez reçu un nouveau message dans votre boîte de réception.
            </p>
            
            <div class="message-info">
                <p><strong>De :</strong> {{ $senderName }}</p>
                <p><strong>Objet :</strong> {{ $mailboxMessage->subject }}</p>
                <p><strong>Date :</strong> {{ $mailboxMessage->created_at->format('d/m/Y H:i') }}</p>
            </div>
            
            <div class="message-body">
                {{ $mailboxMessage->body }}
            </div>
            
            <div style="text-align: center;">
                <a href="{{ $messageUrl }}" class="button">Répondre dans la Mailbox</a>
            </div>
        </div>
        
        <div class="footer">
            <p>Cet email a été envoyé automatiquement par l'application <strong>Mailbox</strong>.</p>
            <p>Si vous ne vous attendiez pas à recevoir ce message, vous pouvez l'ignorer.</p>
        </div>
    </div>
</body>
</html>