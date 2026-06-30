<?php
$hideFilter = true;
include "includes/config.php";
include "includes/header.php";
?>
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-8 col-md-10">

      <!-- Back to Home -->
      <div class="mb-4 text-start">
        <a href="index.php" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm fw-medium">
          <i class="bi bi-arrow-left me-2"></i> Back to Home
        </a>
      </div>

      <div class="card shadow-lg border-0 rounded-4 mb-4">
        <div class="card-body p-4 p-md-5">
          <h2 class="fw-bolder mb-3 text-dark">Contact Us</h2>
          <p class="text-secondary lh-lg mb-5" style="font-size: 1.05rem;">
            Whether you are looking to buy, sell, or rent – we are here to provide you with the best real estate experience in Ahmedabad. Reach out to us using the details below.
          </p>

          <div class="row gy-4">
            <div class="col-md-6">
              <div class="d-flex align-items-start">
                <div class="bg-primary text-white rounded-circle d-flex justify-content-center align-items-center me-3 shadow-sm" style="width: 45px; height: 45px;">
                  <i class="bi bi-geo-alt-fill fs-5"></i>
                </div>
                <div>
                  <h6 class="fw-bold mb-1">Office Address</h6>
                  <p class="mb-0 text-secondary small lh-sm">
                    B - 802, Gopal Palace,<br>
                    Opp. Choice Restaurant,<br>
                    Near Jhansi ki Rani Statue,<br>
                    Nehrunagar, Satellite,<br>
                    Ahmedabad, Gujarat.
                  </p>
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="d-flex align-items-start mb-4">
                <div class="bg-success text-white rounded-circle d-flex justify-content-center align-items-center me-3 shadow-sm" style="width: 45px; height: 45px;">
                  <i class="bi bi-telephone-fill fs-5"></i>
                </div>
                <div>
                  <h6 class="fw-bold mb-1">Phone Number</h6>
                  <p class="mb-0 text-secondary small">
                    <a href="tel:+918758257487" class="text-decoration-none text-dark fw-medium">
                      +91 87582 57487
                    </a>
                  </p>
                </div>
              </div>

              <div class="d-flex align-items-start mb-4">
                <div class="bg-danger text-white rounded-circle d-flex justify-content-center align-items-center me-3 shadow-sm" style="width: 45px; height: 45px;">
                  <i class="bi bi-envelope-fill fs-5"></i>
                </div>
                <div>
                  <h6 class="fw-bold mb-1">Email Address</h6>
                  <p class="mb-0 text-secondary small">
                    <a href="mailto:kargilproperty1@gmail.com" class="text-decoration-none text-dark fw-medium">
                      kargilproperty1@gmail.com
                    </a>
                  </p>
                </div>
              </div>
              
              <div class="d-flex align-items-start">
                <div class="bg-warning text-dark rounded-circle d-flex justify-content-center align-items-center me-3 shadow-sm" style="width: 45px; height: 45px;">
                  <i class="bi bi-clock-fill fs-5"></i>
                </div>
                <div>
                  <h6 class="fw-bold mb-1">Working Hours</h6>
                  <p class="mb-0 text-secondary small lh-sm">
                    Monday to Saturday<br>
                    10:00 AM – 7:00 PM<br>
                    Sunday: Closed
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Location (static map) -->
      <div class="card shadow-lg border-0 rounded-4">
        <div class="card-body p-4 p-md-5">
          <h4 class="fw-bold mb-3">Our Location</h4>
          <p class="small text-secondary mb-4">
            Visit our office located in the heart of Ahmedabad. Use the map below to find directions.
          </p>

          <div class="ratio ratio-16x9 rounded-4 overflow-hidden shadow-sm">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3672.017238051254!2d72.53572367509179!3d23.02313927917334!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x395e84dca1279fc7%3A0x6519207152c4c258!2sGopal%20Palace%20Block-B%2C%20Gopal%20Palace%2C%20Acharya%20Narendradev%20Nagar%2C%20Ambawadi%2C%20Ahmedabad%2C%20Gujarat%20380015!5e0!3m2!1sen!2sin!4v1766134893754!5m2!1sen!2sin" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
          </div>

        </div>
      </div>

    </div>
  </div>
</div>

<?php include "includes/footer.php"; ?>

