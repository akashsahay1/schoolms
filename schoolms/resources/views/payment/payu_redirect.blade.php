<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirecting to PayU...</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f5f5f5;
        }
        .loading-container {
            text-align: center;
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        h2 {
            color: #333;
            margin-bottom: 10px;
        }
        p {
            color: #666;
        }
    </style>
</head>
<body>
    <div class="loading-container">
        <div class="spinner"></div>
        <h2>Redirecting to PayU</h2>
        <p>Please wait while we redirect you to the payment gateway...</p>
    </div>

    <form id="payu_form" action="{{ $actionUrl }}" method="POST" style="display: none;">
        @foreach($formData as $key => $value)
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endforeach
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('payu_form').submit();
        });
    </script>
</body>
</html>
