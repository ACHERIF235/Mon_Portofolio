document.addEventListener('DOMContentLoaded', () => {
    // Register ScrollTrigger
    gsap.registerPlugin(ScrollTrigger);

    const ctx = gsap.context(() => {
        
        // 1. Navbar Morphing (Intersection Observer + GSAP)
        const navbar = document.getElementById('navbar');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                navbar.classList.add('bg-charbon/80', 'backdrop-blur-xl', 'border-white/10', 'shadow-2xl');
                navbar.classList.remove('bg-transparent', 'border-transparent');
            } else {
                navbar.classList.remove('bg-charbon/80', 'backdrop-blur-xl', 'border-white/10', 'shadow-2xl');
                navbar.classList.add('bg-transparent', 'border-transparent');
            }
        });

        // 2. Magnetic Buttons (Effet fluide de suivi de la souris)
        const magnets = document.querySelectorAll('.magnet-btn');
        magnets.forEach(btn => {
            btn.addEventListener('mousemove', (e) => {
                const rect = btn.getBoundingClientRect();
                const x = e.clientX - rect.left - rect.width / 2;
                const y = e.clientY - rect.top - rect.height / 2;
                
                gsap.to(btn, {
                    x: x * 0.4,
                    y: y * 0.4,
                    duration: 0.6,
                    ease: 'power3.out'
                });
            });
            btn.addEventListener('mouseleave', () => {
                gsap.to(btn, {
                    x: 0,
                    y: 0,
                    duration: 0.8,
                    ease: 'elastic.out(1, 0.3)'
                });
            });
        });

        // 3. Hero Section Animations (Entrée spectaculaire)
        gsap.fromTo('.hero-item', 
            { y: 50, opacity: 0, scale: 0.95 },
            { y: 0, opacity: 1, scale: 1, duration: 1.4, stagger: 0.15, ease: 'expo.out', delay: 0.2 }
        );

        // 4. Hero Parallax Scroll (Effet cinématique au défilement)
        gsap.to('.hero-content', {
            y: 120,
            opacity: 0,
            scale: 0.9,
            ease: 'none',
            scrollTrigger: {
                trigger: '.hero-content',
                start: 'top 30%',
                end: 'bottom top',
                scrub: true
            }
        });

        // 5. About Section (Manifeste)
        gsap.fromTo('.about-title',
            { x: -50, opacity: 0, skewX: 5 },
            { 
                x: 0, opacity: 1, skewX: 0, duration: 1.2, ease: 'expo.out',
                scrollTrigger: {
                    trigger: '#about',
                    start: 'top 75%'
                }
            }
        );
        
        gsap.fromTo('.about-line',
            { scaleY: 0, transformOrigin: 'top' },
            { 
                scaleY: 1, duration: 1.5, ease: 'power3.inOut',
                scrollTrigger: {
                    trigger: '#about',
                    start: 'top 65%'
                }
            }
        );

        gsap.fromTo('.about-text',
            { y: 40, opacity: 0 },
            { 
                y: 0, opacity: 1, duration: 1.2, ease: 'expo.out',
                scrollTrigger: {
                    trigger: '#about',
                    start: 'top 65%'
                }
            }
        );

        // 6. Experience Cards (Timeline avec effet dynamique 3D)
        const expCards = gsap.utils.toArray('.exp-wrapper');
        expCards.forEach((wrapper, index) => {
            const isEven = index % 2 === 0;
            const card = wrapper.querySelector('.exp-card');
            const dot = wrapper.querySelector('.dot-pulse');
            
            gsap.fromTo(card,
                { x: isEven ? -80 : 80, opacity: 0, rotationY: isEven ? -10 : 10 },
                { 
                    x: 0, opacity: 1, rotationY: 0, duration: 1.2, ease: 'expo.out',
                    scrollTrigger: {
                        trigger: wrapper,
                        start: 'top 80%'
                    }
                }
            );

            if(dot) {
                gsap.fromTo(dot,
                    { scale: 0, opacity: 0 },
                    {
                        scale: 1, opacity: 1, duration: 0.6, ease: 'back.out(2)',
                        scrollTrigger: {
                            trigger: wrapper,
                            start: 'top 80%'
                        }
                    }
                );
            }
        });

        // 7. Skills Section (Tags pop avec rebond dynamique)
        gsap.fromTo('.skill-tag',
            { scale: 0.5, opacity: 0, y: 20 },
            { 
                scale: 1, opacity: 1, y: 0, duration: 0.8, stagger: 0.05, ease: 'back.out(1.8)',
                scrollTrigger: {
                    trigger: '#skills',
                    start: 'top 80%'
                }
            }
        );

        // 8. Education Cards (Glissement vertical)
        gsap.fromTo('.edu-card',
            { y: 40, opacity: 0, scale: 0.98 },
            { 
                y: 0, opacity: 1, scale: 1, duration: 1, stagger: 0.15, ease: 'power3.out',
                scrollTrigger: {
                    trigger: '.edu-card',
                    start: 'top 85%'
                }
            }
        );

        // 9. Contact Links (Apparition décalée)
        gsap.fromTo('.contact-links a',
            { y: 30, opacity: 0 },
            { 
                y: 0, opacity: 1, duration: 0.8, stagger: 0.1, ease: 'expo.out',
                scrollTrigger: {
                    trigger: '#contact',
                    start: 'top 80%'
                }
            }
        );

    }, document.body);

    window.addEventListener('unload', () => ctx.revert());
});
