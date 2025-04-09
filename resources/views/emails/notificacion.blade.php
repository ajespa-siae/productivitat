<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: system-ui, -apple-system, sans-serif;
            line-height: 1.5;
            color: #1a202c;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #f97316;
            color: white;
            padding: 20px;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #ffffff;
            padding: 20px;
            border: 1px solid #e2e8f0;
            border-radius: 0 0 8px 8px;
        }
        .button {
            display: inline-block;
            background-color: #f97316;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin-top: 20px;
        }
        .footer {
            margin-top: 20px;
            color: #718096;
            font-size: 0.875rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Nova Notificació</h1>
        </div>
        <div class="content">
            <p>Hola,</p>
            <p>{{ $notificacion->mensaje }}</p>
            
            @if($notificacion->url)
                <a href="{{ $notificacion->url }}" class="button">Veure detalls</a>
            @endif

            <div class="footer">
                <p>Aquest és un missatge automàtic del sistema <strong>SIAE Avaluació de Rendiment</strong>. Si us plau, no responguis a aquest correu.</p>
            </div>
        </div>
    </div>
</body>
</html>
