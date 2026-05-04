<?php
$staffForPin = $staffForPin ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#1a6b3c">
    <link rel="manifest" href="manifest.json">
    <title>Login - Kizo SOP Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        body { background-color: var(--bg-color); display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 1.5rem; margin: 0; }
        .login-wrapper { width: 100%; max-width: 400px; }
        .brand-logo { font-size: 3rem; color: var(--brand-primary); margin-bottom: 16px; text-align: center; }
        .pin-display { letter-spacing: 0.75rem; font-size: 2rem; font-weight: 700; min-height: 3rem; color: var(--brand-primary); }
        .pin-dots span { width: 18px; height: 18px; border-radius: 50%; border: 2px solid #ccc; display: inline-block; margin: 0 6px; transition: background 0.15s; }
        .pin-dots span.filled { background: var(--brand-primary); border-color: var(--brand-primary); }
        .keypad-btn { width: 72px; height: 72px; border-radius: 50%; font-size: 1.5rem; font-weight: 700; border: 2px solid #e5e7eb; background: white; color: #111; transition: all 0.1s; cursor: pointer; }
        .keypad-btn:active { background: var(--brand-primary); color: white; border-color: var(--brand-primary); transform: scale(0.92); }
        .keypad-btn.del { background: #fee2e2; border-color: #fca5a5; color: #dc2626; }
    </style>
</head>
<body>
<div class="login-wrapper">
    <div class="text-center mb-4">
        <div class="brand-logo"><i class="fas fa-clipboard-check"></i></div>
        <h2 class="fw-bold text-brand">Kizo SOP</h2>
        <p class="text-muted">Operations Manager</p>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-pills nav-justified mb-4 bg-white rounded-3 p-1 shadow-sm" id="loginTabs">
        <li class="nav-item">
            <a class="nav-link <?php echo (!isset($_GET['tab']) || $_GET['tab'] === 'email') ? 'active' : ''; ?>" href="index.php?action=login&tab=email">
                <i class="fas fa-envelope me-1"></i> Email
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo (isset($_GET['tab']) && $_GET['tab'] === 'pin') ? 'active' : ''; ?>" href="index.php?action=login&tab=pin">
                <i class="fas fa-th me-1"></i> Quick PIN
            </a>
        </li>
    </ul>

    <!-- Error -->
    <?php if (isset($error) && $error): ?>
    <div class="alert alert-danger py-2 rounded-3 text-center fw-500 mb-3" role="alert">
        <i class="fas fa-exclamation-circle me-1"></i> <?php echo htmlspecialchars($error); ?>
    </div>
    <?php endif; ?>

    <!-- Email Login Tab -->
    <?php if (!isset($_GET['tab']) || $_GET['tab'] === 'email'): ?>
    <form method="POST" action="index.php?action=login&tab=email" class="card p-4 border-0 shadow-sm">
        <div class="mb-3">
            <label class="form-label text-muted fw-600 small mb-2">Email Address</label>
            <input type="email" name="email" class="form-control" placeholder="name@kizo.com" required autofocus autocomplete="username">
        </div>
        <div class="mb-4">
            <label class="form-label text-muted fw-600 small mb-2">Password</label>
            <input type="password" name="password" class="form-control" placeholder="••••••••" required autocomplete="current-password">
        </div>
        <button type="submit" class="btn btn-primary w-100 py-3 fs-5">Sign In</button>
    </form>

    <!-- PIN Login Tab -->
    <?php else: ?>
    <div class="card border-0 shadow-sm p-4 overflow-hidden" style="border-radius: 20px;">
        <?php if (empty($staffForPin)): ?>
            <div class="text-center py-5">
                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px;">
                    <i class="fas fa-lock text-muted fs-1"></i>
                </div>
                <p class="text-muted fw-600">No staff PINs set up yet.</p>
                <p class="text-muted small px-4">Staff can set their PIN from their Profile page after logging in with email.</p>
            </div>
        <?php else: ?>
        <form method="POST" action="index.php?action=pin_login" id="pinForm">
            <input type="hidden" name="pin" id="pinInput" value="">
            <input type="hidden" name="user_id" id="userIdInput" value="">

            <div class="mb-4">
                <label class="form-label text-muted fw-700 small text-uppercase mb-3 d-block text-center" style="letter-spacing: 1px;">Select Your Name</label>
                <div class="staff-selector d-flex overflow-auto pb-2 gap-3" style="scrollbar-width: none; -ms-overflow-style: none;">
                    <?php foreach ($staffForPin as $staff): ?>
                    <div class="staff-card text-center" onclick="selectStaff(<?php echo $staff['id']; ?>, this)" style="min-width: 80px; cursor: pointer;">
                        <div class="avatar-circle mx-auto mb-2 d-flex align-items-center justify-content-center fw-bold text-white shadow-sm" 
                             style="width: 56px; height: 56px; border-radius: 50%; background: linear-gradient(135deg, #1a6b3c, #2d9a5a); font-size: 1.2rem; transition: all 0.2s;">
                            <?php 
                                $names = explode(' ', $staff['name']);
                                echo strtoupper(substr($names[0], 0, 1) . (isset($names[1]) ? substr($names[1], 0, 1) : ''));
                            ?>
                        </div>
                        <div class="small fw-600 text-dark text-truncate" style="max-width: 80px;"><?php echo explode(' ', htmlspecialchars($staff['name']))[0]; ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="text-center mb-4 mt-2">
                <div class="pin-dots d-flex justify-content-center align-items-center gap-3">
                    <span id="d1"></span><span id="d2"></span><span id="d3"></span><span id="d4"></span>
                </div>
            </div>

            <!-- Keypad -->
            <div class="keypad-container mx-auto" style="max-width: 280px;">
                <div class="row g-3 justify-content-center">
                    <?php foreach ([1,2,3,4,5,6,7,8,9] as $n): ?>
                    <div class="col-4 text-center">
                        <button type="button" class="keypad-btn" onclick="addDigit('<?php echo $n; ?>')"><?php echo $n; ?></button>
                    </div>
                    <?php endforeach; ?>
                    <div class="col-4 text-center"></div>
                    <div class="col-4 text-center">
                        <button type="button" class="keypad-btn" onclick="addDigit('0')">0</button>
                    </div>
                    <div class="col-4 text-center">
                        <button type="button" class="keypad-btn del border-0 shadow-none" onclick="removeDigit()">
                            <i class="fas fa-backspace"></i>
                        </button>
                    </div>
                </div>
            </div>
        </form>
        <?php endif; ?>
    </div>

    <style>
        .staff-selector::-webkit-scrollbar { display: none; }
        .staff-card.selected .avatar-circle {
            transform: scale(1.15);
            box-shadow: 0 0 0 3px white, 0 0 0 6px var(--brand-primary) !important;
        }
        .staff-card.selected .small { color: var(--brand-primary) !important; font-weight: 700 !important; }
        
        .pin-dots span { width: 14px; height: 14px; border-radius: 50%; border: 2px solid #ddd; transition: all 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
        .pin-dots span.filled { background: var(--brand-primary); border-color: var(--brand-primary); transform: scale(1.2); }
        
        .keypad-btn {
            width: 68px; height: 68px; border-radius: 50%; border: none; background: #f8fafc;
            color: #334155; font-size: 1.5rem; font-weight: 600; transition: all 0.1s;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }
        .keypad-btn:active { background: var(--brand-primary); color: white; transform: scale(0.9); }
        .keypad-btn.del:active { background: #fee2e2; color: #ef4444; }
    </style>

    <script>
    let pin = '';
    let selectedUserId = null;

    function selectStaff(userId, el) {
        selectedUserId = userId;
        document.getElementById('userIdInput').value = userId;
        
        // Update UI
        document.querySelectorAll('.staff-card').forEach(c => c.classList.remove('selected'));
        el.classList.add('selected');
        
        // Scroll into view
        el.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
    }

    function updateDots() {
        for (let i = 1; i <= 4; i++) {
            const dot = document.getElementById('d' + i);
            if (dot) dot.className = (i <= pin.length) ? 'filled' : '';
        }
    }

    function addDigit(d) {
        if (!selectedUserId) {
            // Shake the staff selector to prompt selection
            const selector = document.querySelector('.staff-selector');
            selector.classList.add('shake');
            setTimeout(() => selector.classList.remove('shake'), 500);
            return;
        }
        
        if (pin.length >= 4) return;
        pin += d;
        updateDots();
        
        if (pin.length === 4) {
            document.getElementById('pinInput').value = pin;
            setTimeout(() => {
                document.getElementById('pinForm').submit();
            }, 150);
        }
    }

    function removeDigit() { 
        pin = pin.slice(0, -1); 
        updateDots(); 
    }
    </script>
    <?php endif; ?>

    <p class="text-center text-muted small mt-4 mb-0">
        <?php if (isset($_GET['tab']) && $_GET['tab'] === 'pin'): ?>
        <a href="index.php?action=login" class="text-brand text-decoration-none">← Sign in with email instead</a>
        <?php else: ?>
        Staff can also use <a href="index.php?action=login&tab=pin" class="text-brand text-decoration-none">Quick PIN Login →</a>
        <?php endif; ?>
    </p>
    <div class="text-center mt-5">
        <p class="text-muted small mb-0">Built with ❤️ by <strong>George Asiedu Annan</strong></p>
    </div>
</div>
</body>
</html>
