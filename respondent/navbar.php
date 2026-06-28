<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="respondent_dashboard.php">
            <strong>AccessForm</strong> - Respondent
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-lg-center">
                
                <!-- ==================== ACCESSIBILITY DROPDOWN ==================== -->
                <li class="nav-item dropdown me-lg-3">
                    <button 
                        class="btn btn-outline-light nav-link dropdown-toggle d-flex align-items-center gap-2 px-3" 
                        type="button" 
                        id="accessibilityDropdown" 
                        data-bs-toggle="dropdown" 
                        aria-expanded="false"
                        aria-label="Accessibility settings">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" width="22" height="22" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.05 4.575a1.575 1.575 0 1 0-3.15 0v3m3.15-3v-1.5a1.5 1.5 0 0 1 3 0v1.5m-3 0h3m-6.3 10.5a4.5 4.5 0 1 1 9 0v1.5a1.5 1.5 0 0 1-3 0v-1.5a1.5 1.5 0 0 0-3 0v1.5a1.5 1.5 0 0 1-3 0v-1.5Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Z" />
                        </svg>
                        <span>Accessibility</span>
                    </button>
                    
                    <div class="dropdown-menu dropdown-menu-end p-4 shadow-lg border-0" 
                         aria-labelledby="accessibilityDropdown" 
                         style="width: 340px; border-radius: 16px;">
                        
                        <h2 class="h6 text-uppercase fw-bold text-muted mb-3">Visual Theme</h2>
                        
                        <div class="d-flex flex-column gap-2 mb-4">
                            <button onclick="setTheme('default')" id="btn-default" 
                                    class="btn-theme-option w-100 text-start p-2 rounded border active" type="button">
                                <strong>Default</strong><br>
                                <small class="text-muted">Calm blue theme</small>
                            </button>

                            <button onclick="setTheme('high-contrast')" id="btn-high-contrast" 
                                    class="btn-theme-option w-100 text-start p-2 rounded border" type="button">
                                <strong>High Contrast</strong><br>
                                <small class="text-muted">Maximum contrast</small>
                            </button>

                            <button onclick="setTheme('dyslexia')" id="btn-dyslexia" 
                                    class="btn-theme-option w-100 text-start p-2 rounded border" type="button">
                                <strong>Dyslexia Friendly</strong><br>
                                <small class="text-muted">OpenDyslexic font</small>
                            </button>
                        </div>

                        <hr class="dropdown-divider my-3">

                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <label for="screenReaderToggle" class="mb-0 cursor-pointer">
                                <strong>Voice Reader</strong><br>
                                <small class="text-muted">Read text on hover</small>
                            </label>
                            <input class="form-check-input" type="checkbox" id="screenReaderToggle" 
                                   onchange="toggleScreenReader(this.checked)">
                        </div>

                        <div class="d-flex align-items-center justify-content-between">
                            <label for="textToggle" class="mb-0 cursor-pointer">
                                <strong>Larger Text</strong><br>
                                <small class="text-muted">Increase font size</small>
                            </label>
                            <input class="form-check-input" type="checkbox" id="textToggle" 
                                   onchange="toggleLargeText(this.checked)">
                        </div>
                    </div>
                </li>
                <!-- ==================== ACCESSIBILITY END ==================== -->

                <li class="nav-item"><a class="nav-link" href="respondent_dashboard.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<script src="./accessibility.js"></script>