@extends('layouts.app')

@section('title', 'My Profile')

@section('page-title', 'My Profile')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Profile</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-4">
        <!-- Profile Card -->
        <div class="card">
            <div class="card-body text-center">
                <div class="mb-3">
                    @if($user->avatar)
                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" 
                             class="rounded-circle" style="width: 120px; height: 120px; object-fit: cover;">
                    @else
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center mx-auto" 
                             style="width: 120px; height: 120px;">
                            <span class="text-white" style="font-size: 48px;">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </span>
                        </div>
                    @endif
                </div>
                <h5>{{ $user->name }}</h5>
                <p class="text-muted">{{ $user->email }}</p>
                @if($user->phone)
                    <p class="text-muted"><i data-feather="phone" class="icon-xs"></i> {{ $user->phone }}</p>
                @endif
                <div class="d-flex gap-2 justify-content-center">
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                        <i data-feather="edit" class="icon-xs"></i> Edit Profile
                    </button>
                    <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                        <i data-feather="lock" class="icon-xs"></i> Change Password
                    </button>
                </div>
            </div>
        </div>

        <!-- Account Info -->
        <div class="card mt-3">
            <div class="card-header">
                <h6>Account Information</h6>
            </div>
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-6"><strong>Member Since:</strong></div>
                    <div class="col-6">{{ $user->created_at->format('M Y') }}</div>
                </div>
                <div class="row mb-2">
                    <div class="col-6"><strong>Last Login:</strong></div>
                    <div class="col-6">{{ $user->updated_at->diffForHumans() }}</div>
                </div>
                <div class="row mb-2">
                    <div class="col-6"><strong>Status:</strong></div>
                    <div class="col-6">
                        <span class="badge badge-light-success">Active</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Profile Details -->
        <div class="card">
            <div class="card-header">
                <h6>Profile Details</h6>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-sm-3"><strong>Full Name:</strong></div>
                    <div class="col-sm-9">{{ $user->name }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-3"><strong>Email:</strong></div>
                    <div class="col-sm-9">{{ $user->email }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-3"><strong>Phone:</strong></div>
                    <div class="col-sm-9">{{ $user->phone ?: 'Not provided' }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-3"><strong>Address:</strong></div>
                    <div class="col-sm-9">{{ $user->address ?: 'Not provided' }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-3"><strong>Account Created:</strong></div>
                    <div class="col-sm-9">{{ $user->created_at->format('d M Y, h:i A') }}</div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="card mt-3">
            <div class="card-header">
                <h6>Recent Activity</h6>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar-sm bg-primary rounded-circle me-3">
                        <i data-feather="log-in" class="icon-xs text-white"></i>
                    </div>
                    <div>
                        <p class="mb-0">Last login</p>
                        <small class="text-muted">{{ $user->updated_at->format('d M Y, h:i A') }}</small>
                    </div>
                </div>
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar-sm bg-success rounded-circle me-3">
                        <i data-feather="user" class="icon-xs text-white"></i>
                    </div>
                    <div>
                        <p class="mb-0">Profile last updated</p>
                        <small class="text-muted">{{ $user->updated_at->diffForHumans() }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="{{ old('name', $user->name) }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="{{ old('email', $user->email) }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="text" class="form-control" id="phone" name="phone" 
                               value="{{ old('phone', $user->phone) }}">
                    </div>
                    
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="3">{{ old('address', $user->address) }}</textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="avatar" class="form-label">Profile Picture</label>
                        <input type="file" class="form-control" id="avatar" name="avatar" accept="image/*">
                        @if($user->avatar)
                            <div class="mt-2">
                                <img src="{{ asset('storage/' . $user->avatar) }}" alt="Current Avatar" 
                                     class="img-thumbnail" style="max-height: 80px;">
                                <div class="mt-1">
                                    <a href="{{ route('admin.profile.delete-avatar') }}" 
                                       class="btn btn-outline-danger btn-sm"
                                       onclick="return confirm('Are you sure you want to remove your profile picture?')">
                                        Remove Picture
                                    </a>
                                </div>
                            </div>
                        @endif
                        <small class="text-muted">Max file size: 2MB. Supported formats: JPG, PNG</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.profile.update-password') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">New Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <small class="text-muted">Password must be at least 8 characters long</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Password</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .avatar-sm {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>
@endpush