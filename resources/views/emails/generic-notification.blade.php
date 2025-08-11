<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - Bildirim</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: white;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #4A90E2;
        }
        .content {
            margin-bottom: 30px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #f0f0f0;
            font-size: 12px;
            color: #666;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #4A90E2;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .button:hover {
            background-color: #357ABD;
        }
        .info-box {
            background-color: #f8f9fa;
            border-left: 4px solid #4A90E2;
            padding: 15px;
            margin: 20px 0;
        }
        .text-muted {
            color: #6c757d;
        }
        .text-center {
            text-align: center;
        }
        h2 {
            color: #2c3e50;
            margin-bottom: 15px;
        }
        p {
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">{{ config('app.name') }}</div>
        </div>
        
        <div class="content">
            {!! nl2br(e($content)) !!}
            
            @if(isset($data['action_url']))
            <div class="text-center">
                <a href="{{ $data['action_url'] }}" class="button">
                    {{ $data['action_text'] ?? 'Görüntüle' }}
                </a>
            </div>
            @endif
            
            @if(isset($data['additional_info']))
            <div class="info-box">
                {!! $data['additional_info'] !!}
            </div>
            @endif
        </div>
        
        <div class="footer">
            <p class="text-muted">
                Bu e-posta {{ config('app.name') }} tarafından gönderilmiştir.
            </p>
            <p class="text-muted">
                © {{ date('Y') }} {{ config('app.name') }}. Tüm hakları saklıdır.
            </p>
            <p class="text-muted">
                Bildirim ayarlarınızı değiştirmek için <a href="{{ url('/notifications/settings') }}">buraya tıklayın</a>.
            </p>
        </div>
    </div>
</body>
</html>
