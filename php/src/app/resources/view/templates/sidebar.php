<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    const sidebar = document.querySelector('.sidebar');
    if (sidebarToggle && sidebar) sidebarToggle.addEventListener('click', () => sidebar.classList.toggle('active'));

    function applySort() {
        const sortOrder = document.getElementById("sortOrder").value;
        const urlParams = new URLSearchParams(window.location.search);
        const currentView = urlParams.get("view") || "pessoal";
        urlParams.set("order", sortOrder);
        urlParams.set("view", currentView);
        window.location.search = urlParams.toString();
    }

    window.applySort = applySort;
});