<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Record New Attendance') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-5">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-primary text-white py-3">
                                <h5 class="card-title mb-0">Attendance Form</h5>
                            </div>
                            <div class="card-body p-4">
                                <form action="{{ route('attendance.store') }}" method="POST">
                                    @csrf

                                    <div class="mb-3">
                                        <label for="user_id" class="form-label fw-bold">Select User</label>
                                        <select name="user_id" id="user_id" class="form-select @error('user_id') is-invalid @enderror">
                                            <option value="">-- Select a User --</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                                    {{ $user->name }} ({{ $user->email }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('user_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <label for="attendance_date" class="form-label fw-bold">Date</label>
                                            <input type="date" name="attendance_date" id="attendance_date" class="form-control @error('attendance_date') is-invalid @enderror" value="{{ old('attendance_date', date('Y-m-d')) }}">
                                            @error('attendance_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="check_in" class="form-label fw-bold">Check In Time</label>
                                            <input type="time" name="check_in" id="check_in" class="form-control @error('check_in') is-invalid @enderror" value="{{ old('check_in') }}">
                                            @error('check_in')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label for="check_out" class="form-label fw-bold">Check Out Time</label>
                                            <input type="time" name="check_out" id="check_out" class="form-control @error('check_out') is-invalid @enderror" value="{{ old('check_out') }}">
                                            @error('check_out')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="status" class="form-label fw-bold">Status</label>
                                        <div class="d-flex gap-3">
                                            @foreach(['Present', 'Absent', 'Late', 'Excused'] as $status)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="status" id="status_{{ $status }}" value="{{ $status }}" {{ old('status', 'Present') == $status ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="status_{{ $status }}">
                                                        {{ $status }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                        @error('status')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-4">
                                        <label for="notes" class="form-label fw-bold">Notes (Optional)</label>
                                        <textarea name="notes" id="notes" rows="3" class="form-control @error('notes') is-invalid @enderror" placeholder="Add any comments here...">{{ old('notes') }}</textarea>
                                        @error('notes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                        <a href="{{ route('attendance.index') }}" class="btn btn-light px-4">Cancel</a>
                                        <button type="submit" class="btn btn-primary px-5 shadow-sm">Save Attendance</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
