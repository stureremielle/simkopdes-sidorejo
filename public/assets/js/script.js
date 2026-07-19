/*
========================================================================
   SIMKOPDES REPLICA JAVASCRIPT
   Description: Sticky Header Scroll Effect, Mobile Menu Toggle, 
                and Registration Modal Handlers
========================================================================
*/

document.addEventListener('DOMContentLoaded', () => {

    // === 1. Navbar Scroll Transformation ===
    const header = document.getElementById('navbar');
    
    const handleScroll = () => {
        if (window.scrollY > 30) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    };
    
    window.addEventListener('scroll', handleScroll);
    handleScroll(); // Check scroll position immediately on page load

    // === 2. Mobile Menu Toggle ===
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');
    const navLinks = document.querySelectorAll('.mobile-menu-overlay .nav-link, .mobile-menu-overlay .btn-join');

    const toggleMenu = () => {
        mobileMenuBtn.classList.toggle('active');
        mobileMenuOverlay.classList.toggle('open');
        // Disable body scroll when menu is active
        document.body.style.overflow = mobileMenuOverlay.classList.contains('open') ? 'hidden' : '';
    };

    if (mobileMenuBtn && mobileMenuOverlay) {
        mobileMenuBtn.addEventListener('click', toggleMenu);
    }

    // Close mobile menu overlay on clicking menu links
    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            if (mobileMenuOverlay.classList.contains('open')) {
                toggleMenu();
            }
        });
    });

    // === 3. Modal Windows Action ===
    const registerModal = document.getElementById('registerModal');
    const closeRegisterModal = document.getElementById('closeRegisterModal');
    const openRegisterBtns = document.querySelectorAll('.open-register-modal');

    const openModal = () => {
        if (registerModal) {
            registerModal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    };

    const closeModal = () => {
        if (registerModal) {
            registerModal.classList.remove('active');
            document.body.style.overflow = '';
        }
    };

    openRegisterBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            openModal();
        });
    });

    if (closeRegisterModal) {
        closeRegisterModal.addEventListener('click', closeModal);
    }

    // Close when overlay clicked
    if (registerModal) {
        registerModal.addEventListener('click', (e) => {
            if (e.target === registerModal) {
                closeModal();
            }
        });
    }

    // Close when Esc key pressed
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && registerModal && registerModal.classList.contains('active')) {
            closeModal();
        }
    });

    // Register Form Action Simulation
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const name = document.getElementById('regName').value;
            alert(`Terima kasih ${name}, pengajuan pendaftaran Anda telah dikirim! Pengurus Koperasi Desa Merah Putih akan segera memproses data Anda.`);
            registerForm.reset();
            closeModal();
        });
    }

    // Dedicated Full-Page Register Form Simulation
    const memberRegisterForm = document.getElementById('memberRegisterForm');
    if (memberRegisterForm) {
        memberRegisterForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const name = document.getElementById('namaLengkap').value;
            alert(`Terima kasih ${name}, pendaftaran anggota Koperasi Simkopdes Anda telah berhasil dikirim! Pengurus koperasi akan segera memverifikasi data Anda.`);
            memberRegisterForm.reset();
        });
    }
});
