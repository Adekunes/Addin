class EnhancedSidebar {
    constructor() {
        this.sidebar = document.getElementById('mainSidebar');
        this.currentPath = window.location.pathname;
        this.init();
    }

    async init() {
        await this.loadUserData();
        this.setupEventListeners();
        this.setupTheme();
        this.initializeSubmenus();
        this.highlightCurrentPage();
        this.setupNotifications();
    }

    async loadUserData() {
        try {
            const response = await fetch('/model/auth/get_user_data.php');
            const userData = await response.json();
            
            document.getElementById('userName').textContent = userData.name;
            document.getElementById('userRole').textContent = userData.role;
            
            if (userData.avatar) {
                document.getElementById('userAvatar').src = userData.avatar;
            }

            // Show/hide menus based on role
            this.setupMenuVisibility(userData.role);
        } catch (error) {
            console.error('Error loading user data:', error);
        }
    }

    setupMenuVisibility(role) {
        const menuSections = document.querySelectorAll('.menu-section');
        menuSections.forEach(section => {
            section.style.display = section.dataset.role === role ? 'block' : 'none';
        });
    }

    setupEventListeners() {
        // Toggle sidebar
        const toggleBtn = document.getElementById('sidebarToggle');
        if (toggleBtn) {
            toggleBtn.addEventListener('click', () => this.toggleSidebar());
        }

        // Theme toggle
        const themeToggle = document.getElementById('themeToggle');
        if (themeToggle) {
            themeToggle.addEventListener('click', (e) => {
                e.preventDefault();
                this.toggleTheme();
            });
        }

        // Logout handling
        const logoutBtn = document.getElementById('logoutBtn');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.handleLogout();
            });
        }

        // Handle submenu toggles
        const submenuTriggers = document.querySelectorAll('.has-submenu > a');
        submenuTriggers.forEach(trigger => {
            trigger.addEventListener('click', (e) => {
                e.preventDefault();
                this.toggleSubmenu(trigger);
            });
        });

        // Handle window resize
        window.addEventListener('resize', () => {
            if (window.innerWidth > 768) {
                this.sidebar.classList.remove('active');
                document.body.classList.remove('sidebar-open');
            }
        });

        // Handle clicks outside sidebar on mobile
        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 768 && 
                !this.sidebar.contains(e.target) && 
                !e.target.matches('#sidebarToggle')) {
                this.sidebar.classList.remove('active');
                document.body.classList.remove('sidebar-open');
            }
        });
    }

    setupTheme() {
        const savedTheme = localStorage.getItem('sidebarTheme') || 'dark';
        this.applyTheme(savedTheme);
    }

    applyTheme(theme) {
        this.sidebar.dataset.theme = theme;
        localStorage.setItem('sidebarTheme', theme);
        
        const themeIcon = document.querySelector('#themeToggle i');
        if (themeIcon) {
            themeIcon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
        }
    }

    toggleTheme() {
        const currentTheme = this.sidebar.dataset.theme;
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        this.applyTheme(newTheme);
    }

    initializeSubmenus() {
        const currentPage = this.currentPath;
        const activeSubmenu = document.querySelector(`a[href="${currentPage}"]`)?.closest('.has-submenu');
        if (activeSubmenu) {
            activeSubmenu.classList.add('active');
        }
    }

    toggleSubmenu(trigger) {
        const submenuParent = trigger.parentElement;
        const wasActive = submenuParent.classList.contains('active');
        
        // Close all open submenus first
        const allSubmenus = document.querySelectorAll('.has-submenu');
        allSubmenus.forEach(menu => menu.classList.remove('active'));
        
        // If this submenu wasn't active before, open it
        if (!wasActive) {
            submenuParent.classList.add('active');
        }
    }

    highlightCurrentPage() {
        const links = this.sidebar.querySelectorAll('a');
        links.forEach(link => {
            const href = link.getAttribute('href');
            if (href === this.currentPath || this.currentPath.includes(href)) {
                link.classList.add('active');
                // Expand parent submenu if exists
                const submenuParent = link.closest('.has-submenu');
                if (submenuParent) {
                    submenuParent.classList.add('active');
                }
            }
        });
    }

    async setupNotifications() {
        try {
            const response = await fetch('/model/notifications/get_count.php');
            const data = await response.json();
            
            const badge = document.getElementById('dashboardNotifications');
            if (badge && data.count > 0) {
                badge.textContent = data.count;
                badge.style.display = 'inline';
            }
        } catch (error) {
            console.error('Error loading notifications:', error);
        }
    }

    toggleSidebar() {
        this.sidebar.classList.toggle('active');
        document.body.classList.toggle('sidebar-open');
    }

    async handleLogout() {
        if (confirm('Are you sure you want to logout?')) {
            try {
                const response = await fetch('/model/auth/logout.php');
                const data = await response.json();
                if (data.success) {
                    window.location.href = '/login.php';
                } else {
                    alert('Logout failed. Please try again.');
                }
            } catch (error) {
                console.error('Logout error:', error);
                alert('An error occurred during logout.');
            }
        }
    }

    updateBreadcrumb() {
        const breadcrumb = document.getElementById('breadcrumb');
        if (breadcrumb) {
            const pathParts = this.currentPath.split('/').filter(Boolean);
            let breadcrumbHTML = '<li><a href="/"><i class="fas fa-home"></i></a></li>';
            
            pathParts.forEach((part, index) => {
                const formattedPart = part.replace(/-/g, ' ').replace('.html', '');
                if (index === pathParts.length - 1) {
                    breadcrumbHTML += `<li class="active">${formattedPart}</li>`;
                } else {
                    const path = '/' + pathParts.slice(0, index + 1).join('/');
                    breadcrumbHTML += `<li><a href="${path}">${formattedPart}</a></li>`;
                }
            });
            
            breadcrumb.innerHTML = breadcrumbHTML;
        }
    }

    // Static initialization
    static init() {
        return new EnhancedSidebar();
    }
}

// Initialize sidebar when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.sidebar = EnhancedSidebar.init();
});