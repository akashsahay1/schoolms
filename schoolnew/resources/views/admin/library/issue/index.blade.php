@extends('layouts.app')

@section('title', 'Book Issue')

@section('page-title', 'Library - Book Issue')

@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
	<li class="breadcrumb-item">Library</li>
	<li class="breadcrumb-item active">Book Issue</li>
@endsection

@section('content')
<div class="row">
	<div class="col-12">
		@if(session('success'))
			<div class="alert alert-success alert-dismissible fade show" role="alert">
				{{ session('success') }}
				<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
			</div>
		@endif

		<div class="card">
			<div class="card-header">
				<div class="d-flex justify-content-between align-items-center">
					<h5>Book Issue Records</h5>
					<a href="{{ route('admin.library.issue.create') }}" class="btn btn-primary">
						<i data-feather="plus" class="me-1"></i> Issue Book
					</a>
				</div>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table table-striped table-hover">
						<thead>
							<tr>
								<th>#</th>
								<th>Book</th>
								<th>Student</th>
								<th>Issue Date</th>
								<th>Due Date</th>
								<th>Return Date</th>
								<th>Status</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							@forelse($issues as $issue)
								<tr>
									<td>{{ $issues->firstItem() + $loop->index }}</td>
									<td><strong>{{ $issue->book->title }}</strong></td>
									<td>{{ $issue->student->full_name }}</td>
									<td>{{ $issue->issue_date->format('d M Y') }}</td>
									<td>{{ $issue->due_date->format('d M Y') }}</td>
									<td>{{ $issue->return_date ? $issue->return_date->format('d M Y') : '-' }}</td>
									<td>
										<span class="badge badge-light-{{ $issue->status === 'issued' ? 'warning' : 'success' }}">
											{{ ucfirst($issue->status) }}
										</span>
									</td>
									<td>
										@if($issue->status === 'issued')
											<button type="button" class="btn btn-sm btn-success return-book-btn" data-issue-id="{{ $issue->id }}" data-book-title="{{ $issue->book->title }}" data-student-name="{{ $issue->student->full_name }}" data-due-date="{{ $issue->due_date->format('Y-m-d') }}">
												<i data-feather="corner-down-left" class="me-1" style="width: 14px; height: 14px;"></i> Return
											</button>
											<form id="return-form-{{ $issue->id }}" action="{{ route('admin.library.issue.return', $issue) }}" method="POST" class="d-none">
												@csrf
												<input type="hidden" name="return_date" id="return-date-{{ $issue->id }}">
												<input type="hidden" name="fine_amount" id="fine-amount-{{ $issue->id }}">
											</form>
										@endif
									</td>
								</tr>
							@empty
								<tr>
									<td colspan="8" class="text-center py-4">
										<p class="text-muted">No issue records found.</p>
									</td>
								</tr>
							@endforelse
						</tbody>
					</table>
				</div>

				@if($issues->hasPages())
					<div class="mt-3">{{ $issues->links() }}</div>
				@endif
			</div>
		</div>
	</div>
</div>
@endsection

@push('scripts')
<script>
jQuery(document).ready(function() {
	jQuery('.return-book-btn').on('click', function() {
		var issueId = jQuery(this).data('issue-id');
		var bookTitle = jQuery(this).data('book-title');
		var studentName = jQuery(this).data('student-name');
		var dueDate = jQuery(this).data('due-date');
		var today = new Date().toISOString().split('T')[0];

		Swal.fire({
			title: 'Return Book',
			html: `
				<div class="text-start mb-3">
					<p class="mb-1"><strong>Book:</strong> ${bookTitle}</p>
					<p class="mb-0"><strong>Student:</strong> ${studentName}</p>
				</div>
				<div class="mb-3 text-start">
					<label class="form-label">Return Date <span class="text-danger">*</span></label>
					<input type="date" id="swal-return-date" class="form-control" value="${today}" required>
				</div>
				<div class="text-start">
					<label class="form-label">Fine Amount (₹)</label>
					<input type="number" id="swal-fine-amount" class="form-control" value="0" min="0" step="0.01" placeholder="0.00">
					<small class="text-muted">Due date was: ${dueDate}</small>
				</div>
			`,
			icon: 'question',
			showCancelButton: true,
			confirmButtonColor: '#198754',
			cancelButtonColor: '#6c757d',
			confirmButtonText: 'Confirm Return',
			cancelButtonText: 'Cancel',
			preConfirm: () => {
				const returnDate = document.getElementById('swal-return-date').value;
				const fineAmount = document.getElementById('swal-fine-amount').value;

				if (!returnDate) {
					Swal.showValidationMessage('Please enter the return date');
					return false;
				}

				return { returnDate: returnDate, fineAmount: fineAmount || 0 };
			}
		}).then((result) => {
			if (result.isConfirmed) {
				jQuery('#return-date-' + issueId).val(result.value.returnDate);
				jQuery('#fine-amount-' + issueId).val(result.value.fineAmount);
				jQuery('#return-form-' + issueId).submit();
			}
		});
	});
});
</script>
@endpush
