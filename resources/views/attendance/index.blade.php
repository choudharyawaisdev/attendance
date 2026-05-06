<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Dashboard</title>

    <!-- Bootstrap 4 CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --orange-primary: #f97316;
            --orange-hover: #ea580c;
            --orange-light: #fff7ed;
            --dark-navy: #1e293b;
            --bg-gray: #f8fafc;
        }

        body {
            background-color: var(--bg-gray);
            font-family: 'Inter', sans-serif;
            color: #334155;
        }

        /* Card Styling */
        .attendance-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            background: #ffffff;
            overflow: hidden;
            margin-bottom: 2rem;
        }

        /* Buttons */
        .btn-orange {
            background-color: var(--orange-primary);
            color: white !important;
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-orange:hover {
            background-color: var(--orange-hover);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(249, 115, 22, 0.2);
        }

        .btn-outline-orange {
            border: 2px solid var(--orange-primary);
            color: var(--orange-primary) !important;
            background: transparent;
            border-radius: 8px;
            padding: 8px 18px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-outline-orange:hover {
            background-color: var(--orange-primary);
            color: white !important;
        }

        /* Table Design */
        .table-custom thead th {
            background-color: #f8fafc;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            border: none;
            padding: 1.25rem 1.5rem;
        }

        .table-custom tbody td {
            padding: 1.2rem 1.5rem;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
        }

        /* User Avatar */
        .user-avatar {
            width: 40px;
            height: 40px;
            background: var(--orange-light);
            color: var(--orange-primary);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            margin-right: 12px;
        }

        /* Badges */
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            display: inline-block;
        }

        .badge-present { background: #dcfce7; color: #15803d; }
        .badge-absent { background: #fee2e2; color: #b91c1c; }
        .badge-late { background: #fef9c3; color: #854d0e; }
        .badge-excused { background: #e0f2fe; color: #0369a1; }

        /* Action Buttons */
        .action-btn {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            border: 1px solid #e2e8f0;
            background: white;
            color: #64748b;
        }

        .action-btn:hover {
            color: var(--orange-primary);
            border-color: var(--orange-primary);
            text-decoration: none;
        }

        .action-btn.delete:hover {
            background: #fee2e2;
            color: #ef4444;
            border-color: #fecaca;
        }

        /* Pagination Override */
        .pagination .page-link {
            color: var(--orange-primary);
            border-radius: 8px;
            margin: 0 2px;
            border: 1px solid #e2e8f0;
        }

        .pagination .page-item.active .page-link {
            background-color: var(--orange-primary);
            border-color: var(--orange-primary);
            color: white;
        }
    </style>
</head>
<body>

    <div class="container py-5">
        
        <!-- Dashboard Header -->
        <div class="row align-items-center mb-5 px-2">
            <div class="col-md-8">
                <h1 class="h3 font-weight-bold text-dark mb-1">Attendance Dashboard</h1>
                <p class="text-muted mb-0">Track and manage employee attendance records with ease.</p>
            </div>
            <div class="col-md-4 text-md-right mt-3 mt-md-0">
                <a href="{{ route('attendance.sync') }}" class="btn btn-outline-orange mr-2">
                    <i class="fas fa-sync-alt mr-1"></i> Sync ZKTeco
                </a>
                <a href="{{ route('attendance.create') }}" class="btn btn-orange shadow-sm">
                    <i class="fas fa-plus mr-1"></i> New Entry
                </a>
            </div>
        </div>

        <!-- Success Alert -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-left: 4px solid #10b981 !important;">
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle mr-3 fa-lg text-success"></i>
                    <div>
                        <strong class="d-block">Success!</strong>
                        {{ session('success') }}
                    </div>
                </div>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <!-- Main Card Table -->
        <div class="attendance-card">
            <div class="table-responsive">
                <table class="table table-custom table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Date</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Status</th>
                            <th>Notes</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $attendance)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar">
                                            {{ strtoupper(substr($attendance->user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-weight-bold text-dark">{{ $attendance->user->name }}</div>
                                            <div class="small text-muted">{{ $attendance->user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-dark font-weight-500">
                                        {{ \Carbon\Carbon::parse($attendance->attendance_date)->format('D, M d') }}
                                    </div>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($attendance->attendance_date)->format('Y') }}</small>
                                </td>
                                <td>
                                    <span class="text-dark font-weight-bold">
                                        {{ $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('h:i A') : '--:--' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-dark font-weight-bold">
                                        {{ $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('h:i A') : '--:--' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge 
                                        {{ $attendance->status == 'Present' ? 'badge-present' : '' }}
                                        {{ $attendance->status == 'Absent' ? 'badge-absent' : '' }}
                                        {{ $attendance->status == 'Late' ? 'badge-late' : '' }}
                                        {{ $attendance->status == 'Excused' ? 'badge-excused' : '' }}">
                                        {{ $attendance->status }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-muted small text-truncate d-inline-block" style="max-width: 120px;" title="{{ $attendance->notes }}">
                                        {{ $attendance->notes ?? 'No notes' }}
                                    </span>
                                </td>
                                <td class="text-right">
                                    <a href="{{ route('attendance.edit', $attendance->id) }}" class="action-btn mr-1" title="Edit Record">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('attendance.destroy', $attendance->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-btn delete" title="Delete Record">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="mb-3">
                                        <i class="fas fa-calendar-times fa-4x text-muted opacity-25"></i>
                                    </div>
                                    <h5 class="text-muted">No attendance records found</h5>
                                    <p class="text-muted small">Try syncing from ZKTeco or adding a manual entry.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center">
            {{ $attendances->links() }}
        </div>

    </div>

    <!-- JS Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>