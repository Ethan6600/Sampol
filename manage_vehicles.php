<?php
/**
 * Enhanced manage_vehicles.php
 * Clean, organized with external CSS/JS
 */

// [Keep all your original PHP code exactly as it was]
?>

<div class="row">
    <div class="col-12">
        <div class="page-header">
            <h2 class="mb-2">Vehicle Fleet Management</h2>
            <p class="text-muted">Manage emergency response vehicles and their availability</p>
        </div>
    </div>
</div>

<!-- Vehicle Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body text-center">
                <i class="fas fa-car-side fa-2x text-success mb-2"></i>
                <h4 class="mb-0" id="availableCount">0</h4>
                <small class="text-muted">Available</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body text-center">
                <i class="fas fa-truck-moving fa-2x text-warning mb-2"></i>
                <h4 class="mb-0" id="dispatchedCount">0</h4>
                <small class="text-muted">Dispatched</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body text-center">
                <i class="fas fa-tools fa-2x text-info mb-2"></i>
                <h4 class="mb-0" id="maintenanceCount">0</h4>
                <small class="text-muted">Maintenance</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body text-center">
                <i class="fas fa-ban fa-2x text-danger mb-2"></i>
                <h4 class="mb-0" id="outOfServiceCount">0</h4>
                <small class="text-muted">Out of Service</small>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4 shadow-soft rounded-3">
    <div class="card-header bg-primary text-white rounded-top d-flex justify-content-between align-items-center">
        <div>
            <i class="fas fa-plus-circle me-2"></i>
            <?= $edit_vehicle_data ? 'Edit Vehicle Details' : 'Register New Vehicle' ?>
        </div>
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="quickAddMode">
            <label class="form-check-label text-white" for="quickAddMode">Quick Add</label>
        </div>
    </div>
    <div class="card-body">
        <form method="POST" action="?page=vehicles" novalidate class="needs-validation" id="vehicleForm">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(get_csrf_token()) ?>">
            <?php if ($edit_vehicle_data): ?>
                <input type="hidden" name="vehicle_id" value="<?= htmlspecialchars($edit_vehicle_data['vehicle_id']) ?>">
            <?php endif; ?>

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="name" class="form-label">
                        <i class="fas fa-car me-1 text-primary"></i>
                        Vehicle Name <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control" id="name" name="name" 
                           placeholder="e.g., Fire Truck 001" 
                           value="<?= htmlspecialchars($edit_vehicle_data['name'] ?? '') ?>" required>
                    <div class="invalid-feedback">Please provide a vehicle name.</div>
                </div>

                <div class="col-md-6">
                    <label for="plate_number" class="form-label">
                        <i class="fas fa-tag me-1 text-info"></i>
                        Plate Number <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control" id="plate_number" name="plate_number" 
                           placeholder="e.g., FT-001" 
                           value="<?= htmlspecialchars($edit_vehicle_data['plate_number'] ?? '') ?>" required>
                    <div class="invalid-feedback">Please provide a plate number.</div>
                </div>

                <div class="col-md-6">
                    <label for="vehicle_type" class="form-label">
                        <i class="fas fa-cog me-1 text-warning"></i>
                        Vehicle Type <span class="text-danger">*</span>
                    </label>
                    <select class="form-select" id="vehicle_type" name="vehicle_type" required>
                        <option value="">Select Vehicle Type</option>
                        <option value="fire_truck" <?= ($edit_vehicle_data['vehicle_type'] ?? '') === 'fire_truck' ? 'selected' : '' ?>>🚒 Fire Truck</option>
                        <option value="ambulance" <?= ($edit_vehicle_data['vehicle_type'] ?? '') === 'ambulance' ? 'selected' : '' ?>>🚑 Ambulance</option>
                        <option value="rescue_vehicle" <?= ($edit_vehicle_data['vehicle_type'] ?? '') === 'rescue_vehicle' ? 'selected' : '' ?>>🚛 Rescue Vehicle</option>
                        <option value="water_tanker" <?= ($edit_vehicle_data['vehicle_type'] ?? '') === 'water_tanker' ? 'selected' : '' ?>>🚛 Water Tanker</option>
                        <option value="other" <?= ($edit_vehicle_data['vehicle_type'] ?? '') === 'other' ? 'selected' : '' ?>>📦 Other</option>
                    </select>
                    <div class="invalid-feedback">Please select a vehicle type.</div>
                </div>

                <div class="col-md-6">
                    <label for="status" class="form-label">
                        <i class="fas fa-traffic-light me-1 text-success"></i>
                        Current Status <span class="text-danger">*</span>
                    </label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="available" <?= ($edit_vehicle_data['status'] ?? '') === 'available' ? 'selected' : '' ?>>🟢 Available</option>
                        <option value="dispatched" <?= ($edit_vehicle_data['status'] ?? '') === 'dispatched' ? 'selected' : '' ?>>🟡 Dispatched</option>
                        <option value="maintenance" <?= ($edit_vehicle_data['status'] ?? '') === 'maintenance' ? 'selected' : '' ?>>🔧 Maintenance</option>
                        <option value="out_of_service" <?= ($edit_vehicle_data['status'] ?? '') === 'out_of_service' ? 'selected' : '' ?>>🔴 Out of Service</option>
                    </select>
                </div>

                <div class="col-12">
                    <label for="current_location" class="form-label">
                        <i class="fas fa-map-marker-alt me-1 text-danger"></i>
                        Current Location
                    </label>
                    <input type="text" class="form-control" id="current_location" name="current_location" 
                           placeholder="e.g., Fire Station, Health Center, CDRRMO Office" 
                           value="<?= htmlspecialchars($edit_vehicle_data['current_location'] ?? '') ?>">
                    <div class="form-text">Leave blank if vehicle is dispatched or in transit</div>
                </div>
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                <?php if ($edit_vehicle_data): ?>
                    <button type="submit" name="edit_vehicle" class="btn btn-success btn-lg">
                        <i class="fas fa-save me-1"></i> Update Vehicle
                    </button>
                    <a href="?page=vehicles" class="btn btn-secondary btn-lg">
                        <i class="fas fa-undo me-1"></i> Cancel Edit
                    </a>
                <?php else: ?>
                    <button type="submit" name="add_vehicle" class="btn btn-primary btn-lg">
                        <i class="fas fa-plus-circle me-1"></i> Register Vehicle
                    </button>
                    <button type="reset" class="btn btn-outline-secondary btn-lg">
                        <i class="fas fa-eraser me-1"></i> Clear Form
                    </button>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Vehicle Fleet Table -->
<div class="card shadow-soft rounded-3">
    <div class="card-header bg-info text-white rounded-top d-flex justify-content-between align-items-center">
        <div>
            <i class="fas fa-list me-2"></i>
            Vehicle Fleet Inventory
            <span class="badge bg-light text-dark ms-2" id="totalVehicles"><?= count($vehicles) ?></span>
        </div>
        <div class="export-controls">
            <button class="btn btn-sm btn-light me-2" onclick="dispatchSystem.exportTableToCSV('vehiclesTable', 'vehicles.csv')">
                <i class="fas fa-download me-1"></i> Export
            </button>
            <button class="btn btn-sm btn-light" onclick="window.print()">
                <i class="fas fa-print me-1"></i> Print
            </button>
        </div>
    </div>
    <div class="card-body">
        <?php if (count($vehicles) > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle" id="vehiclesTable">
                    <thead>
                        <tr>
                            <th width="30">
                                <input type="checkbox" class="form-check-input select-all" id="selectAllVehicles">
                            </th>
                            <th>Vehicle Details</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Location</th>
                            <th>Last Updated</th>
                            <th width="120" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($vehicles as $vehicle): ?>
                            <tr class="vehicle-row" data-status="<?= htmlspecialchars($vehicle['status']) ?>">
                                <td>
                                    <input type="checkbox" class="form-check-input item-select" value="<?= htmlspecialchars($vehicle['vehicle_id']) ?>">
                                </td>
                                <td>
                                    <div class="vehicle-info">
                                        <div class="fw-bold text-primary"><?= htmlspecialchars($vehicle['name']) ?></div>
                                        <div class="text-muted small">
                                            <i class="fas fa-tag me-1"></i>
                                            <?= htmlspecialchars($vehicle['plate_number']) ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info text-dark">
                                        <i class="fas 
                                            <?php
                                                switch ($vehicle['vehicle_type']) {
                                                    case 'fire_truck': echo 'fa-fire'; break;
                                                    case 'ambulance': echo 'fa-ambulance'; break;
                                                    case 'rescue_vehicle': echo 'fa-truck-pickup'; break;
                                                    case 'water_tanker': echo 'fa-truck-moving'; break;
                                                    default: echo 'fa-car'; break;
                                                }
                                            ?> me-1">
                                        </i>
                                        <?= htmlspecialchars(ucwords(str_replace('_', ' ', $vehicle['vehicle_type']))) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge 
                                        <?php
                                            switch ($vehicle['status']) {
                                                case 'available': echo 'bg-success'; break;
                                                case 'dispatched': echo 'bg-warning text-dark'; break;
                                                case 'maintenance': echo 'bg-info text-dark'; break;
                                                case 'out_of_service': echo 'bg-danger'; break;
                                                default: echo 'bg-secondary'; break;
                                            }
                                        ?>">
                                        <i class="fas 
                                            <?php
                                                switch ($vehicle['status']) {
                                                    case 'available': echo 'fa-check-circle'; break;
                                                    case 'dispatched': echo 'fa-truck-moving'; break;
                                                    case 'maintenance': echo 'fa-tools'; break;
                                                    case 'out_of_service': echo 'fa-ban'; break;
                                                    default: echo 'fa-question'; break;
                                                }
                                            ?> me-1">
                                        </i>
                                        <?= htmlspecialchars(ucwords(str_replace('_', ' ', $vehicle['status']))) ?>
                                    </span>
                                    
                                    <!-- Quick Status Update -->
                                    <div class="mt-1">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-outline-success btn-sm quick-status-btn" 
                                                    data-id="<?= htmlspecialchars($vehicle['vehicle_id']) ?>" 
                                                    data-status="available"
                                                    title="Mark as Available">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-warning btn-sm quick-status-btn"
                                                    data-id="<?= htmlspecialchars($vehicle['vehicle_id']) ?>" 
                                                    data-status="dispatched"
                                                    title="Mark as Dispatched">
                                                <i class="fas fa-truck-moving"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-info btn-sm quick-status-btn"
                                                    data-id="<?= htmlspecialchars($vehicle['vehicle_id']) ?>" 
                                                    data-status="maintenance"
                                                    title="Mark for Maintenance">
                                                <i class="fas fa-tools"></i>
                                            </button>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($vehicle['current_location']): ?>
                                        <div class="location-info">
                                            <i class="fas fa-map-marker-alt text-danger me-1"></i>
                                            <?= htmlspecialchars($vehicle['current_location']) ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted">Not specified</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="text-muted small">
                                        <?= htmlspecialchars(formatDate($vehicle['last_updated'])) ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="?page=vehicles&action=edit&id=<?= htmlspecialchars($vehicle['vehicle_id']) ?>" 
                                           class="btn btn-sm btn-warning me-1" title="Edit Vehicle">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger delete-vehicle-btn" 
                                                data-id="<?= htmlspecialchars($vehicle['vehicle_id']) ?>" 
                                                data-name="<?= htmlspecialchars($vehicle['name']) ?>"
                                                title="Delete Vehicle">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Table Summary -->
            <div class="row mt-3">
                <div class="col-md-6">
                    <div class="text-muted small" id="vehicleSummary">
                        Showing <?= count($vehicles) ?> vehicles in fleet
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <nav aria-label="Vehicle pagination">
                        <ul class="pagination pagination-sm justify-content-end mb-0">
                            <li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>
                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">Next</a></li>
                        </ul>
                    </nav>
                </div>
            </div>
            
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-car-side fa-3x text-muted mb-3"></i>
                <h5>No Vehicles Registered</h5>
                <p class="text-muted">Your vehicle fleet is empty. Register your first emergency response vehicle to get started.</p>
                <a href="?page=vehicles" class="btn btn-primary">
                    <i class="fas fa-plus-circle me-1"></i> Register First Vehicle
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Vehicle-specific JavaScript
document.addEventListener('DOMContentLoaded', function() {
    initializeVehicleStatistics();
    initializeVehicleFilters();
    initializeQuickStatusUpdates();
    setupVehicleFormValidation();
    
    // Quick add mode toggle
    const quickAddToggle = document.getElementById('quickAddMode');
    if (quickAddToggle) {
        quickAddToggle.addEventListener('change', function() {
            if (this.checked) {
                enableQuickAddMode();
            } else {
                disableQuickAddMode();
            }
        });
    }
});

function initializeVehicleStatistics() {
    const vehicles = <?= json_encode($vehicles) ?>;
    let available = 0, dispatched = 0, maintenance = 0, outOfService = 0;
    
    vehicles.forEach(vehicle => {
        switch(vehicle.status) {
            case 'available': available++; break;
            case 'dispatched': dispatched++; break;
            case 'maintenance': maintenance++; break;
            case 'out_of_service': outOfService++; break;
        }
    });
    
    document.getElementById('availableCount').textContent = available;
    document.getElementById('dispatchedCount').textContent = dispatched;
    document.getElementById('maintenanceCount').textContent = maintenance;
    document.getElementById('outOfServiceCount').textContent = outOfService;
}

function initializeVehicleFilters() {
    const statusFilter = document.getElementById('statusFilter');
    if (statusFilter) {
        statusFilter.addEventListener('change', filterVehicles);
    }
}

function filterVehicles() {
    const status = document.getElementById('statusFilter')?.value || '';
    const rows = document.querySelectorAll('.vehicle-row');
    let visibleCount = 0;
    
    rows.forEach(row => {
        const showRow = !status || row.dataset.status === status;
        row.style.display = showRow ? '' : 'none';
        if (showRow) visibleCount++;
    });
    
    document.getElementById('totalVehicles').textContent = visibleCount;
    document.getElementById('vehicleSummary').textContent = `Showing ${visibleCount} of ${rows.length} vehicles`;
}

function initializeQuickStatusUpdates() {
    document.querySelectorAll('.quick-status-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const vehicleId = this.dataset.id;
            const newStatus = this.dataset.status;
            const vehicleName = this.closest('tr').querySelector('.fw-bold').textContent;
            
            if (confirm(`Change ${vehicleName} status to "${newStatus.replace('_', ' ')}"?`)) {
                updateVehicleStatus(vehicleId, newStatus);
            }
        });
    });
}

function updateVehicleStatus(vehicleId, newStatus) {
    dispatchSystem.showLoading('Updating vehicle status...');
    
    // Simulate API call - in real implementation, this would be an AJAX call
    setTimeout(() => {
        dispatchSystem.hideLoading();
        dispatchSystem.showNotification(`Vehicle status updated to ${newStatus.replace('_', ' ')}`, 'success');
        
        // Update UI
        const row = document.querySelector(`.quick-status-btn[data-id="${vehicleId}"]`).closest('tr');
        const statusBadge = row.querySelector('.status-badge');
        
        if (statusBadge) {
            statusBadge.textContent = newStatus.replace('_', ' ');
            statusBadge.className = `status-badge bg-${getVehicleStatusColor(newStatus)}`;
            
            // Update row data attribute
            row.dataset.status = newStatus;
        }
        
        // Refresh statistics
        initializeVehicleStatistics();
        filterVehicles();
    }, 1000);
}

function getVehicleStatusColor(status) {
    const colors = {
        'available': 'success',
        'dispatched': 'warning',
        'maintenance': 'info',
        'out_of_service': 'danger'
    };
    return colors[status] || 'secondary';
}

function setupVehicleFormValidation() {
    const vehicleForm = document.getElementById('vehicleForm');
    if (vehicleForm) {
        vehicleForm.addEventListener('submit', function(e) {
            if (!validateVehicleForm()) {
                e.preventDefault();
                dispatchSystem.showNotification('Please check the form for errors', 'error');
            }
        });
    }
}

function validateVehicleForm() {
    const name = document.getElementById('name');
    const plateNumber = document.getElementById('plate_number');
    const vehicleType = document.getElementById('vehicle_type');
    
    let isValid = true;
    
    if (!name.value.trim()) {
        showFieldError(name, 'Vehicle name is required');
        isValid = false;
    }
    
    if (!plateNumber.value.trim()) {
        showFieldError(plateNumber, 'Plate number is required');
        isValid = false;
    }
    
    if (!vehicleType.value) {
        showFieldError(vehicleType, 'Please select a vehicle type');
        isValid = false;
    }
    
    return isValid;
}

function showFieldError(field, message) {
    // Clear existing error
    const existingError = field.parentNode.querySelector('.field-error');
    if (existingError) existingError.remove();
    
    // Add new error
    const errorDiv = document.createElement('div');
    errorDiv.className = 'field-error text-danger small mt-1';
    errorDiv.textContent = message;
    field.parentNode.appendChild(errorDiv);
    field.classList.add('is-invalid');
}

function enableQuickAddMode() {
    // Simplify form for quick additions
    const form = document.getElementById('vehicleForm');
    form.classList.add('quick-add-mode');
    dispatchSystem.showNotification('Quick add mode enabled - simplified form', 'info');
}

function disableQuickAddMode() {
    const form = document.getElementById('vehicleForm');
    form.classList.remove('quick-add-mode');
}

// Enhanced delete with confirmation
document.querySelectorAll('.delete-vehicle-btn').forEach(button => {
    button.addEventListener('click', function() {
        const vehicleId = this.dataset.id;
        const vehicleName = this.dataset.name;
        
        if (confirm(`Are you sure you want to delete "${vehicleName}"? This action cannot be undone.`)) {
            document.getElementById('deleteVehicleId').value = vehicleId;
            document.getElementById('deleteVehicleForm').submit();
        }
    });
});

// Make functions available globally
window.filterVehicles = filterVehicles;
window.updateVehicleStatus = updateVehicleStatus;
</script>

<!-- Hidden forms for actions -->
<form id="deleteVehicleForm" method="POST" action="?page=vehicles" style="display: none;">
    <input type="hidden" name="delete_vehicle" value="1">
    <input type="hidden" name="vehicle_id" id="deleteVehicleId">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(get_csrf_token()) ?>">
</form>

<style>
/* Vehicle-specific styles */
.vehicle-info {
    line-height: 1.4;
}

.location-info {
    font-size: 0.9rem;
}

.quick-add-mode .form-label {
    font-size: 0.9rem;
}

.quick-add-mode .form-control,
.quick-add-mode .form-select {
    padding: 0.5rem 0.75rem;
    font-size: 0.9rem;
}

@media (max-width: 768px) {
    .vehicle-info .fw-bold {
        font-size: 0.9rem;
    }
    
    .status-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
}
</style>

<?php include 'footer.php'; ?>