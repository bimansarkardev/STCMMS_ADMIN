<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="auditoriumModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Auditorium Hall Booking</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">

          <!-- Step 1: Mobile + OTP -->
          <div id="otp-section">
            <div class="mb-3">
              <label class="form-label">Mobile Number</label>
              <input type="text" id="mobile" class="form-control" placeholder="Enter Mobile Number">
            </div>
            <button class="btn btn-primary w-100 mb-2" onclick="sendOTP()">Send OTP</button>

            <div id="otp-verify" style="display:none;">
              <label class="form-label">Enter OTP</label>
              <input type="text" id="otp" class="form-control mb-2" placeholder="Enter OTP">
              <button class="btn btn-success w-100" onclick="verifyOTP()">Verify OTP</button>
            </div>
          </div>

          <!-- Step 2: Booking Availability -->
          <div id="booking-check" style="display:none;">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">From Date</label>
                <input type="date" class="form-control">
              </div>
              <div class="col-md-6">
                <label class="form-label">To Date</label>
                <input type="date" class="form-control">
              </div>
              <div class="col-md-6">
                <label class="form-label">Select Auditorium</label>
                <select class="form-select">
                  <option>Main Auditorium</option>
                  <option>Community Hall</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Select Type</label>
                <select class="form-select">
                  <option>AC</option>
                  <option>Non AC</option>
                </select>
              </div>
            </div>
            <button class="btn btn-primary w-100 mt-3" onclick="showBookingForm()">Check Availability</button>
          </div>

          <!-- Step 3: Booking Form -->
          <div id="booking-form" style="display:none;">
            <form>
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label">From Date</label>
                  <input type="text" class="form-control" value="2025-10-25" readonly>
                </div>
                <div class="col-md-6">
                  <label class="form-label">To Date</label>
                  <input type="text" class="form-control" value="2025-10-26" readonly>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Ward No.</label>
                  <select class="form-select">
                    <option>1</option><option>2</option><option>3</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Name</label>
                  <input type="text" class="form-control" placeholder="Enter Your Name">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Mobile No.</label>
                  <input type="text" class="form-control" id="verifiedMobile" readonly>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Alternate Mobile No. (Optional)</label>
                  <input type="text" class="form-control">
                </div>
                <div class="col-md-6">
                  <label class="form-label">E-mail</label>
                  <input type="email" class="form-control">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Address</label>
                  <input type="text" class="form-control">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Auditorium</label>
                  <input type="text" class="form-control" value="Main Auditorium" readonly>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Auditorium Type</label>
                  <input type="text" class="form-control" value="AC" readonly>
                </div>
                <div class="col-12">
                  <label class="form-label">Purpose of Booking</label>
                  <textarea class="form-control" rows="2"></textarea>
                </div>
                <div class="col-12">
                  <div class="form-check mt-2">
                    <input class="form-check-input" type="checkbox" required>
                    <label class="form-check-label">
                      By clicking, I consent that I have read, understood, and agree to Municipality Privacy Policy
                    </label>
                  </div>
                </div>
              </div>
              <button type="submit" class="btn btn-success w-100 mt-3">Register & Pay</button>
            </form>
          </div>

        </div>
      </div>
    </div>
  </div>