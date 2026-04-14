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
    <div class="receipt-title">Booking Receipt / Bill Details</div>

    <!-- Booking Summary -->
    <h3 class="section-title">Booking Summary</h3>
    <table>
      <tr>
        <th>Booking Code</th>
        <td class="fw-bold">{{ $uid }}</td>
      </tr>
      <tr>
        <th>Service</th>
        <td>{{ $service_master_title }}</td>
      </tr>
      <tr>
        <th>Order Date</th>
        <td>{{ $formatted_created_at }}</td>
      </tr>
    </table>

    <!-- Service Info -->
    <h3 class="section-title">Service & User Details</h3>
    <div class="profile">
      <div>
        <strong>{{ $user_name }}</strong><br>
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
        <th>Service</th>
        <td>{{ $service_master_title }}</td>
      </tr>
      <!-- {{ $service_master_id }} -->
     @if(!empty($service_master_id) && $service_master_id==1)
      <tr>
        <th>Service Details</th>
        <td>{{ $service_item_name }} (Type: {{ $service_item_type_name }})</td>
      </tr>
      <tr>
        <th>Price</th>
        <td class="text-success fw-bold">₹ {{ number_format($service_price_per_day, 2) }} / day</td>
      </tr>
      <tr>
        <th>Details</th>
        <td>
            {{ $service_item_details }}
            @if(!empty($service_item_capacity))
                - Capacity: {{ $service_item_capacity }} Ltr.
            @endif
        </td>
      </tr> 
      @endif
      
      @if (!empty($service_master_id) && $service_master_id==2 ||$service_master_id==3 || $service_master_id==5 || $service_master_id==12 )
        <!-- <tr>
        <th>Service Details</th>
        <td>{{ $service_item_name }} (Type: {{ $service_item_type_name }})</td>
      </tr> -->
      <tr>
        <th>Price</th>
        <td class="text-success fw-bold">
          @if($service_master_id==2)
          ₹ {{ number_format($price, 2) }} / Trip
          @endif
           @if($service_master_id!=2)
            ₹ {{ number_format($price, 2) }} / Day
          @endif

        </td>
      </tr>
      <tr>
       @if($type_name || $capacity || $quantity)
        <th>Details</th>
      @endif
        <td>
                @if(!empty($type_name))
                 Type: {{ $type_name }}
            @endif
            @if(!empty($capacity) && $capacity!="NA")
                 -Capacity: {{ $capacity }} Ltr.
            @endif
              @if(!empty($quantity))
                - Quantity: {{ $quantity }}.
            @endif
        </td>
      </tr> 
      @endif
      <tr>
        <th>Booking Period</th>
        <td>{{ $formatted_from_date }} → {{ $formatted_to_date }}</td>
      </tr>
      <tr>
        <th>Current Status</th>
        <td class="fw-bold text-{{ $css_class }}">{{ $booking_status_name }}</td>
      </tr>
    </table>

    <!-- Payment Details -->
    <h3 class="section-title">Payment Details</h3>
    @if(!empty($payment_transaction_id))
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Transaction ID</th>
          <th>Method</th>
          <th>Amount</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>1</td>
          <td>{{ $payment_transaction_id }}</td>
          <td>{{ $payment_method_short_name }}</td>
          <td>₹ {{ number_format($payment_amount, 2) }}</td>
          <td>{{ $payments_status_name }}</td>
        </tr>
      </tbody>
    </table>
    @else
      <p class="text-muted">No transaction records found.</p>
    @endif

    <!-- Booking History -->
    @if(!empty($trailDetails))
    <h3 class="section-title">Booking History</h3>
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Date - Time</th>
          <th>Action By</th>
          <th>From</th>
          <th>To</th>
          <th>Remarks</th>
        </tr>
      </thead>
      <tbody>
        @foreach($trailDetails as $index => $trail)
        <tr>
          <td class="text-center">{{ count($trailDetails) - $index }}</td>
          <td class="text-center">{{ $trail['formatted_created_at'] }}</td>
          <td>{{ $trail['action_user_name'] }} <span class="text-muted">({{ $trail['action_user_role'] }})</span></td>
          <td class="text-center">{{ $trail['from_state_name'] }}</td>
          <td class="text-center">{{ $trail['to_state_name'] }}</td>
          <td>{{ $trail['remarks'] }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
    @endif

    <!-- Footer -->
    <div class="footer">
      Generated automatically by {{ $municipality_name ?? 'PUROSATHI' }} • {{ now()->format('d M Y, h:i A') }}
    </div>

  </div>
</body>
</html>
