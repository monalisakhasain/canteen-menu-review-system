        </div><!-- /admin-content -->
    </div><!-- /admin-main -->
</div><!-- /admin-layout -->

<script src="/canteen_project/js/script.js"></script>
<script>
// Sidebar mobile toggle
document.getElementById('adminMenuBtn')?.addEventListener('click', function() {
    document.getElementById('adminSidebar').classList.toggle('open');
});
// Close sidebar on outside click (mobile)
document.addEventListener('click', function(e) {
    var sb = document.getElementById('adminSidebar');
    var btn = document.getElementById('adminMenuBtn');
    if (sb && sb.classList.contains('open') && !sb.contains(e.target) && !btn.contains(e.target)) {
        sb.classList.remove('open');
    }
});
</script>
</body>
</html>
