<?php
/**
 * Clean Common footer layout
 * No inline CSS/JS - everything in external files
 */
?>
    </div> <!-- Close main-content -->
    
    <!-- Enhanced Footer -->
    <footer class="no-print">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="footer-brand">
                        <i class="fas fa-shield-alt me-2"></i>
                        <strong>CDRRMO Dispatch Management System</strong>
                    </div>
                    <div class="footer-info">
                        <small class="text-muted">
                            City Disaster Risk Reduction and Management Office • Maasin City, Southern Leyte
                        </small>
                    </div>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="footer-meta">
                        <small class="text-muted">
                            <i class="fas fa-code me-1"></i>Developed by IT Capstone Team • 
                            <i class="fas fa-calendar me-1"></i>Version 2.0 • 
                            <i class="fas fa-clock me-1"></i>Last Updated: <?php echo date('M d, Y'); ?>
                        </small>
                    </div>
                    <div class="footer-stats">
                        <small class="text-muted">
                            <span id="footer-session-time">Session: 0m</span> • 
                            <span id="footer-memory-usage">Memory: 0MB</span>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Enhanced JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Custom Enhanced JavaScript -->
    <script src="jv.js"></script>
    
    <!-- Enhanced Toast System -->
    <div class="position-fixed top-0 end-0 p-3" style="z-index:9999">
        <div id="enhancedToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <i class="fas fa-circle me-2 toast-icon"></i>
                <strong class="me-auto toast-title">System Notification</strong>
                <small class="text-muted toast-time">just now</small>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">
                Operation completed successfully.
            </div>
        </div>
    </div>

    <!-- Enhanced Loading Overlay -->
    <div class="loading-overlay-global" style="display: none;"></div>
</body>
</html>