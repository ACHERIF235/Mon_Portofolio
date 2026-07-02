<?php
$title = $settings['hero_name_' . $lang] ?? 'Portfolio';
$accent = $settings['accent_color'] ?? '#D4A843'; // Dynamic accent color from admin
$profileImageRaw = $settings['profile_photo'] ?? '';
$profileImage = !empty($profileImageRaw) ? (str_starts_with($profileImageRaw, 'http') ? $profileImageRaw : 'assets/uploads/' . $profileImageRaw) : 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=1200&q=80';

$resumeFileRaw = $settings['resume_file'] ?? '';
$resumeFile = !empty($resumeFileRaw) ? (str_starts_with($resumeFileRaw, 'http') ? $resumeFileRaw : 'assets/uploads/' . $resumeFileRaw) : null;

function safeText(string $text): string {
    return nl2br(htmlspecialchars($text, ENT_QUOTES, 'UTF-8'));
}

// Map database fields to the UI design
$heroName = SettingModel::get('hero_name_' . $lang, 'Acherif AHMAT ALIFA');
$heroTitle = SettingModel::get('hero_title_' . $lang, 'Ingénieur Informatique & Développeur Full Stack');
$heroLocation = SettingModel::get('hero_location_' . $lang, 'Dakar, Sénégal');
$aboutText = SettingModel::get('about_text_' . $lang, '');
$accent = SettingModel::get('accent_color', '#c9a227');
$themeMode = SettingModel::get('theme_mode', 'light');

// Configuration des couleurs dynamiques selon le thème
if ($themeMode === 'dark') {
    $c_charbon = '#0F0F13';
    $c_creme = '#F5F3EE';
    $c_ardoise = '#1E1E26';
    $hero_bg_opacity = '40';
} else {
    $c_charbon = '#F8FAFC';
    $c_creme = '#0F172A';
    $c_ardoise = '#E2E8F0';
    $hero_bg_opacity = '25';
}
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= htmlspecialchars($title) ?> | Portfolio</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- Fonts for Preset B (Nocturne Prestige) -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@0,400;0,700;1,400;1,700&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        charbon: '<?= $c_charbon ?>',
                        or: '<?= $accent ?>',
                        creme: '<?= $c_creme ?>',
                        ardoise: '<?= $c_ardoise ?>'
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        serif: ['Playfair Display', 'serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                    borderRadius: {
                        '4xl': '2rem',
                        '5xl': '3rem',
                    }
                }
            }
        }
    </script>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/public.css">
    
    <!-- GSAP & ScrollTrigger -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-charbon text-creme antialiased selection:bg-or/30 selection:text-or overflow-x-hidden w-full">

    <!-- Noise SVG Filter -->
    <svg style="display:none;">
        <filter id="noise">
            <feTurbulence type="fractalNoise" baseFrequency="0.65" numOctaves="3" stitchTiles="stitch"/>
        </filter>
    </svg>
    <div class="noise-overlay" style="filter: url(#noise);"></div>

    <!-- A. NAVBAR -->
    <nav id="navbar" class="fixed top-4 md:top-6 left-1/2 -translate-x-1/2 z-50 bg-transparent transition-all duration-500 rounded-full px-5 md:px-8 py-3 md:py-4 flex justify-between items-center gap-4 md:gap-12 border border-transparent w-[95%] max-w-max md:w-auto">
        <div class="font-sans font-bold tracking-tight text-xl text-creme shrink-0">
            <?= htmlspecialchars(substr($heroName, 0, 2)) ?>.
        </div>
        
        <!-- Desktop Menu -->
        <div class="hidden md:flex gap-8 text-sm font-medium shrink-0">
            <a href="#about" class="text-creme hover:text-or transition-colors hover-lift"><?= $lang === 'fr' ? 'À propos' : 'About' ?></a>
            <a href="#experience" class="text-creme hover:text-or transition-colors hover-lift"><?= $lang === 'fr' ? 'Projets' : 'Projects' ?></a>
            <a href="#skills" class="text-creme hover:text-or transition-colors hover-lift"><?= $lang === 'fr' ? 'Compétences' : 'Skills' ?></a>
            <a href="#contact" class="text-creme hover:text-or transition-colors hover-lift">Contact</a>
        </div>
        
        <div class="flex items-center gap-4">
            <?php if ($resumeFile): ?>
                <a href="<?= htmlspecialchars($resumeFile) ?>" download class="hidden md:flex bg-or text-charbon px-5 py-2 md:px-6 md:py-2.5 rounded-full font-semibold magnet-btn text-sm shrink-0">
                    <?= $lang === 'fr' ? 'CV' : 'CV' ?>
                </a>
            <?php endif; ?>
            
            <!-- Mobile Menu Toggle -->
            <button id="mobileMenuBtn" class="md:hidden text-creme hover:text-or transition-colors flex items-center justify-center p-2">
                <i data-lucide="menu"></i>
            </button>
        </div>
    </nav>

    <!-- Mobile Menu Overlay -->
    <div id="mobileMenu" class="fixed inset-0 bg-charbon/98 backdrop-blur-md z-40 flex flex-col items-center justify-center gap-8 opacity-0 pointer-events-none transition-all duration-300 transform scale-105">
        <button id="closeMenuBtn" class="absolute top-6 right-6 text-creme hover:text-or p-2">
            <i data-lucide="x" class="w-8 h-8"></i>
        </button>
        <a href="#about" class="mobile-link text-3xl font-serif text-creme hover:text-or transition-colors"><?= $lang === 'fr' ? 'À propos' : 'About' ?></a>
        <a href="#experience" class="mobile-link text-3xl font-serif text-creme hover:text-or transition-colors"><?= $lang === 'fr' ? 'Projets' : 'Projects' ?></a>
        <a href="#skills" class="mobile-link text-3xl font-serif text-creme hover:text-or transition-colors"><?= $lang === 'fr' ? 'Compétences' : 'Skills' ?></a>
        <a href="#contact" class="mobile-link text-3xl font-serif text-creme hover:text-or transition-colors">Contact</a>
        <?php if ($resumeFile): ?>
            <a href="<?= htmlspecialchars($resumeFile) ?>" download class="mobile-link bg-or text-charbon px-8 py-3 mt-4 rounded-full font-bold text-lg">
                <?= $lang === 'fr' ? 'Télécharger CV' : 'Download CV' ?>
            </a>
        <?php endif; ?>
    </div>

    <main class="w-full overflow-hidden">
        <!-- B. HERO SECTION -->
        <section class="min-h-[100dvh] flex flex-col justify-center items-center text-center relative px-4 pt-24 pb-12 overflow-hidden w-full">
            <!-- Background Image overlay -->
            <div class="absolute inset-0 z-[-1] opacity-<?= $hero_bg_opacity ?>">
                <img src="<?= $themeMode === 'dark' ? 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?auto=format&fit=crop&w=2000&q=80' : 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?auto=format&fit=crop&w=2000&q=80' ?>" alt="Background Texture" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-charbon via-charbon/80 to-transparent"></div>
            </div>

            <div class="hero-content flex flex-col items-center w-full max-w-full">
                <div class="w-28 h-28 sm:w-32 sm:h-32 md:w-40 md:h-40 rounded-full overflow-hidden border-2 border-or/30 mb-6 md:mb-8 hero-item shrink-0 cursor-pointer transition-transform hover:scale-105 shadow-xl" onclick="openImageModal()">
                    <img src="<?= htmlspecialchars($profileImage) ?>" alt="<?= htmlspecialchars($heroName) ?>" class="w-full h-full object-cover" style="image-rendering: high-quality; filter: contrast(1.05) saturate(1.1);">
                </div>
                <h1 class="font-sans font-extrabold text-4xl sm:text-5xl md:text-7xl tracking-tighter mb-4 hero-item text-creme break-words max-w-full px-2">
                    <?= htmlspecialchars($heroName) ?>
                </h1>
                <h2 class="font-serif italic text-xl sm:text-2xl md:text-4xl text-or mb-8 md:mb-10 hero-item break-words max-w-full px-2">
                    <?= htmlspecialchars($heroTitle) ?>
                </h2>
                
                <div class="flex flex-wrap justify-center items-center gap-2 sm:gap-4 text-xs sm:text-sm font-mono text-creme/60 mb-10 md:mb-12 hero-item">
                    <span><?= htmlspecialchars($heroLocation) ?></span>
                    <span class="hidden sm:block w-1.5 h-1.5 rounded-full bg-or"></span>
                    <span><?= count($projects) ?> <?= $lang === 'fr' ? 'Projets' : 'Projects' ?></span>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 hero-item w-full sm:w-auto px-4 sm:px-0">
                    <a href="#contact" class="bg-or text-charbon px-8 py-4 rounded-full font-semibold magnet-btn w-full sm:w-auto text-center shadow-lg">
                        <?= $lang === 'fr' ? 'Me contacter' : 'Contact Me' ?>
                    </a>
                    <a href="#experience" class="border-2 border-or/30 text-creme px-8 py-4 rounded-full font-medium hover:bg-or/10 transition-colors magnet-btn w-full sm:w-auto text-center">
                        <?= $lang === 'fr' ? 'Explorer mon travail' : 'Explore my work' ?>
                    </a>
                </div>
            </div>
        </section>

        <!-- C. ABOUT SECTION -->
        <section id="about" class="py-20 md:py-32 px-5 md:px-12 bg-ardoise/20 w-full overflow-hidden">
            <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-[1fr_2px_2fr] gap-8 md:gap-20 items-start">
                <div class="about-title w-full">
                    <h3 class="font-serif italic text-4xl sm:text-5xl md:text-7xl text-or leading-tight break-words">
                        <?= $lang === 'fr' ? 'À propos' : 'About' ?>
                    </h3>
                </div>
                <div class="hidden md:block w-full h-full bg-or/20 rounded-full about-line"></div>
                <div class="font-sans text-base sm:text-lg md:text-xl text-creme/80 leading-relaxed font-light about-text w-full">
                    <p><?= safeText($aboutText) ?></p>
                </div>
            </div>
        </section>

        <!-- D. EXPERIENCE / PROJECTS -->
        <section id="experience" class="py-20 md:py-32 px-5 md:px-12 w-full overflow-hidden">
            <div class="max-w-5xl mx-auto relative w-full">
                <h3 class="font-sans font-bold text-sm tracking-widest uppercase text-creme/40 mb-12 md:mb-20 text-center">
                    <?= $lang === 'fr' ? 'Travaux & Projets' : 'Work & Projects' ?>
                </h3>
                
                <!-- Timeline Line -->
                <div class="hidden md:block absolute left-1/2 top-24 bottom-0 w-px bg-or/20 -translate-x-1/2"></div>
                
                <div class="space-y-12 md:space-y-16 w-full">
                    <?php foreach ($projects as $index => $project): ?>
                        <div class="exp-wrapper relative flex flex-col md:flex-row <?= $index % 2 === 0 ? 'md:flex-row-reverse' : '' ?> items-center justify-between group w-full">
                            <!-- Timeline Dot -->
                            <div class="hidden md:block absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-3 h-3 bg-or rounded-full shadow-[0_0_15px_rgba(212,168,67,0.5)] z-10 dot-pulse"></div>
                            
                            <div class="w-full md:w-[45%]">
                                <article class="exp-card bg-ardoise/10 shadow-xl p-6 md:p-8 rounded-3xl md:rounded-[2rem] border border-ardoise relative overflow-hidden w-full transition-colors hover:bg-ardoise/20">
                                    <div class="font-mono text-or text-xs md:text-sm mb-3 md:mb-4">
                                        <?= !empty($project['created_at']) ? date('Y', strtotime($project['created_at'])) : '2023' ?>
                                    </div>
                                    <h4 class="font-sans font-bold text-xl md:text-2xl text-creme mb-2 break-words">
                                        <?= htmlspecialchars($project['title_' . $lang] ?? $project['title_fr']) ?>
                                    </h4>
                                    <p class="font-sans text-creme/60 text-sm leading-relaxed mb-5 md:mb-6 line-clamp-3">
                                        <?= htmlspecialchars($project['description_' . $lang] ?? $project['description_fr']) ?>
                                    </p>
                                    <?php if (!empty($project['link'])): ?>
                                        <a href="<?= htmlspecialchars($project['link']) ?>" target="_blank" class="inline-flex items-center text-or font-medium text-sm hover:underline hover-lift break-all">
                                            <?= $lang === 'fr' ? 'Voir le projet' : 'View Project' ?> 
                                            <i data-lucide="arrow-up-right" class="w-4 h-4 ml-1 shrink-0"></i>
                                        </a>
                                    <?php endif; ?>
                                </article>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- E. SKILLS (Tags Pondérés) -->
        <section id="skills" class="py-20 md:py-32 px-5 md:px-12 bg-ardoise/20 w-full overflow-hidden">
            <div class="max-w-6xl mx-auto text-center w-full">
                <h3 class="font-serif italic text-4xl sm:text-5xl md:text-6xl text-or mb-10 md:mb-16">
                    <?= $lang === 'fr' ? 'Expertise' : 'Expertise' ?>
                </h3>
                
                <div class="flex flex-wrap justify-center gap-3 md:gap-6 skills-container w-full">
                    <?php foreach ($skills as $skill): ?>
                        <?php 
                            $level = (int)$skill['level']; // Assumes level is somewhat numeric or can be mapped
                            $isExpert = $level > 70 || strtolower($skill['level']) === 'expert' || strtolower($skill['level']) === 'avancé';
                        ?>
                        <div class="skill-tag <?= $isExpert ? 'bg-or text-white font-bold px-4 py-2 md:px-6 md:py-3 text-sm md:text-lg' : 'bg-ardoise/10 border border-ardoise text-creme px-3 py-1.5 md:px-5 md:py-2.5 text-xs md:text-base shadow-sm' ?> rounded-full font-mono transition-transform hover:scale-105 cursor-default max-w-full truncate">
                            <?= htmlspecialchars($skill['name_' . $lang] ?? $skill['name_fr']) ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- F. EDUCATION -->
        <section class="py-20 md:py-32 px-5 md:px-12 w-full overflow-hidden">
            <div class="max-w-4xl mx-auto w-full">
                <h3 class="font-sans font-bold text-sm tracking-widest uppercase text-creme/40 mb-10 md:mb-16 text-center">
                    <?= $lang === 'fr' ? 'Fondations' : 'Foundations' ?>
                </h3>
                
                <div class="grid gap-4 md:gap-6 w-full">
                    <?php if(!empty($education)): ?>
                        <?php foreach($education as $edu): ?>
                            <article class="edu-card bg-ardoise/10 shadow-lg p-6 md:p-8 rounded-3xl md:rounded-[2rem] border border-ardoise flex flex-col sm:flex-row sm:items-center justify-between gap-4 transition-colors hover:shadow-xl w-full hover:bg-ardoise/20">
                                <div class="w-full">
                                    <h4 class="font-sans font-bold text-lg md:text-xl text-creme mb-1 break-words"><?= htmlspecialchars($edu['title_' . $lang] ?? ($edu['title_fr'] ?? '')) ?></h4>
                                    <p class="font-sans text-creme/60 text-sm md:text-base break-words"><?= htmlspecialchars($edu['organization'] ?? '') ?></p>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <article class="edu-card bg-ardoise/10 shadow-lg p-6 md:p-8 rounded-3xl md:rounded-[2rem] border border-ardoise flex flex-col sm:flex-row sm:items-center justify-between gap-4 transition-colors hover:shadow-xl w-full hover:bg-ardoise/20">
                            <div class="w-full">
                                <h4 class="font-sans font-bold text-lg md:text-xl text-creme mb-1 break-words">Formation par défaut</h4>
                                <p class="font-sans text-creme/60 text-sm md:text-base break-words">Université / École</p>
                            </div>
                        </article>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- G. CONTACT -->
        <section id="contact" class="py-20 md:py-32 px-5 md:px-12 relative overflow-hidden w-full">
            <div class="absolute inset-0 bg-or/5"></div>
            <div class="max-w-4xl mx-auto text-center relative z-10 w-full">
                <h2 class="font-serif italic text-4xl sm:text-5xl md:text-7xl text-or mb-10 md:mb-12 break-words">
                    <?= $lang === 'fr' ? 'Travaillons ensemble' : "Let's work together" ?>
                </h2>
                
                <div class="flex flex-col sm:flex-row flex-wrap justify-center gap-4 md:gap-6 mb-12 md:mb-16 contact-links w-full">
                    <?php if(!empty($settings['contact_email'])): ?>
                        <a href="mailto:<?= htmlspecialchars($settings['contact_email']) ?>" class="group flex items-center gap-2 bg-ardoise p-4 rounded-2xl md:rounded-[1rem] hover:bg-ardoise/80 hover-lift w-full sm:w-auto justify-center">
                            <i data-lucide="mail" class="w-5 h-5 text-or shrink-0"></i>
                            <span class="font-mono text-xs md:text-sm text-creme group-hover:underline break-all"><?= htmlspecialchars($settings['contact_email']) ?></span>
                        </a>
                    <?php endif; ?>
                    <?php if(!empty($settings['github_url'])): ?>
                        <a href="<?= htmlspecialchars($settings['github_url']) ?>" target="_blank" class="group flex items-center gap-2 bg-ardoise p-4 rounded-2xl md:rounded-[1rem] hover:bg-ardoise/80 hover-lift w-full sm:w-auto justify-center">
                            <i data-lucide="github" class="w-5 h-5 text-or shrink-0"></i>
                            <span class="font-mono text-xs md:text-sm text-creme group-hover:underline">GitHub</span>
                        </a>
                    <?php endif; ?>
                    <?php if(!empty($settings['contact_phone'])): ?>
                        <a href="tel:<?= htmlspecialchars($settings['contact_phone']) ?>" class="group flex items-center gap-2 bg-ardoise p-4 rounded-2xl md:rounded-[1rem] hover:bg-ardoise/80 hover-lift w-full sm:w-auto justify-center">
                            <i data-lucide="phone" class="w-5 h-5 text-or shrink-0"></i>
                            <span class="font-mono text-xs md:text-sm text-creme group-hover:underline break-all"><?= htmlspecialchars($settings['contact_phone']) ?></span>
                        </a>
                    <?php endif; ?>
                    <?php if(!empty($settings['contact_whatsapp'])): ?>
                        <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $settings['contact_whatsapp']) ?>" target="_blank" class="group flex items-center gap-2 bg-ardoise p-4 rounded-2xl md:rounded-[1rem] hover:bg-ardoise/80 hover-lift w-full sm:w-auto justify-center">
                            <svg class="w-5 h-5 text-or shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 21l1.65-3.8a9 9 0 1 1 3.4 2.9L3 21"></path>
                                <path d="M9 10a.5.5 0 0 0 1 0V9a.5.5 0 0 0-1 0v1a5 5 0 0 0 5 5h1a.5.5 0 0 0 0-1h-1a.5.5 0 0 0 0 1"></path>
                            </svg>
                            <span class="font-mono text-xs md:text-sm text-creme group-hover:underline break-all">WhatsApp</span>
                        </a>
                    <?php endif; ?>
                </div>

                <form action="contact-submit.php" method="POST" class="max-w-2xl mx-auto mt-12 bg-ardoise/10 p-6 md:p-10 rounded-[2rem] border border-ardoise/30 backdrop-blur-md text-left shadow-2xl relative z-20">
                    <input type="hidden" name="lang" value="<?= htmlspecialchars($lang) ?>">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(generate_csrf_token()) ?>">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block font-sans text-sm font-semibold text-creme mb-2"><?= $lang === 'fr' ? 'Nom complet' : 'Full Name' ?></label>
                            <input type="text" name="name" required class="w-full bg-charbon/50 border border-ardoise/50 rounded-xl px-4 py-3 text-creme focus:outline-none focus:border-or focus:ring-1 focus:ring-or transition-all" placeholder="<?= $lang === 'fr' ? 'Votre nom' : 'Your name' ?>">
                        </div>
                        <div>
                            <label class="block font-sans text-sm font-semibold text-creme mb-2"><?= $lang === 'fr' ? 'Email' : 'Email Address' ?></label>
                            <input type="email" name="email" required class="w-full bg-charbon/50 border border-ardoise/50 rounded-xl px-4 py-3 text-creme focus:outline-none focus:border-or focus:ring-1 focus:ring-or transition-all" placeholder="email@example.com">
                        </div>
                    </div>
                    <div class="mb-8">
                        <label class="block font-sans text-sm font-semibold text-creme mb-2"><?= $lang === 'fr' ? 'Message' : 'Message' ?></label>
                        <textarea name="message" required rows="4" class="w-full bg-charbon/50 border border-ardoise/50 rounded-xl px-4 py-3 text-creme focus:outline-none focus:border-or focus:ring-1 focus:ring-or transition-all resize-none" placeholder="<?= $lang === 'fr' ? 'Comment puis-je vous aider ?' : 'How can I help you?' ?>"></textarea>
                    </div>
                    <button type="submit" class="w-full bg-or text-charbon py-4 rounded-xl font-bold text-lg hover:bg-or/90 transition-colors shadow-[0_0_20px_rgba(212,168,67,0.2)] hover:shadow-[0_0_30px_rgba(212,168,67,0.4)]">
                        <?= $lang === 'fr' ? 'Envoyer le message' : 'Send Message' ?>
                    </button>
                    
                    <?php if(isset($_GET['sent']) && $_GET['sent'] === '1'): ?>
                    <div id="contact-success-msg" class="mt-4 p-4 bg-green-500/20 border border-green-500/50 text-green-200 rounded-xl text-center text-sm">
                        <?= $lang === 'fr' ? 'Votre message a été envoyé avec succès !' : 'Your message has been sent successfully!' ?>
                    </div>
                    <script>
                        setTimeout(() => {
                            const msg = document.getElementById('contact-success-msg');
                            if (msg) msg.style.opacity = '0';
                            setTimeout(() => {
                                if (msg) msg.style.display = 'none';
                            }, 500); // Wait for transition
                            
                            // Remove query param from URL
                            const url = new URL(window.location);
                            url.searchParams.delete('sent');
                            window.history.replaceState({}, '', url);
                        }, 4000);
                    </script>
                    <style>
                        #contact-success-msg {
                            transition: opacity 0.5s ease;
                        }
                    </style>
                    <?php endif; ?>
                    <?php if(isset($_GET['mail_error']) && $_GET['mail_error'] === '1'): ?>
                    <div class="mt-4 p-4 bg-red-500/20 border border-red-500/50 text-red-200 rounded-xl text-center text-sm">
                        <?= $lang === 'fr' ? 'Une erreur est survenue lors de l\'envoi du message.' : 'An error occurred while sending the message.' ?>
                    </div>
                    <?php endif; ?>
                </form>
            </div>
        </section>
    </main>

    <!-- H. FOOTER -->
    <footer class="bg-charbon py-8 md:py-12 px-5 md:px-6 rounded-t-[3rem] md:rounded-t-[4rem] border-t border-white/5 relative z-20 w-full overflow-hidden">
        <div class="max-w-6xl mx-auto flex flex-col md:flex-row justify-between items-center gap-6 w-full text-center md:text-left">
            <div class="font-sans font-bold text-xl text-creme break-words max-w-full">
                <?= htmlspecialchars($heroName) ?>.
            </div>
            <div class="text-creme/40 font-sans text-xs md:text-sm">
                &copy; <?= date('Y') ?> - <?= $lang === 'fr' ? 'Fait par Acherif Ahmat Alifa' : 'Made with vibe coding' ?>
            </div>
            <div class="flex items-center justify-center gap-2 bg-ardoise/50 px-4 py-2 rounded-full border border-white/10 w-max shrink-0">
                <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                <span class="font-mono text-[10px] md:text-xs text-creme/80 uppercase tracking-wider">En ligne</span>
            </div>
        </div>
    </footer>

    <!-- Image Modal -->
    <div id="imageModal" class="fixed inset-0 z-[100] bg-charbon/95 backdrop-blur-sm hidden flex-col justify-center items-center opacity-0 transition-opacity duration-300" onclick="closeImageModal()">
        <button class="absolute top-6 right-6 text-creme/60 hover:text-or transition-colors p-2" onclick="closeImageModal()">
            <i data-lucide="x" class="w-8 h-8"></i>
        </button>
        <img src="<?= htmlspecialchars($profileImage) ?>" class="max-w-[90vw] max-h-[85vh] object-contain rounded-2xl shadow-2xl scale-95 transition-transform duration-300" id="modalImage">
    </div>

    <!-- Initialize Icons -->
    <script>
        lucide.createIcons();
    </script>
    
    <!-- Custom Animations -->
    <script src="assets/js/public.js"></script>
    <script>
        function openImageModal() {
            const modal = document.getElementById('imageModal');
            const img = document.getElementById('modalImage');
            modal.classList.remove('hidden');
            modal.style.display = 'flex';
            // trigger reflow
            void modal.offsetWidth;
            modal.classList.remove('opacity-0');
            modal.classList.add('opacity-100');
            img.classList.remove('scale-95');
            img.classList.add('scale-100');
            document.body.style.overflow = 'hidden';
        }
        function closeImageModal() {
            const modal = document.getElementById('imageModal');
            const img = document.getElementById('modalImage');
            modal.classList.remove('opacity-100');
            modal.classList.add('opacity-0');
            img.classList.remove('scale-100');
            img.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.style.display = 'none';
                document.body.style.overflow = '';
            }, 300);
        }

        // Mobile Menu Logic
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const closeMenuBtn = document.getElementById('closeMenuBtn');
        const mobileMenu = document.getElementById('mobileMenu');
        const mobileLinks = document.querySelectorAll('.mobile-link');

        function openMobileMenu() {
            mobileMenu.classList.remove('pointer-events-none', 'opacity-0', 'scale-105');
            mobileMenu.classList.add('opacity-100', 'scale-100');
            document.body.style.overflow = 'hidden';
        }

        function closeMobileMenu() {
            mobileMenu.classList.remove('opacity-100', 'scale-100');
            mobileMenu.classList.add('opacity-0', 'scale-105', 'pointer-events-none');
            document.body.style.overflow = '';
        }

        mobileMenuBtn.addEventListener('click', openMobileMenu);
        closeMenuBtn.addEventListener('click', closeMobileMenu);

        mobileLinks.forEach(link => {
            link.addEventListener('click', closeMobileMenu);
        });
    </script>
</body>
</html>
