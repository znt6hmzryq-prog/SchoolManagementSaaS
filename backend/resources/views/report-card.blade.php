<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; color: #222; }
        .header { text-align: center; margin-bottom: 20px; }
        .section { margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 6px 8px; border: 1px solid #ddd; }
        .small { font-size: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $school ?? config('app.name') }}</h1>
        <h2>Student Report Card</h2>
    </div>

    <div class="section">
        <strong>Student:</strong> {{ $student->first_name }} {{ $student->last_name }}<br />
        <strong>Class:</strong> {{ optional($student->section->classRoom)->name }} - {{ optional($student->section)->name }}<br />
        <strong>Date:</strong> {{ $date }}
    </div>

    <div class="section">
        <h3>Subject Grades</h3>
        <table>
            <thead>
                <tr>
                    <th>Subject</th>
                    <th>Average</th>
                    <th>Assessments</th>
                </tr>
            </thead>
            <tbody>
            @foreach($subjects as $s)
                <tr>
                    <td>{{ $s['subject'] }}</td>
                    <td class="small">{{ $s['average'] }}</td>
                    <td class="small">{{ $s['assessments'] }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <h3>Attendance Summary</h3>
        <p>Total: {{ $attendance['total'] }}, Present: {{ $attendance['present'] }}, Absent: {{ $attendance['absent'] }}</p>
    </div>

    <div class="section">
        <h3>Teacher Comments</h3>
        <p>{{ $ai_comment }}</p>
    </div>

    <div class="section">
        <p>___________________________</p>
        <p>Teacher Signature: {{ optional($teacher)->name ?? '' }}</p>
    </div>

</body>
</html>
