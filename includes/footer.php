<?php
/**
 * SevaNest — Shared Footer Component
 *
 * Expected Config:
 *   - $base_path : Relative path multiplier to root (e.g. "../../")
 */

/* ── Path prefix helper for subfolder modules ──────────── */
$path_prefix = isset($base_path) ? $base_path : '';

/* ── App-wide version — change here only ─────────────────── */
if (!defined('SEVANEST_VERSION')) {
    define('SEVANEST_VERSION', 'v1.0.0');
}

/* ── Organisation details ─────────────────────────────────── */
$org_name    = "SevaNest";
$org_full    = "SevaNest Old Age Home";
$phone       = "+91 98765 43210";
$email       = "care@sevanest.in";
$website_url = "https://www.sevanest.in";
$address     = "12, Shanti Nagar, Pune — 411001, Maharashtra";

/* ── Logo check ────────────────────────────────────────── */
$logo_file = $path_prefix . 'assets/images/logo/logo.jpeg';
$show_logo = file_exists($logo_file);
?>

</div><!-- /.oahms-main-content -->

<!-- ╔══════════════════════════════════════════════════════════╗
     ║  SEVANEST FOOTER                                        ║
     ╚══════════════════════════════════════════════════════════╝ -->
<footer style="
    background: linear-gradient(160deg, #3d5c52 0%, #4e7466 50%, #6B9080 100%);
    border-top: 4px solid #D4A373;
    font-family: 'Outfit', sans-serif;
    color: #F6F4EC;
">

    <!-- ── Main Grid ─────────────────────────────────────── -->
    <div class="container py-5">
        <div class="row gy-5">

            <!-- COL 1 : Brand + About ───────────────────── -->
            <div class="col-12 col-md-6 col-lg-4">

                <!-- Logo + Name -->
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div style="
                        background: rgba(255,255,255,0.95);
                        border-radius: 10px;
                        padding: 6px;
                        width: 58px; height: 58px;
                        display:flex; align-items:center; justify-content:center;
                        box-shadow: 0 4px 12px rgba(0,0,0,0.18);
                        flex-shrink:0;
                    ">
                        <?php if ($show_logo): ?>
                            <img src="<?php echo htmlspecialchars($logo_file); ?>"
                                 alt="SevaNest"
                                 style="width:46px; height:46px; object-fit:contain;">
                        <?php else: ?>
                            <span style="color: var(--color-primary); font-weight: 800; font-size: 0.9rem;">SevaNest</span>
                        <?php endif; ?>
                    </div>
                    <div>
                        <div style="font-size:1.6rem; font-weight:800; color:#fff; line-height:1.1;">
                            Seva<span style="color:#D4A373;">Nest</span>
                        </div>
                        <div style="font-size:0.62rem; letter-spacing:2.5px; color:rgba(246,244,236,0.7); text-transform:uppercase; font-weight:500;">
                            Care &bull; Respect &bull; Together
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <p style="color:rgba(246,244,236,0.82); font-size:0.875rem; line-height:1.75; margin-bottom:1.5rem;">
                    SevaNest is a dedicated management platform for old age homes — providing seamless care coordination, health tracking, visitor management, and donor engagement to ensure every resident lives with dignity and comfort.
                </p>

                <!-- Social Buttons -->
                <div class="d-flex gap-2 flex-wrap">
                    <?php
                    $socials = [
                        ['icon' => 'bi-facebook',  'label' => 'Facebook'],
                        ['icon' => 'bi-instagram', 'label' => 'Instagram'],
                        ['icon' => 'bi-twitter-x', 'label' => 'Twitter/X'],
                        ['icon' => 'bi-youtube',   'label' => 'YouTube'],
                        ['icon' => 'bi-linkedin',  'label' => 'LinkedIn'],
                    ];
                    foreach ($socials as $s):
                    ?>
                    <a href="#"
                       aria-label="<?php echo $s['label']; ?>"
                       style="
                           display:inline-flex; align-items:center; justify-content:center;
                           width:36px; height:36px;
                           background:rgba(255,255,255,0.1);
                           border:1px solid rgba(255,255,255,0.2);
                           border-radius:8px;
                           color:#F6F4EC;
                           font-size:0.95rem;
                           text-decoration:none;
                           transition:all 0.2s ease;
                       "
                       onmouseover="this.style.background='#D4A373'; this.style.borderColor='#D4A373'; this.style.transform='translateY(-2px)';"
                       onmouseout="this.style.background='rgba(255,255,255,0.1)'; this.style.borderColor='rgba(255,255,255,0.2)'; this.style.transform='translateY(0)';">
                        <i class="bi <?php echo $s['icon']; ?>"></i>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- COL 2 : Quick Links ─────────────────────── -->
            <div class="col-6 col-md-3 col-lg-2">
                <h6 style="
                    color:#fff;
                    font-weight:700;
                    font-size:0.88rem;
                    letter-spacing:1.2px;
                    text-transform:uppercase;
                    margin-bottom:1.25rem;
                    padding-bottom:0.5rem;
                    border-bottom: 2px solid #D4A373;
                    display:inline-block;
                ">Quick Links</h6>

                <ul style="list-style:none; padding:0; margin:0;">
                    <?php
                    $quick_links = [
                        ['label'=>'Home',          'href'=>$path_prefix . 'index.php'],
                        ['label'=>'Login',         'href'=>$path_prefix . 'modules/authentication/login.php'],
                        ['label'=>'About Us',      'href'=>$website_url],
                        ['label'=>'Our Services',  'href'=>'#'],
                        ['label'=>'Donate',        'href'=>'#'],
                        ['label'=>'Contact Us',    'href'=>'#'],
                    ];
                    foreach ($quick_links as $link):
                    ?>
                    <li style="margin-bottom:0.6rem;">
                        <a href="<?php echo htmlspecialchars($link['href']); ?>"
                           style="
                               color:rgba(246,244,236,0.8);
                               text-decoration:none;
                               font-size:0.875rem;
                               display:flex;
                               align-items:center;
                               gap:0.4rem;
                               transition:all 0.18s ease;
                           "
                           onmouseover="this.style.color='#D4A373'; this.style.paddingLeft='4px';"
                           onmouseout="this.style.color='rgba(246,244,236,0.8)'; this.style.paddingLeft='0';">
                            <i class="bi bi-chevron-right" style="font-size:0.65rem; color:#D4A373;"></i>
                            <?php echo htmlspecialchars($link['label']); ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- COL 3 : Our Services ───────────────────── -->
            <div class="col-6 col-md-3 col-lg-2">
                <h6 style="
                    color:#fff;
                    font-weight:700;
                    font-size:0.88rem;
                    letter-spacing:1.2px;
                    text-transform:uppercase;
                    margin-bottom:1.25rem;
                    padding-bottom:0.5rem;
                    border-bottom: 2px solid #D4A373;
                    display:inline-block;
                ">Our Services</h6>

                <ul style="list-style:none; padding:0; margin:0;">
                    <?php
                    $services = [
                        'Resident Care',
                        'Medical Support',
                        'Visitor Management',
                        'Donation Portal',
                        'Staff Management',
                        'Health Records',
                    ];
                    foreach ($services as $svc):
                    ?>
                    <li style="margin-bottom:0.6rem;">
                        <a href="#"
                           style="
                               color:rgba(246,244,236,0.8);
                               text-decoration:none;
                               font-size:0.875rem;
                               display:flex;
                               align-items:center;
                               gap:0.4rem;
                               transition:all 0.18s ease;
                           "
                           onmouseover="this.style.color='#D4A373'; this.style.paddingLeft='4px';"
                           onmouseout="this.style.color='rgba(246,244,236,0.8)'; this.style.paddingLeft='0';">
                            <i class="bi bi-chevron-right" style="font-size:0.65rem; color:#D4A373;"></i>
                            <?php echo htmlspecialchars($svc); ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- COL 4 : Contact Details ────────────────── -->
            <div class="col-12 col-md-6 col-lg-4">
                <h6 style="
                    color:#fff;
                    font-weight:700;
                    font-size:0.88rem;
                    letter-spacing:1.2px;
                    text-transform:uppercase;
                    margin-bottom:1.25rem;
                    padding-bottom:0.5rem;
                    border-bottom: 2px solid #D4A373;
                    display:inline-block;
                ">Get In Touch</h6>

                <!-- Address -->
                <div style="display:flex; gap:0.875rem; margin-bottom:1rem; align-items:flex-start;">
                    <span style="
                        width:38px; height:38px; flex-shrink:0;
                        background:rgba(212,163,115,0.18);
                        border:1px solid rgba(212,163,115,0.35);
                        border-radius:9px;
                        display:flex; align-items:center; justify-content:center;
                        color:#D4A373; font-size:0.95rem;
                    "><i class="bi bi-geo-alt-fill"></i></span>
                    <div>
                        <div style="color:rgba(246,244,236,0.55); font-size:0.7rem; font-weight:600; text-transform:uppercase; letter-spacing:1px; margin-bottom:2px;">Address</div>
                        <div style="color:#F6F4EC; font-size:0.875rem; line-height:1.5;"><?php echo htmlspecialchars($address); ?></div>
                    </div>
                </div>

                <!-- Phone -->
                <div style="display:flex; gap:0.875rem; margin-bottom:1rem; align-items:flex-start;">
                    <span style="
                        width:38px; height:38px; flex-shrink:0;
                        background:rgba(212,163,115,0.18);
                        border:1px solid rgba(212,163,115,0.35);
                        border-radius:9px;
                        display:flex; align-items:center; justify-content:center;
                        color:#D4A373; font-size:0.95rem;
                    "><i class="bi bi-telephone-fill"></i></span>
                    <div>
                        <div style="color:rgba(246,244,236,0.55); font-size:0.7rem; font-weight:600; text-transform:uppercase; letter-spacing:1px; margin-bottom:2px;">Phone</div>
                        <a href="tel:<?php echo preg_replace('/[^+\d]/', '', $phone); ?>"
                           style="color:#F6F4EC; font-size:0.9rem; font-weight:600; text-decoration:none;"
                           onmouseover="this.style.color='#D4A373';"
                           onmouseout="this.style.color='#F6F4EC';">
                            <?php echo htmlspecialchars($phone); ?>
                        </a>
                    </div>
                </div>

                <!-- Email -->
                <div style="display:flex; gap:0.875rem; margin-bottom:1rem; align-items:flex-start;">
                    <span style="
                        width:38px; height:38px; flex-shrink:0;
                        background:rgba(212,163,115,0.18);
                        border:1px solid rgba(212,163,115,0.35);
                        border-radius:9px;
                        display:flex; align-items:center; justify-content:center;
                        color:#D4A373; font-size:0.95rem;
                    "><i class="bi bi-envelope-fill"></i></span>
                    <div>
                        <div style="color:rgba(246,244,236,0.55); font-size:0.7rem; font-weight:600; text-transform:uppercase; letter-spacing:1px; margin-bottom:2px;">Email</div>
                        <a href="mailto:<?php echo htmlspecialchars($email); ?>"
                           style="color:#F6F4EC; font-size:0.875rem; text-decoration:none;"
                           onmouseover="this.style.color='#D4A373';"
                           onmouseout="this.style.color='#F6F4EC';">
                            <?php echo htmlspecialchars($email); ?>
                        </a>
                    </div>
                </div>

                <!-- Website -->
                <div style="display:flex; gap:0.875rem; margin-bottom:1.5rem; align-items:flex-start;">
                    <span style="
                        width:38px; height:38px; flex-shrink:0;
                        background:rgba(212,163,115,0.18);
                        border:1px solid rgba(212,163,115,0.35);
                        border-radius:9px;
                        display:flex; align-items:center; justify-content:center;
                        color:#D4A373; font-size:0.95rem;
                    "><i class="bi bi-globe2"></i></span>
                    <div>
                        <div style="color:rgba(246,244,236,0.55); font-size:0.7rem; font-weight:600; text-transform:uppercase; letter-spacing:1px; margin-bottom:2px;">Website</div>
                        <a href="<?php echo htmlspecialchars($website_url); ?>"
                           target="_blank"
                           rel="noopener noreferrer"
                           style="color:#F6F4EC; font-size:0.875rem; text-decoration:none;"
                           onmouseover="this.style.color='#D4A373';"
                           onmouseout="this.style.color='#F6F4EC';">
                            www.sevanest.in <i class="bi bi-box-arrow-up-right" style="font-size:0.7rem;"></i>
                        </a>
                    </div>
                </div>

                <!-- Emergency Notice -->
                <div style="
                    background: rgba(212,163,115,0.15);
                    border: 1px solid rgba(212,163,115,0.35);
                    border-left: 3px solid #D4A373;
                    border-radius: 8px;
                    padding: 0.75rem 1rem;
                    font-size: 0.8rem;
                    color: rgba(246,244,236,0.85);
                    display:flex;
                    gap:0.6rem;
                    align-items:center;
                ">
                    <i class="bi bi-telephone-inbound-fill" style="color:#D4A373; font-size:1rem; flex-shrink:0;"></i>
                    <span><strong style="color:#D4A373;">24/7 Emergency:</strong> <?php echo htmlspecialchars($phone); ?></span>
                </div>
            </div>

        </div>
    </div>

    <!-- ── Bottom Bar ─────────────────────────────────────── -->
    <div style="
        background:rgba(0,0,0,0.25);
        border-top:1px solid rgba(246,244,236,0.1);
        padding: 1rem 0;
    ">
        <div class="container">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">

                <span style="color:rgba(246,244,236,0.65); font-size:0.82rem; text-align:center;">
                    &copy; <?php echo date('Y'); ?>
                    <strong style="color:rgba(246,244,236,0.9);"><?php echo htmlspecialchars($org_full); ?></strong>.
                    All rights reserved.
                </span>

                <div class="d-flex align-items-center gap-3">
                    <a href="#" style="color:rgba(246,244,236,0.5); font-size:0.78rem; text-decoration:none;"
                       onmouseover="this.style.color='#D4A373';" onmouseout="this.style.color='rgba(246,244,236,0.5)';">
                        Privacy Policy
                    </a>
                    <span style="color:rgba(246,244,236,0.25);">|</span>
                    <a href="#" style="color:rgba(246,244,236,0.5); font-size:0.78rem; text-decoration:none;"
                       onmouseover="this.style.color='#D4A373';" onmouseout="this.style.color='rgba(246,244,236,0.5)';">
                        Terms of Use
                    </a>
                    <span style="color:rgba(246,244,236,0.25);">|</span>
                    <span style="
                        display:inline-flex; align-items:center; gap:0.35rem;
                        background:rgba(255,255,255,0.08);
                        border:1px solid rgba(255,255,255,0.12);
                        border-radius:50px;
                        padding:0.18rem 0.65rem;
                        color:rgba(246,244,236,0.55);
                        font-size:0.76rem;
                        font-family:'Courier New', monospace;
                    ">
                        <i class="bi bi-cpu" style="font-size:0.7rem;"></i>
                        <?php echo SEVANEST_VERSION; ?>
                    </span>
                </div>

            </div>
        </div>
    </div>

</footer>

<!-- Bootstrap 5 Bundle (includes Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Shared common.js script -->
<script src="<?php echo htmlspecialchars($path_prefix); ?>assets/js/common.js"></script>

</body>
</html>
