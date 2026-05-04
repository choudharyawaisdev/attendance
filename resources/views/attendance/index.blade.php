<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Attendance Records') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-5">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="h4 mb-0 text-primary">List of Attendances</h3>
                    <a href="{{ route('attendance.create') }}" class="btn btn-primary shadow-sm">
                        <i class="bi bi-plus-circle me-1"></i> Add New Attendance
                    </a>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
                        <strong>Success!</strong> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover align-middle border text-nowrap">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>User Name</th>
                                <th>Date</th>
                                <th>Check In</th>
                                <th>Check Out</th>
                                <th>Status</th>
                                <th>Notes</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attendances as $attendance)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="ms-2">
                                                <div class="fw-bold text-dark">{{ $attendance->user->name }}</div>
                                                <small class="text-muted">{{ $attendance->user->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($attendance->attendance_date)->format('M d, Y') }}</td>
                                    <td>{{ $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('h:i A') : '-' }}</td>
                                    <td>{{ $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('h:i A') : '-' }}</td>
                                    <td>
                                        <span class="badge rounded-pill 
                                            {{ $attendance->status == 'Present' ? 'bg-success' : '' }}
                                            {{ $attendance->status == 'Absent' ? 'bg-danger' : '' }}
                                            {{ $attendance->status == 'Late' ? 'bg-warning text-dark' : '' }}
                                            {{ $attendance->status == 'Excused' ? 'bg-info text-dark' : '' }}">
                                            {{ $attendance->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-truncate d-inline-block" style="max-width: 150px;" title="{{ $attendance->notes }}">
                                            {{ $attendance->notes ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group shadow-sm">
                                            <a href="{{ route('attendance.edit', $attendance->id) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                                <i class="bi bi-pencil"></i> Edit
                                            </a>
                                            <form action="{{ route('attendance.destroy', $attendance->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this record?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                    <i class="bi bi-trash"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <i class="bi bi-calendar-x display-4 d-block mb-3"></i>
                                        No attendance records found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $attendances->links() }}
                </div>
            </div>
        </div>
    </div>
    
    <!-- Link Bootstrap Icons if not present -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</x-app-layout>
