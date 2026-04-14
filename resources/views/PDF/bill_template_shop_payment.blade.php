<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>{{ $title ?? 'Booking Receipt' }}</title>
  <style>
    body {
      font-family: DejaVu Sans, sans-serif;
      background: #f8f9fa;
      margin: 20px;
      color: #212529;
    }
    .container {
      background: #fff;
      border-radius: 10px;
      padding: 25px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    /* Header */
    .municipality-header {
      text-align: center;
      border-bottom: 3px solid #0d6efd;
      padding-bottom: 10px;
      margin-bottom: 10px;
    }
    .municipality-header h1 {
      margin: 0;
      font-size: 22px;
      color: #0d6efd;
      text-transform: uppercase;
      font-weight: bold;
    }
    .municipality-header p {
      margin: 4px 0;
      font-size: 13px;
      color: #6c757d;
    }

    .receipt-title {
      text-align: center;
      background: linear-gradient(90deg, #0dcaf0, #17a2b8);
      color: #fff;
      font-weight: bold;
      font-size: 18px;
      padding: 8px 0;
      border-radius: 8px;
      margin-bottom: 15px;
    }

    /* Sections */
    .section-title {
      font-size: 16px;
      font-weight: bold;
      color: #0d6efd;
      margin-top: 25px;
      margin-bottom: 8px;
      border-bottom: 2px solid #0d6efd;
      display: inline-block;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 6px;
    }
    th, td {
      border: 1px solid #dee2e6;
      padding: 8px 10px;
      font-size: 13px;
      vertical-align: top;
    }
    th {
      background: #f1f3f5;
      text-align: left;
    }

    .text-center { text-align: center; }
    .text-right { text-align: right; }
    .text-success { color: #198754; }
    .text-danger { color: #dc3545; }
    .text-warning { color: #ffc107; }
    .text-muted { color: #6c757d; }
    .fw-bold { font-weight: bold; }

    .profile {
      display: flex;
      align-items: center;
      gap: 12px;
      margin: 10px 0;
    }
    .profile img {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      border: 2px solid #17a2b8;
      object-fit: cover;
    }

    .footer {
      text-align: center;
      font-size: 12px;
      color: #6c757d;
      border-top: 1px solid #dee2e6;
      margin-top: 30px;
      padding-top: 8px;
    }
  </style>
</head>
<body>
  <div class="container">

    <!-- Municipality Header -->
    <div class="municipality-header">
      <h1>{{ $municipality_name ?? 'Cooch Behar Municipality' }}</h1>
      <p>{{ $municipality_address ?? 'Main Road, Cooch Behar - 736101, West Bengal' }}</p>
      <p>📞 {{ $municipality_phone ?? '+91 3561 230456' }} | ✉️ {{ $municipality_email ?? 'info@coochbeharmunicipality.in' }}</p>
    </div>

    <!-- Receipt Title -->
    <div class="receipt-title">Shop Rent Payment Receipt / Bill Details</div>

    <!-- Booking Summary -->
    <h3 class="section-title">Rent Payment Summary</h3>
    <table>
      <tr>
        <th>Payment Code</th>
        <td class="fw-bold">{{ $uid }}</td>
      </tr>
      <tr>
        <th>Service</th>
        <td>{{ $service_master_title }}</td>
      </tr>
      <tr>
        <th>Payment Date</th>
        <td>{{ $formatted_created_at }}</td>
      </tr>
    </table>

    <!-- Service Info -->
    <h3 class="section-title">Shop & Tenant Details</h3>
    <div class="profile">
      <div>
        <strong>{{ $tenant_name }}</strong><br>
       <span class="text-muted">
          {{ $mobile }}
          @if(!empty($email))
              | {{ $email }}
          @endif
          | Ward {{ $user_ward_no }}
      </span>
      </div>
    </div>

    <table>
      <tr>
        <th>Shop</th>
        <td>{{ $shop_name }}</td>
      </tr>
      <tr>
        <th>Shop Details</th>
        <td>{{ $shop_ward }}, {{ $shop_address }}</td>
      </tr>
      <tr>
        <th>Rent</th>
        <td class="text-success fw-bold">₹ {{ number_format($monthly_rent, 2) }} / month</td>
      </tr>
      <tr>
        <th>Due Day</th>
        <td>
            Every month's day {{ $due_day }} day is due day
        </td>
      </tr>
      <tr>
        <th>Paid Period</th>
        <td>{{ $from_month_year }} → {{ $to_month_year }}</td>
      </tr>
      <tr>
        <th>Total Month</th>
        <td>{{ $total_month }}</td>
      </tr>
      <tr>
        <th>Rent Paid</th>
        <td>₹ {{ number_format($amount, 2) }}</td>
      </tr>
    </table>

    <!-- Footer -->
    <div class="footer">
      Generated automatically by {{ $municipality_name ?? 'PUROSATHI' }} • {{ now()->format('d M Y, h:i A') }}
    </div>

  </div>
</body>
</html>
