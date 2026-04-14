<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Application Status</title>
</head>
<body style="margin:0; padding:0; background-color:#f4f6f9; font-family: Arial, sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" bgcolor="#f4f6f9">
<tr>
<td align="center">

<table width="600" cellpadding="0" cellspacing="0" bgcolor="#ffffff" 
       style="margin:30px 0; border-radius:8px; overflow:hidden; box-shadow:0 4px 10px rgba(0,0,0,0.08);">

    <!-- Header -->
    <tr>
        <td align="center" 
            style="padding:20px; background-color: {{ $details['message'] == 'Your application has been approved successfully.' ? '#28a745' : '#dc3545' }}; color:#ffffff;">
            <h2 style="margin:0;">
                {{ $details['message'] == 'Your application has been approved successfully.' ? 'Application Approved' : 'Application Rejected' }}
            </h2>
        </td>
    </tr>

    <!-- Body -->
    <tr>
        <td style="padding:30px; color:#333333; font-size:15px; line-height:1.6;">

            <p>Hello <strong>{{ $details['name'] }}</strong>,</p>

            <p>{{ $details['message'] }}</p>

            @if(isset($details['password']))
            <!-- Login Details Box -->
            <div style="background:#f8f9fa; padding:20px; border-radius:6px; margin:25px 0; border:1px solid #e3e6ea;">
                
                <h3 style="margin-top:0; color:#333;">Login Credentials</h3>

                <p style="margin:5px 0;">
                    <strong>Email:</strong> {{ $details['email'] }}
                </p>

                <p style="margin:5px 0;">
                    <strong>Temporary Password:</strong> {{ $details['password'] }}
                </p>

                <p style="font-size:13px; color:#888; margin-top:10px;">
                    ⚠ Please change your password after first login.
                </p>
            </div>
            @endif

            <p>
                If you have any questions, please contact our support team.
            </p>

            <p style="margin-top:25px;">
                Regards,<br>
                <strong>{{ config('app.name') }}</strong>
            </p>

        </td>
    </tr>

    <!-- Footer -->
    <tr>
        <td align="center" style="background:#f1f1f1; padding:15px; font-size:12px; color:#777;">
            © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </td>
    </tr>

</table>

</td>
</tr>
</table>

</body>
</html>