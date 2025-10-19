<?php
/**
 * Clean dispatch_logs.php
 * No inline CSS/JS - everything in external files
 */

// [Keep all your original PHP code exactly as it was]

// After your existing PHP code, replace only the HTML output (remove all <style> and <script> tags):
?>

<div class="row">
    <div class="col-12">
        <div class="page-header">
            <h2 class="mb-2">Dispatch Operations</h2>
            <p class="text-muted">Manage and track emergency vehicle dispatches in real-time</p>
        </div>
    </div>
</div>

<!-- Enhanced Filter and Search Bar -->
<div class="filter-bar no-print">
    <div class="row g-3 align-items-end">
        <div class="col-md-3">
            <label class="form-label">Status Filter</label>
            <select class="form-select" id="statusFilter">
                <option value="">All Statuses</option>
                <option value="assigned">Assigned</option>
                <option value="en_route">En Route</option>
                <option value="on_scene">On Scene</option>
                <option value="returning">Returning</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Date Range</label>
            <input type="date" class="form-control" id="dateFilter">
        </div>
        <div class="col-md-3">
            <label class="form-label">Trip Ticket Status</label>
            <select class="form-select" id="ticketFilter">
                <option value="">All Tickets</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
            </select>
        </div>
        <div class="col-md-3">
            <div class="d-grid">
                <button class="btn btn-outline-secondary" onclick="dispatchSystem.clearFilters()">
                    <i class="fas fa-times"></i> Clear Filters
                </button>
            </div>
        </div>
    </div>
</div>

<!-- [Rest of your dispatch logs HTML content remains exactly the same, just remove any <style> or <script> tags] -->

<!-- Hidden forms for actions -->
<form id="deleteDispatchForm" method="POST" action="?page=dispatch" style="display: none;">
    <input type="hidden" name="delete_dispatch" value="1">
    <input type="hidden" name="dispatch_id" id="deleteDispatchId">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(get_csrf_token()) ?>">
</form>

<form id="approveTicketForm" method="POST" action="?page=dispatch" style="display: none;">
    <input type="hidden" name="approve_ticket" value="1">
    <input type="hidden" name="dispatch_id" id="approveTicketId">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(get_csrf_token()) ?>">
</form>

<form id="rejectTicketForm" method="POST" action="?page=dispatch" style="display: none;">
    <input type="hidden" name="reject_ticket" value="1">
    <input type="hidden" name="dispatch_id" id="rejectTicketId">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(get_csrf_token()) ?>">
</form>

<?php include 'footer.php'; ?>