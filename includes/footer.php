<?php
// ============================================
// Shared Footer for Client Pages
// No Twitter/Instagram links
// ============================================
?>
<footer class="footer" id="footer">
    <div class="footer-left">
        <h3>Wasla</h3>
        <p>&copy; 2024 WASLA DIGITAL CONDUIT. ALL RIGHTS RESERVED.</p>
    </div>
    <div class="footer-links">
        <?php $base_path = (strpos($_SERVER['PHP_SELF'], '/usher/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? '../' : './'; ?>
        <a href="<?php echo $base_path; ?>terms.php">TERMS OF SERVICE</a>
        <a href="<?php echo $base_path; ?>privacy.php">PRIVACY POLICY</a>
        <a href="<?php echo $base_path; ?>contact.php">CONTACT US</a>
    </div>
</footer>
