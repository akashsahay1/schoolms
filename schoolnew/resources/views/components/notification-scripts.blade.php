<script>
	// Notification System
	(function() {
		var notificationColors = {
			'success': '#28a745',
			'danger': '#dc3545',
			'warning': '#ffc107',
			'info': '#17a2b8',
			'primary': '#7366ff'
		};

		function fetchNotifications() {
			jQuery.get('{{ route("notifications.index") }}', function(data) {
				var badge = jQuery('.notification-badge');
				var list = jQuery('#notification-list');
				var markAllBtn = jQuery('.mark-all-read');

				// Update badge
				if (data.unread_count > 0) {
					badge.text(data.unread_count).show();
					markAllBtn.show();
				} else {
					badge.hide();
					markAllBtn.hide();
				}

				// Update notification list
				list.empty();
				if (data.notifications.length === 0) {
					list.append('<li class="text-center no-notifications"><p class="text-muted">No new notifications</p></li>');
				} else {
					jQuery.each(data.notifications, function(i, n) {
						var color = notificationColors[n.data.color] || notificationColors['primary'];
						var isUnread = !n.read_at;
						var bgStyle = isUnread ? 'background-color: #f8f9fa;' : '';
						var html = '<li class="notification-item" data-id="' + n.id + '" style="cursor:pointer; border-left: 3px solid ' + color + '; padding: 10px 15px; ' + bgStyle + '">' +
							'<div class="d-flex justify-content-between align-items-start">' +
							'<div>' +
							'<p class="mb-0 f-w-600 f-14" style="color: #2c323f;">' + n.data.title + '</p>' +
							'<p class="mb-0 f-12" style="color: #6c757d;">' + n.data.message + '</p>' +
							'</div>' +
							(isUnread ? '<span class="badge bg-primary rounded-circle" style="width:8px;height:8px;padding:0;"></span>' : '') +
							'</div>' +
							'<small class="text-muted f-10">' + n.created_at + '</small>' +
							'</li>';
						list.append(html);
					});
				}
			});
		}

		jQuery(document).ready(function() {
			// Fetch on page load
			fetchNotifications();

			// Poll every 30 seconds
			setInterval(fetchNotifications, 30000);

			// Mark single notification as read
			jQuery(document).on('click', '.notification-item', function() {
				var id = jQuery(this).data('id');
				jQuery.post('{{ route("notifications.read", ["id" => "__ID__"]) }}'.replace('__ID__', id), function(data) {
					if (data.success && data.url && data.url !== '#') {
						window.location.href = data.url;
					} else {
						fetchNotifications();
					}
				});
			});

			// Mark all as read
			jQuery(document).on('click', '.mark-all-read', function(e) {
				e.preventDefault();
				e.stopPropagation();
				jQuery.post('{{ route("notifications.read-all") }}', function(data) {
					if (data.success) {
						fetchNotifications();
					}
				});
			});
		});
	})();
</script>
