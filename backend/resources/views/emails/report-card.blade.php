<html>
<body>
    <p>Dear {{ $student->parent_name ?? $student->first_name }},</p>

    <p>Please find attached the latest report card for {{ $student->first_name }} {{ $student->last_name }}.</p>

    <p>Best regards,<br>{{ config('app.name') }}</p>
</body>
</html>
