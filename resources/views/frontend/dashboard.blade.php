<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Online Municipal Service Booking System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .hero {
      background: linear-gradient(to right, #0d6efd, #0078d7);
      color: white;
      padding: 80px 0;
      text-align: center;
    }
    .service-btn {
      min-width: 250px;
      margin: 10px;
      font-size: 18px;
    }
    .modal-header {
      background-color: #0d6efd;
      color: white;
    }
  </style>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="{{ asset('js/custom/alert.js') }}"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.9/angular.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.9/angular-sanitize.js"></script>
  <script type="text/javascript">

    var app = angular.module('myApp', ['ngSanitize'], function($interpolateProvider) {
          $interpolateProvider.startSymbol('[[');
          $interpolateProvider.endSymbol(']]');
      });

    app.controller('Ctrl', function ($scope, $http) {
      
      $scope.isLoading = false;     

      $scope.checkLogin = function () {
          $("#loginModal").modal('show');
      }

      $scope.checkLogin();

      // Optional: Initialize municipalities from backend (if needed)
      $scope.municipalities = @json($municipalities);
      $scope.otpSent = false;
      $scope.successMsg = '';
      $scope.errorMsg = '';

      // 🔹 Function to generate OTP
      $scope.sendOTP = function() {
        if (!$scope.mobile || !$scope.selectedMunicipality) {
          $scope.errorMsg = "Please select municipality and enter mobile number.";
          return;
        }

        //alert($scope.selectedMunicipality);

        $scope.errorMsg = '';
        $http.post('/api/login', {
          municipality: $scope.selectedMunicipality,
          mobile: $scope.mobile
        }).then(function(response) {
          if (response.data.success) {
            $scope.otpSent = true;
            $scope.successMsg = "OTP sent successfully!";
          } else {
            $scope.errorMsg = response.data.message || "Failed to send OTP.";
          }
        }, function(error) {
          $scope.errorMsg = "Error: Unable to send OTP.";
        });
      };

      // 🔹 Function to verify OTP
      $scope.verifyOTP = function() {
        if (!$scope.otp) {
          $scope.errorMsg = "Please enter OTP.";
          return;
        }

        $scope.errorMsg = '';
        $http.post('/api/verify-otp', {
          mobile: $scope.mobile,
          otp: $scope.otp
        }).then(function(response) {
          if (response.data.success) {
            $scope.successMsg = "OTP verified successfully!";
          } else {
            $scope.errorMsg = response.data.message || "Invalid OTP.";
          }
        }, function(error) {
          $scope.errorMsg = "Error: Unable to verify OTP.";
        });
      };
      
    });
  </script>
</head>
<body ng-app="myApp" ng-controller="Ctrl" ng-cloak>

  <!-- Header -->
  <div class="hero">
    <div class="container">
      <h1 class="display-5 fw-bold">Online Municipal Service Booking Management System</h1>
      <p class="lead">Book your municipal services online – quick, easy, and transparent.</p>
    </div>
  </div>

  <!-- Services -->
  <div class="container text-center mt-5">
    <h2 class="mb-4">Select a Service</h2>
    <div class="d-flex flex-wrap justify-content-center">
      <button class="btn btn-outline-primary service-btn">Auditorium Hall Booking</button>
      <button class="btn btn-outline-info service-btn">Cess Pool Booking</button>
      <button class="btn btn-outline-warning service-btn">Drinking Water Tank Booking</button>
      <button class="btn btn-outline-success service-btn">VAT Booking</button>
      <button class="btn btn-outline-danger service-btn">Shop Rent Payment</button>
    </div>
  </div>

  <!-- Cancellation Clause -->
  <div class="container mt-5 mb-5">
    <div class="alert alert-warning">
      <h5>📢 Cancellation Clause for Emergency Government Use</h5>
      <p>Should an unforeseen government-organized event require the use of the Municipality Auditorium Hall, 
      the Municipality shall exercise its right to cancel any existing bookings. A full refund (100%) of the 
      booking fee will be issued to the affected party. This clause is enacted in alignment with public interest 
      and administrative priority.</p>
    </div>
  </div>

  <!-- Login Modal -->
  @include('frontend.loginModal');

  <script>
    function sendOTP() {
      const mobile = document.getElementById('mobile').value;
      if (mobile.length === 10) {
        alert('OTP sent to ' + mobile);
        document.getElementById('otp-verify').style.display = 'block';
      } else {
        alert('Please enter a valid 10-digit mobile number');
      }
    }
    function verifyOTP() {
      alert('OTP verified successfully!');
      document.getElementById('otp-section').style.display = 'none';
      document.getElementById('booking-check').style.display = 'block';
      document.getElementById('verifiedMobile').value = document.getElementById('mobile').value;
    }
    function showBookingForm() {
      alert('Auditorium available!');
      document.getElementById('booking-check').style.display = 'none';
      document.getElementById('booking-form').style.display = 'block';
    }
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
