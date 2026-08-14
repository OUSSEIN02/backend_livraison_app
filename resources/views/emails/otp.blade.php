<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification OTP</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f7;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        .header {
            background: linear-gradient(135deg, #7c3aed 0%, #8b5cf6 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 40px 30px;
            text-align: center;
        }
        .content h2 {
            color: #1f2937;
            margin-bottom: 16px;
        }
        .content p {
            color: #6b7280;
            line-height: 1.6;
            margin-bottom: 24px;
        }
        .otp-code {
            display: inline-block;
            background: #f5f3ff;
            border: 2px dashed #7c3aed;
            border-radius: 8px;
            padding: 20px 40px;
            font-size: 36px;
            font-weight: 700;
            letter-spacing: 8px;
            color: #7c3aed;
            margin: 20px 0;
        }
        .warning {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 16px;
            margin-top: 24px;
            text-align: left;
            border-radius: 4px;
        }
        .warning p {
            margin: 0;
            color: #92400e;
            font-size: 14px;
        }
        .footer {
            background: #f9fafb;
            padding: 20px 30px;
            text-align: center;
            color: #9ca3af;
            font-size: 12px;
        }
        .footer a {
            color: #7c3aed;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔐 Vérification de votre compte</h1>
        </div>
        
        <div class="content">
            <h2>Bonjour {{ $companyName ?? 'futur vendeur' }},</h2>
            <p>
                Vous avez demandé un code de vérification pour finaliser votre inscription 
                sur <strong>Gabon Livraison Express</strong>.
            </p>
            <p>Votre code de vérification est :</p>
            
            <div class="otp-code">{{ $otp }}</div>
            
            <p>Ce code expirera dans <strong>10 minutes</strong>.</p>
            
            <div class="warning">
                <p>
                    ⚠️ <strong>Important :</strong> Ne partagez jamais ce code avec qui que ce soit. 
                    Notre équipe ne vous le demandera jamais par téléphone ou message.
                </p>
            </div>
        </div>
        
        <div class="footer">
            <p>
                © 2026 Gabon Livraison Express - Tous droits réservés<br>
                <a href="https://gabonlivraison.com">gabonlivraison.com</a>
            </p>
        </div>
    </div>
</body>
</html>