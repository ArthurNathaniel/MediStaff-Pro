<div class="navbar_all">
    <div class="logo"></div>
    <button id="toggleButton">
        <i class="fa-solid fa-bars-staggered"></i>
        
    </button>

    <div class="mobile">
    <?php 
  $current_page = basename($_SERVER['PHP_SELF']); 
?>

<a href="staff_dashboard.php" class="<?php echo ($current_page == 'staff_dashboard.php') ? 'active' : ''; ?>">
  <i class="fas fa-home"></i> Dashboard
</a>
<a href="weekly_shift.php" class="<?php echo ($current_page == 'weekly_shift.php') ? 'active' : ''; ?>">
<i class="fas fa-calendar-alt"></i> Weekly Shifts
</a>
<a href="logout.php" class="<?php echo ($current_page == 'logout.php') ? 'active' : ''; ?>">
  <i class="fas fa-sign-out-alt"></i> Logout
</a>



    </div>
   
</div>

<script>
        // Get the button and sidebar elements
        var toggleButton = document.getElementById("toggleButton");
    var sidebar = document.querySelector(".mobile");
    var icon = toggleButton.querySelector("i");

    // Add click event listener to the button
    toggleButton.addEventListener("click", function() {
        // Toggle the visibility of the sidebar
        if (sidebar.style.display === "none" || sidebar.style.display === "") {
            sidebar.style.display = "flex";
            sidebar.style.flexDirection = "column";
            icon.classList.remove("fa-bars-staggered");
            icon.classList.add("fa-xmark");
        } else {
            sidebar.style.display = "none";
            icon.classList.remove("fa-xmark");
            icon.classList.add("fa-bars-staggered");
        }
    });
</script>