<footer class="admin-footer">
    <div class="footer-content">
        <p>&copy; <?php echo date('Y'); ?> Dar Al-'Ulum Montréal. All rights reserved.</p>
    </div>
</footer>

<!-- Common JavaScript -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script>
    // Common functionality
    $(document).ready(function() {
        // Sidebar toggle
        $('.menu-toggle').click(function() {
            $('.sidebar').toggleClass('active');
        });

        // Tooltips
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>