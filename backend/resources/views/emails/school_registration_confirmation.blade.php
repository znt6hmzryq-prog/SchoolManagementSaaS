<!DOCTYPE html>
<html>
<head>
    <title>School Registration Confirmation</title>
</head>
<body>
    <h1>Welcome to SchoolManagementSaaS!</h1>
    <p>Dear {{ $school->name }},</p>
    <p>Your school has been successfully registered.</p>
    <p>Admin: {{ $school->users->first()->name }}</p>
    <p>Thank you for choosing us.</p>
</body>
</html>