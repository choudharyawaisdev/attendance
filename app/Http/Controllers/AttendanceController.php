<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index()
    {
        $attendances = Attendance::with('user')->latest()->paginate(10);
        return view('attendance.index', compact('attendances'));
    }

    public function create()
    {
        $users = User::all();
        return view('attendance.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'attendance_date' => 'required|date',
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i',
            'status' => 'required|in:Present,Absent,Late,Excused',
            'notes' => 'nullable|string',
        ]);

        Attendance::create($request->all());

        return redirect()->route('attendance.index')->with('success', 'Attendance recorded successfully.');
    }

    public function edit(Attendance $attendance)
    {
        $users = User::all();
        return view('attendance.edit', compact('attendance', 'users'));
    }

    public function update(Request $request, Attendance $attendance)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'attendance_date' => 'required|date',
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i',
            'status' => 'required|in:Present,Absent,Late,Excused',
            'notes' => 'nullable|string',
        ]);

        $attendance->update($request->all());

        return redirect()->route('attendance.index')->with('success', 'Attendance updated successfully.');
    }

    public function destroy(Attendance $attendance)
    {
        $attendance->delete();
        return redirect()->route('attendance.index')->with('success', 'Attendance deleted successfully.');
    }
}
