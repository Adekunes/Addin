class EnhancedSidebar {
    constructor() {
        this.init();
    }

    async init() {
        await this.loadUserData();
        this.setupSidebarToggle();
        this.setupActiveLinks();
        await this.setupNotifications();
    }

    async loadUserData() {
        try {
            const response = await fetch('./model/db_requests/get_user_data.php');
            if (!response.ok) throw new Error('Network response was not ok');
            const data = await response.json();
            

            // Update user info in sidebar
            const userNameElement = document.querySelector('.user span');
            const userAvatarElement = document.querySelector('.user .avatar');
            
            if (userNameElement) userNameElement.textContent = data.name;
            if (userAvatarElement) userAvatarElement.src = data.avatar;
            
        } catch (error) {
            console.error('Error loading user data:', error);
        }
    }

    setupSidebarToggle() {
        const toggleBtn = document.querySelector('.sidebar-toggle');
        const sidebar = document.getElementById('mainSidebar');
        
        if (toggleBtn && sidebar) {
            toggleBtn.addEventListener('click', () => {
                sidebar.classList.toggle('collapsed');
            });
        }
    }

    setupActiveLinks() {
        const currentPath = window.location.pathname;
        const links = document.querySelectorAll('.nav-item');
        
        links.forEach(link => {
            if (currentPath.includes(link.getAttribute('data-page'))) {
                link.classList.add('active');
            }
        });
    }

    async setupNotifications() {
        try {
            const response = await fetch('/Source_folder_final_v2/model/notifications/get_count.php');
            if (!response.ok) throw new Error('Network response was not ok');
            const data = await response.json();
            
            const badge = document.querySelector('.notifications .badge');
            if (badge && data.count > 0) {
                badge.textContent = data.count;
                badge.style.display = 'block';
            }
        } catch (error) {
            console.error('Error loading notifications:', error);
        }
    }
}

// Initialize the sidebar
document.addEventListener('DOMContentLoaded', () => {
    new EnhancedSidebar();
});