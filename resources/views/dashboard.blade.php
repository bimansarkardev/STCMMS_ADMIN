<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ config('constants.APP_NAME') }} | Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('common.header_links')
  </head>
  <!--end::Head-->
  <!--begin::Body-->
  <body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
      <!--begin::Header-->
      @include('common.header')
      <!--end::Header-->
      <!--begin::Sidebar-->
      @include('common.sidebar')
      <!--end::Sidebar-->
      <!--begin::App Main-->
      <main class="app-main">
        <!--begin::App Content Header-->
        <div class="app-content-header">

          <div class="container-fluid">

            <div class="row">
              <div class="col-sm-6">
                <h3 class="mb-0">Dashboard</h3>
              </div>
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                  <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                  <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                </ol>
              </div>
            </div>

          </div>

        </div>
        <!--end::App Content Header-->
        <!--begin::App Content-->
        <div class="app-content">
          <!--begin::Container-->
          <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">

              @if(session('error'))
              <div class="col-12">
                  <div class="alert alert-danger alert-dismissible fade show" role="alert">
                      <strong>Error!</strong> {{ session('error') }}
                      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>
              </div>
              @endif

              <!--begin::greeting-box-->
              <div class="col-12">                
                <div class="small-box text-bg-info">
                  <div class="inner">
                    <h5 id="current-time" style="font-weight: bold;"></h5>
                    <p id="greeting-box"></p>
                  </div>
                  <div class="small-box-icon d-flex align-items-center justify-content-center fs-1">
                    <i class="bi bi-palette fs-1"></i>
                  </div>
                  <a href="{{ route('admin.dashboard') }}" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                    More info <i class="bi bi-link-45deg"></i>
                  </a>
                </div>                
              </div>
              <!--end::greeting-box-->

              @if(Session::get('user')->user_type_id == 2)
                @include('dashboard_municipality_admin')
              @endif

              @if(Session::get('user')->user_type_id == 3)
                @include('dashboard_municipality_subadmin')
              @endif

            </div>
            <!--end::Row-->
            <!--begin::Row-->

            <!-- /.row (main row) -->
          </div>
          <!--end::Container-->
        </div>
        <!--end::App Content-->
      </main>
      <!--end::App Main-->
      <!--begin::Footer-->
      @include('common.footer')
      <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->
    <script>
      function updateGreetingAndTime() {
        const now = new Date();
        const hour = now.getHours();
        let greeting = "";
        let icon = "";
        let user = "{{ session('user')->name }}";
        let APP_NAME = "{{ config('constants.APP_NAME') }}";

        if (hour >= 5 && hour < 12) {
          greeting = "Good Morning";
          icon = "bi-sun-fill";
        } else if (hour >= 12 && hour < 17) {
          greeting = "Good Afternoon";
          icon = "bi-cloud-sun-fill";
        } else if (hour >= 17 && hour < 21) {
          greeting = "Good Evening";
          icon = "bi-moon-fill";
        } else {
          greeting = "Good Night";
          icon = "bi-moon-stars-fill";
        }

        const timeOptions = { hour: '2-digit', minute: '2-digit', second: '2-digit' };
        const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };

        const currentTime = now.toLocaleTimeString([], timeOptions);
        const currentDate = now.toLocaleDateString([], dateOptions);

        document.getElementById("greeting-box").innerHTML =
          `<i class="bi ${icon} me-2"></i> Hello, ${user}! ${greeting}, Welcome to ${APP_NAME}`;

        document.getElementById("current-time").innerText = `${currentDate} | ${currentTime}`;
      }

      updateGreetingAndTime();
      setInterval(updateGreetingAndTime, 1000);
    </script>


    <!--begin::Script-->
    @include('common.script')
    <!--end::Script-->

    <!-- Include Firebase SDK via CDN (UMD versions, not module-based) -->
    <script src="https://www.gstatic.com/firebasejs/9.6.1/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.6.1/firebase-messaging-compat.js"></script>

    <script>
      const firebaseConfig = {
        apiKey: "AIzaSyAuXK5T3eWSoUsH2r7xtZ7muHYuQ6ylWXI",
        authDomain: "grievance-2eddf.firebaseapp.com",
        projectId: "grievance-2eddf",
        storageBucket: "grievance-2eddf.firebasestorage.app",
        messagingSenderId: "331503211235",
        appId: "1:331503211235:web:84561e2f9b10bfb393ebd6",
        measurementId: "G-NN3QKSN2QP"
      };

      firebase.initializeApp(firebaseConfig);

      const messaging = firebase.messaging();

      function getDeviceType() {
        const ua = navigator.userAgent;
        if (/tablet|ipad|playbook|silk/i.test(ua)) {
          return "Tablet";
        }
        if (/Mobile|Android|iPhone|iPod|IEMobile|Opera Mini/i.test(ua)) {
          return "Mobile";
        }
        return "Desktop";
      }

      if ('serviceWorker' in navigator) {
        navigator.serviceWorker
          .register("{{ url('firebase-messaging-sw.js') }}")
          .then(function (registration) {
            console.log('Service Worker Registered:', registration);

            messaging.getToken({
              vapidKey: 'BJfiPa_oSycFK_UixjO4NdTdcG6nNov6ppN2lYIh7zIUNgKcUtq0TmlIb06zIJVOrFVd-z-m0uezOsMFdVjoZK8',
              serviceWorkerRegistration: registration, // This is the new way
            }).then((currentToken) => {
              if (currentToken) {
                console.log("FCM Token:", currentToken);
                const deviceType = getDeviceType();
                var csrftoken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                //save the token
                fetch('{{route("admin.saveToken")}}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrftoken
                    },
                    body: JSON.stringify({ token: currentToken , deviceType: deviceType }),
                });
              } else {
                console.warn("No registration token available. Request permission to generate one.");
              }
            }).catch((err) => {
              console.error("An error occurred while retrieving token. ", err);
            });

          })
          .catch(function (err) {
            console.error('Service Worker registration failed:', err);
          });
      }
    </script>




  </body>
  <!--end::Body-->
</html>
