<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>@yield('title', config('app.name'))</title>
	<style>
		body {
			font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
			line-height: 1.6;
			color: #333;
			max-width: 600px;
			margin: 0 auto;
			padding: 20px;
			background-color: #f5f5f5;
		}
		.email-container {
			background-color: #ffffff;
			border-radius: 10px;
			box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
			overflow: hidden;
		}
		.header {
			background: linear-gradient(135deg, {{ $headerColor ?? '#7366ff' }} 0%, {{ $headerColorDark ?? '#5a52d5' }} 100%);
			color: #ffffff;
			padding: 30px;
			text-align: center;
		}
		.header .icon { font-size: 48px; margin-bottom: 15px; }
		.header h1 { margin: 0; font-size: 22px; font-weight: 600; }
		.content { padding: 30px; }
		.greeting { font-size: 18px; margin-bottom: 20px; }
		.details-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
		.details-table td { padding: 10px 15px; border-bottom: 1px solid #eee; }
		.details-table td:first-child { font-weight: 600; color: #666; width: 40%; }
		.message-box {
			background-color: #f8f9fa;
			border-left: 4px solid #7366ff;
			padding: 15px 20px;
			margin: 20px 0;
			border-radius: 0 8px 8px 0;
		}
		.btn {
			display: inline-block;
			padding: 12px 30px;
			background: linear-gradient(135deg, #7366ff 0%, #5a52d5 100%);
			color: #ffffff !important;
			text-decoration: none;
			border-radius: 25px;
			font-weight: 600;
			margin-top: 15px;
		}
		.badge {
			display: inline-block;
			padding: 5px 15px;
			border-radius: 15px;
			font-weight: 600;
			font-size: 13px;
		}
		.badge-danger { background-color: #f8d7da; color: #721c24; }
		.badge-success { background-color: #d4edda; color: #155724; }
		.badge-warning { background-color: #fff3cd; color: #856404; }
		.badge-info { background-color: #d1ecf1; color: #0c5460; }
		.badge-primary { background-color: #e0dfff; color: #5a52d5; }
		.footer {
			background-color: #f8f9fa;
			padding: 20px 30px;
			text-align: center;
			font-size: 13px;
			color: #666;
		}
		.footer a { color: #7366ff; text-decoration: none; }
	</style>
</head>
<body>
	<div class="email-container">
		<div class="header">
			<div class="icon">@yield('icon', '🔔')</div>
			<h1>@yield('heading')</h1>
		</div>
		<div class="content">
			@yield('content')
		</div>
		<div class="footer">
			<p>This is an automated email from {{ config('app.name') }}.</p>
			<p>If you have any questions, please contact the school administration.</p>
			<p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
		</div>
	</div>
</body>
</html>
