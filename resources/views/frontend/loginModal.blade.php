<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Please Login/Register</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">

          <!-- Step 1: Mobile + OTP -->
          <div id="otp-section">
            
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Select Municipality</label>
                <select class="form-select" ng-model="selectedMunicipality">
                    <option value="">Choose Municipality</option>
                    <option ng-repeat="m in municipalities" value="[[ m.user_id ]]">
                      [[ m.name ]]
                    </option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Mobile Number</label>
                <input type="text" id="mobile" ng-model="mobile" class="form-control" placeholder="Enter Mobile Number">
              </div>
            </div>

            <button class="btn btn-primary w-100 mt-3" ng-click="sendOTP()">Send OTP</button>

            <div id="otp-verify" ng-show="otpSent">
              <label class="form-label mt-3">Enter OTP</label>
              <input type="text" id="otp" ng-model="otp" class="form-control mb-2" placeholder="Enter OTP">
              <button class="btn btn-success w-100" ng-click="verifyOTP()">Verify OTP</button>
            </div>
            
            <div class="alert alert-success mt-3" ng-show="successMsg">[[successMsg]]</div>
            <div class="alert alert-danger mt-3" ng-show="errorMsg">[[errorMsg]]</div>

          </div>

        </div>
      </div>
    </div>
  </div>