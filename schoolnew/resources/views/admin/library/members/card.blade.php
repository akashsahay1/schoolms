<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Library Card - {{ $member->member_id }}</title>
	<style>
		* {
			margin: 0;
			padding: 0;
			box-sizing: border-box;
		}
		body {
			font-family: 'Arial', sans-serif;
			background: #f5f5f5;
			padding: 20px;
		}
		.card-container {
			display: flex;
			justify-content: center;
			align-items: center;
			min-height: 100vh;
		}
		.library-card {
			width: 340px;
			height: 215px;
			background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
			border-radius: 15px;
			padding: 20px;
			color: white;
			position: relative;
			box-shadow: 0 10px 30px rgba(0,0,0,0.2);
		}
		.library-card::before {
			content: '';
			position: absolute;
			top: 0;
			right: 0;
			width: 100px;
			height: 100px;
			background: rgba(255,255,255,0.1);
			border-radius: 0 15px 0 100%;
		}
		.card-header {
			display: flex;
			justify-content: space-between;
			align-items: flex-start;
			margin-bottom: 15px;
		}
		.school-name {
			font-size: 12px;
			font-weight: bold;
			text-transform: uppercase;
			letter-spacing: 1px;
		}
		.card-title {
			font-size: 10px;
			opacity: 0.8;
			margin-top: 3px;
		}
		.card-logo {
			width: 40px;
			height: 40px;
			background: white;
			border-radius: 50%;
			display: flex;
			align-items: center;
			justify-content: center;
		}
		.card-logo svg {
			width: 24px;
			height: 24px;
			fill: #667eea;
		}
		.card-body {
			display: flex;
			gap: 15px;
		}
		.member-photo {
			width: 70px;
			height: 70px;
			border-radius: 10px;
			background: white;
			overflow: hidden;
			flex-shrink: 0;
		}
		.member-photo img {
			width: 100%;
			height: 100%;
			object-fit: cover;
		}
		.member-info {
			flex: 1;
		}
		.member-name {
			font-size: 16px;
			font-weight: bold;
			margin-bottom: 5px;
		}
		.member-id {
			font-size: 14px;
			font-weight: 600;
			background: rgba(255,255,255,0.2);
			padding: 3px 8px;
			border-radius: 4px;
			display: inline-block;
			margin-bottom: 5px;
		}
		.member-type {
			font-size: 11px;
			opacity: 0.9;
		}
		.card-footer {
			display: flex;
			justify-content: space-between;
			margin-top: 15px;
			padding-top: 10px;
			border-top: 1px solid rgba(255,255,255,0.2);
			font-size: 10px;
		}
		.footer-item {
			text-align: center;
		}
		.footer-label {
			opacity: 0.7;
			margin-bottom: 2px;
		}
		.footer-value {
			font-weight: 600;
		}
		.status-badge {
			position: absolute;
			top: 10px;
			right: 60px;
			font-size: 9px;
			padding: 2px 8px;
			border-radius: 10px;
			background: rgba(255,255,255,0.3);
		}
		.status-active { background: #27ae60; }
		.status-expired { background: #f39c12; }
		.status-suspended { background: #e74c3c; }

		@media print {
			body {
				background: white;
				padding: 0;
			}
			.card-container {
				min-height: auto;
			}
			.no-print {
				display: none !important;
			}
		}
	</style>
</head>
<body>
	<div class="no-print" style="text-align: center; margin-bottom: 20px;">
		<button onclick="window.print()" style="padding: 10px 30px; background: #667eea; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px;">
			Print Card
		</button>
		<button onclick="window.close()" style="padding: 10px 30px; background: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; margin-left: 10px;">
			Close
		</button>
	</div>

	<div class="card-container">
		<div class="library-card">
			<span class="status-badge status-{{ $member->status }}">{{ ucfirst($member->status) }}</span>

			<div class="card-header">
				<div>
					<div class="school-name">{{ config('app.name', 'School Name') }}</div>
					<div class="card-title">Library Membership Card</div>
				</div>
				<div class="card-logo">
					<svg viewBox="0 0 24 24">
						<path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
					</svg>
				</div>
			</div>

			<div class="card-body">
				<div class="member-photo">
					@if($member->memberable)
						<img src="{{ $member->memberable->photo_url ?? asset('assets/images/user/user.png') }}" alt="Photo">
					@else
						<img src="{{ asset('assets/images/user/user.png') }}" alt="Photo">
					@endif
				</div>
				<div class="member-info">
					<div class="member-name">{{ $member->member_name }}</div>
					<div class="member-id">{{ $member->member_id }}</div>
					<div class="member-type">{{ $member->member_type }}</div>
				</div>
			</div>

			<div class="card-footer">
				<div class="footer-item">
					<div class="footer-label">Issue Date</div>
					<div class="footer-value">{{ $member->membership_start->format('d/m/Y') }}</div>
				</div>
				<div class="footer-item">
					<div class="footer-label">Valid Until</div>
					<div class="footer-value">{{ $member->membership_end?->format('d/m/Y') ?? 'Lifetime' }}</div>
				</div>
				<div class="footer-item">
					<div class="footer-label">Max Books</div>
					<div class="footer-value">{{ $member->max_books_allowed }}</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>
