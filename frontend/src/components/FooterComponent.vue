<template>
  <footer class="footer mt-auto py-5 bg-dark text-white position-relative overflow-hidden">
    <div class="footer-glow"></div>
    <div class="container position-relative">
      <div class="row">
        <div class="col-lg-4 mb-4 mb-lg-0">
          <div class="footer-brand">
            <h5 class="text-gold mb-3 fw-bold">GKI Menteng</h5>
            <p class="text-light mb-3">
              Gereja Kristen Indonesia<br />Jl. Teuku Umar No. 2, Menteng<br />Jakarta Pusat 10350
            </p>
            <div class="footer-verse opacity-50 fst-italic">
              "Tuhan adalah gembalaku, takkan kekurangan aku" - Mazmur 23:1
            </div>
          </div>
        </div>
        <div class="col-lg-4 mb-4 mb-lg-0">
          <h5 class="text-gold mb-3 fw-bold">Quick Links</h5>
          <ul class="list-unstyled footer-links">
            <li class="mb-2">
              <router-link to="/" class="text-light text-decoration-none footer-link"
                ><i class="bi bi-speedometer2 me-2"></i>Dashboard</router-link
              >
            </li>
            <li class="mb-2">
              <router-link to="/calendar" class="text-light text-decoration-none footer-link"
                ><i class="bi bi-calendar3 me-2"></i>Calendar</router-link
              >
            </li>
            <li class="mb-2">
              <router-link to="/volunteers" class="text-light text-decoration-none footer-link"
                ><i class="bi bi-people-fill me-2"></i>Volunteers</router-link
              >
            </li>
            <li class="mb-2">
              <router-link to="/about" class="text-light text-decoration-none footer-link"
                ><i class="bi bi-info-circle-fill me-2"></i>About</router-link
              >
            </li>
          </ul>
        </div>
        <div class="col-lg-4">
          <h5 class="text-gold mb-3 fw-bold">Connect With Us</h5>
          <div class="social-links mb-3">
            <a href="#" class="text-white me-3 social-link"><i class="bi bi-facebook"></i></a>
            <a href="#" class="text-white me-3 social-link"><i class="bi bi-instagram"></i></a>
            <a href="#" class="text-white me-3 social-link"><i class="bi bi-youtube"></i></a>
            <a href="#" class="text-white social-link"><i class="bi bi-whatsapp"></i></a>
          </div>
          <div class="contact-info small text-muted">
            <div class="mb-1"><i class="bi bi-envelope me-2"></i>info@gkimenteng.org</div>
            <div><i class="bi bi-telephone me-2"></i>+62 21 1234 5678</div>
          </div>
        </div>
      </div>
      <hr class="my-4 border-gold" />
      <div class="text-center text-light opacity-75">
        <small>&copy; 2024 GKI Menteng. All rights reserved.</small>
      </div>
    </div>
  </footer>
</template>
