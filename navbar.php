<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">AccessForm</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-lg-center">
                
                <li class="nav-item dropdown me-lg-2">
                    <button class="btn btn-outline-light nav-link dropdown-toggle d-flex align-items-center gap-2 px-3 text-white border-0" 
                            type="button" 
                            id="accessibilityDropdown" 
                            data-bs-toggle="dropdown" 
                            aria-expanded="false"
                            aria-label="Accessibility Settings">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 20px; height: 20px;" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.05 4.575a1.575 1.575 0 1 0-3.15 0v3m3.15-3v-1.5a1.5 1.5 0 0 1 3 0v1.5m-3 0h3m-6.3 10.5a4.5 4.5 0 1 1 9 0v1.5a1.5 1.5 0 0 1-3 0v-1.5a1.5 1.5 0 0 0-3 0v1.5a1.5 1.5 0 0 1-3 0v-1.5Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Z" />
                        </svg>
                        <span>Accessibility</span>
                    </button>
                    
                    <div class="dropdown-menu dropdown-menu-end p-4 shadow-lg border-0" aria-labelledby="accessibilityDropdown" style="width: 320px; border-radius: 16px;">
                        
                        <h2 class="h6 text-uppercase fw-bold text-muted mb-3" style="font-size: 0.75rem; letter-spacing: 0.05em;">Visual Theme</h2>
                        
                        <div class="d-flex flex-column gap-2 mb-3">
                            <button onclick="setTheme('default')" id="btn-default" class="btn-theme-option w-full text-start p-2 rounded border text-dark bg-light" type="button" style="font-size: 0.9rem;">
                                <strong>Default</strong><br><small class="text-muted">Calm blue theme</small>
                            </button>

                            <button onclick="setTheme('high-contrast')" id="btn-high-contrast" class="btn-theme-option w-full text-start p-2 rounded border text-dark bg-white" type="button" style="font-size: 0.9rem;">
                                <strong>High contrast</strong><br><small class="text-muted">Maximum contrast, yellow focus</small>
                            </button>

                            <button onclick="setTheme('dyslexia')" id="btn-dyslexia" class="btn-theme-option w-full text-start p-2 rounded border text-dark bg-white" type="button" style="font-size: 0.9rem;">
                                <strong>Dyslexia-friendly</strong><br><small class="text-muted">OpenDyslexic font, cream bg</small>
                            </button>
                        </div>

                        <hr class="dropdown-divider my-3">

                        <div class="d-flex align-items-center justify-content-between pt-1 mb-2">
                            <div>
                                <strong class="text-dark" style="font-size: 0.95rem;">Voice Reader</strong><br>
                                <small class="text-muted" style="font-size: 0.8rem;">Reads text on hover</small>
                            </div>
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input" type="checkbox" id="screenReaderToggle" onchange="toggleScreenReader(this.checked)" style="width: 2.5em; height: 1.25em; cursor: pointer;">
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-between pt-1">
                            <div>
                                <strong class="text-dark" style="font-size: 0.95rem;">Larger text</strong><br>
                                <small class="text-muted" style="font-size: 0.8rem;">Increases base font size</small>
                            </div>
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input" type="checkbox" id="textToggle" onchange="toggleLargeText(this.checked)" style="width: 2.5em; height: 1.25em; cursor: pointer;">
                            </div>
                        </div>

                    </div>
                </li>

                <?php
                if (isset($_SESSION["id"]) && !empty($_SESSION["id"])) {
                    echo '<li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>';
                    echo '<li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>';
                } else {
                    echo '<li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>';
                    echo '<li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>';
                    echo '<li class="nav-item"><a class="nav-link" href="./admin/admin_login.php">Admin Login</a></li>';
                }
                ?>
            </ul>
        </div>
    </div>
</nav>

<script src="js/accessibility.js"></script>