<?php
/**
 * Enhanced log_incident.php
 * Clean, organized with external CSS/JS
 */

// [Keep all your original PHP code exactly as it was - including the Southern Leyte locations array]
?>

<div class="row">
    <div class="col-12">
        <div class="page-header">
            <h2 class="mb-2">Incident Management</h2>
            <p class="text-muted">Report and track emergency incidents in real-time</p>
        </div>
    </div>
</div>

<!-- Incident Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body text-center">
                <i class="fas fa-clock fa-2x text-secondary mb-2"></i>
                <h4 class="mb-0" id="pendingCount">0</h4>
                <small class="text-muted">Pending</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body text-center">
                <i class="fas fa-exclamation-triangle fa-2x text-danger mb-2"></i>
                <h4 class="mb-0" id="activeCount">0</h4>
                <small class="text-muted">Active</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body text-center">
                <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                <h4 class="mb-0" id="resolvedCount">0</h4>
                <small class="text-muted">Resolved</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body text-center">
                <i class="fas fa-times-circle fa-2x text-info mb-2"></i>
                <h4 class="mb-0" id="cancelledCount">0</h4>
                <small class="text-muted">Cancelled</small>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4 shadow-soft rounded-3">
    <div class="card-header bg-primary text-white rounded-top d-flex justify-content-between align-items-center">
        <div>
            <i class="fas fa-plus-circle me-2"></i>
            <?= $edit_incident_data ? 'Edit Incident Report' : 'Report New Incident' ?>
        </div>
        <div class="incident-number-badge">
            <span class="badge bg-light text-dark">
                <i class="fas fa-hashtag me-1"></i>
                <?= htmlspecialchars($edit_incident_data['incident_number'] ?? ($generated_incident_number ?? 'Generating...')) ?>
            </span>
        </div>
    </div>
    <div class="card-body">
        <form method="POST" action="?page=incidents" novalidate class="needs-validation" id="incidentForm">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(get_csrf_token()) ?>">
            <?php if ($edit_incident_data): ?>
                <input type="hidden" name="incident_id" value="<?= htmlspecialchars($edit_incident_data['incident_id']) ?>">
            <?php endif; ?>

            <div class="row g-3">
                <!-- Incident Number (Auto-generated) -->
                <div class="col-md-6">
                    <label for="incident_number" class="form-label">
                        <i class="fas fa-hashtag me-1 text-primary"></i>
                        Incident Reference Number
                    </label>
                    <input type="text" class="form-control" id="incident_number" name="incident_number" 
                           value="<?= htmlspecialchars($edit_incident_data['incident_number'] ?? ($generated_incident_number ?? '')) ?>" 
                           readonly>
                    <div class="form-text">Automatically generated incident identifier</div>
                </div>

                <!-- Incident Type -->
                <div class="col-md-6">
                    <label for="incident_type" class="form-label">
                        <i class="fas fa-tag me-1 text-warning"></i>
                        Incident Type <span class="text-danger">*</span>
                    </label>
                    <select class="form-select" id="incident_type" name="incident_type" required>
                        <option value="">Select Incident Type</option>
                        <option value="fire" <?= ($edit_incident_data['incident_type'] ?? '') === 'fire' ? 'selected' : '' ?>>🔥 Fire Emergency</option>
                        <option value="flood" <?= ($edit_incident_data['incident_type'] ?? '') === 'flood' ? 'selected' : '' ?>>🌊 Flood Situation</option>
                        <option value="medical" <?= ($edit_incident_data['incident_type'] ?? '') === 'medical' ? 'selected' : '' ?>>🏥 Medical Emergency</option>
                        <option value="landslide" <?= ($edit_incident_data['incident_type'] ?? '') === 'landslide' ? 'selected' : '' ?>>⛰️ Landslide Incident</option>
                        <option value="earthquake" <?= ($edit_incident_data['incident_type'] ?? '') === 'earthquake' ? 'selected' : '' ?>>🌍 Earthquake Event</option>
                        <option value="rescue" <?= ($edit_incident_data['incident_type'] ?? '') === 'rescue' ? 'selected' : '' ?>>🆘 Rescue Operation</option>
                        <option value="other" <?= ($edit_incident_data['incident_type'] ?? '') === 'other' ? 'selected' : '' ?>>📋 Other Emergency</option>
                    </select>
                    <div class="invalid-feedback">Please select the incident type.</div>
                </div>

                <!-- Incident Description -->
                <div class="col-12">
                    <label for="description" class="form-label">
                        <i class="fas fa-align-left me-1 text-info"></i>
                        Incident Description <span class="text-danger">*</span>
                    </label>
                    <textarea class="form-control" id="description" name="description" rows="4" 
                              placeholder="Provide detailed description of the incident, including severity, number of people affected, immediate risks, and any other relevant information..."
                              required><?= htmlspecialchars($edit_incident_data['description'] ?? '') ?></textarea>
                    <div class="form-text">
                        <span id="charCount">0</span> characters. Be as detailed as possible for proper response planning.
                    </div>
                    <div class="invalid-feedback">Please provide a detailed incident description.</div>
                </div>

                <!-- Location Selection - Enhanced -->
                <div class="col-md-6">
                    <label for="municipality" class="form-label">
                        <i class="fas fa-city me-1 text-success"></i>
                        Municipality/City <span class="text-danger">*</span>
                    </label>
                    <select class="form-select" id="municipality" name="municipality" required>
                        <option value="">Select Municipality/City</option>
                        <?php 
                        $selected_municipality = '';
                        if (isset($edit_incident_data['location'])) {
                            $parts = explode(', ', $edit_incident_data['location']);
                            if (count($parts) > 1) {
                                $selected_municipality = $parts[1];
                            }
                        }
                        
                        foreach ($southern_leyte_locations as $municipality => $barangays): 
                        ?>
                            <option value="<?= htmlspecialchars($municipality) ?>" 
                                <?= ($selected_municipality === $municipality) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($municipality) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback">Please select a municipality/city.</div>
                </div>
                
                <div class="col-md-6">
                    <label for="barangay" class="form-label">
                        <i class="fas fa-map-marker-alt me-1 text-danger"></i>
                        Barangay <span class="text-danger">*</span>
                    </label>
                    <select class="form-select" id="barangay" name="barangay" required disabled>
                        <option value="">Select Municipality First</option>
                    </select>
                    <input type="hidden" id="location" name="location">
                    <div class="invalid-feedback">Please select a barangay.</div>
                </div>

                <!-- Additional Incident Details -->
                <div class="col-md-6">
                    <label for="reported_by" class="form-label">
                        <i class="fas fa-user me-1 text-primary"></i>
                        Reported By
                    </label>
                    <input type="text" class="form-control" id="reported_by" name="reported_by" 
                           placeholder="e.g., Walk-in, Police, BDRRMC, Citizen"
                           value="<?= htmlspecialchars($edit_incident_data['reported_by'] ?? 'Walk-in') ?>">
                    <div class="form-text">Source of the incident report</div>
                </div>

                <div class="col-md-6">
                    <label for="status" class="form-label">
                        <i class="fas fa-traffic-light me-1 text-warning"></i>
                        Incident Status <span class="text-danger">*</span>
                    </label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="pending" <?= ($edit_incident_data['status'] ?? '') === 'pending' ? 'selected' : '' ?>>⏳ Pending Response</option>
                        <option value="active" <?= ($edit_incident_data['status'] ?? '') === 'active' ? 'selected' : '' ?>>🚨 Active Response</option>
                        <option value="resolved" <?= ($edit_incident_data['status'] ?? '') === 'resolved' ? 'selected' : '' ?>>✅ Resolved</option>
                        <option value="cancelled" <?= ($edit_incident_data['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>❌ Cancelled/False Alarm</option>
                    </select>
                </div>

                <!-- Emergency Level Indicator -->
                <div class="col-12">
                    <div class="emergency-level-card">
                        <label class="form-label">
                            <i class="fas fa-exclamation-triangle me-1 text-danger"></i>
                            Emergency Level Assessment
                        </label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="emergency_level" id="level_low" value="low" autocomplete="off">
                            <label class="btn btn-outline-success" for="level_low">
                                <i class="fas fa-check-circle me-1"></i>Low
                            </label>

                            <input type="radio" class="btn-check" name="emergency_level" id="level_medium" value="medium" autocomplete="off" checked>
                            <label class="btn btn-outline-warning" for="level_medium">
                                <i class="fas fa-exclamation-triangle me-1"></i>Medium
                            </label>

                            <input type="radio" class="btn-check" name="emergency_level" id="level_high" value="high" autocomplete="off">
                            <label class="btn btn-outline-danger" for="level_high">
                                <i class="fas fa-skull-crossbones me-1"></i>High
                            </label>

                            <input type="radio" class="btn-check" name="emergency_level" id="level_critical" value="critical" autocomplete="off">
                            <label class="btn btn-outline-dark" for="level_critical">
                                <i class="fas fa-biohazard me-1"></i>Critical
                            </label>
                        </div>
                        <div class="form-text">Assess the severity level for appropriate response prioritization</div>
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                <?php if ($edit_incident_data): ?>
                    <button type="submit" name="edit_incident" class="btn btn-success btn-lg">
                        <i class="fas fa-save me-1"></i> Update Incident
                    </button>
                    <a href="?page=incidents" class="btn btn-secondary btn-lg">
                        <i class="fas fa-undo me-1"></i> Cancel Edit
                    </a>
                <?php else: ?>
                    <button type="submit" name="add_incident" class="btn btn-danger btn-lg">
                        <i class="fas fa-triangle-exclamation me-1"></i> Report Emergency
                    </button>
                    <button type="reset" class="btn btn-outline-secondary btn-lg">
                        <i class="fas fa-eraser me-1"></i> Clear Form
                    </button>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Recent Incidents Table -->
<div class="card shadow-soft rounded-3">
    <div class="card-header bg-info text-white rounded-top d-flex justify-content-between align-items-center">
        <div>
            <i class="fas fa-list me-2"></i>
            Recent Incident Reports
            <span class="badge bg-light text-dark ms-2" id="totalIncidents"><?= count($incidents) ?></span>
        </div>
        <div class="export-controls">
            <button class="btn btn-sm btn-light me-2" onclick="dispatchSystem.exportTableToCSV('incidentsTable', 'incidents.csv')">
                <i class="fas fa-download me-1"></i> Export
            </button>
            <button class="btn btn-sm btn-light" onclick="window.print()">
                <i class="fas fa-print me-1"></i> Print
            </button>
        </div>
    </div>
    <div class="card-body">
        <?php if (count($incidents) > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle" id="incidentsTable">
                    <thead>
                        <tr>
                            <th width="30">
                                <input type="checkbox" class="form-check-input select-all" id="selectAllIncidents">
                            </th>
                            <th>Incident Details</th>
                            <th>Type & Location</th>
                            <th>Reported Info</th>
                            <th>Timeline</th>
                            <th>Status</th>
                            <th width="120" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($incidents as $incident): ?>
                            <tr class="incident-row" data-status="<?= htmlspecialchars($incident['status']) ?>">
                                <td>
                                    <input type="checkbox" class="form-check-input item-select" value="<?= htmlspecialchars($incident['incident_id']) ?>">
                                </td>
                                <td>
                                    <div class="incident-info">
                                        <div class="fw-bold text-primary"><?= htmlspecialchars($incident['incident_number']) ?></div>
                                        <div class="text-muted small incident-description">
                                            <?= htmlspecialchars(substr($incident['description'], 0, 80)) . (strlen($incident['description']) > 80 ? '...' : '') ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="type-location-info">
                                        <span class="badge bg-info text-dark mb-1">
                                            <?= htmlspecialchars(ucwords(str_replace('_', ' ', $incident['incident_type']))) ?>
                                        </span>
                                        <div class="small text-muted">
                                            <i class="fas fa-map-marker-alt me-1"></i>
                                            <?= htmlspecialchars($incident['location']) ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="reporter-info">
                                        <div class="small">
                                            <i class="fas fa-user me-1"></i>
                                            <?= htmlspecialchars($incident['reported_by']) ?>
                                        </div>
                                        <div class="small text-muted">
                                            <?= htmlspecialchars(formatDate($incident['reported_at'])) ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="timeline-info">
                                        <div class="small fw-bold">Reported: <?= htmlspecialchars(formatDate($incident['reported_at'], 'M d, H:i')) ?></div>
                                        <?php if ($incident['last_updated'] && $incident['last_updated'] != $incident['reported_at']): ?>
                                            <div class="text-muted smaller">
                                                Updated: <?= htmlspecialchars(formatDate($incident['last_updated'], 'M d, H:i')) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="status-badge 
                                        <?php
                                            switch ($incident['status']) {
                                                case 'pending': echo 'bg-secondary'; break;
                                                case 'active': echo 'bg-danger'; break;
                                                case 'resolved': echo 'bg-success'; break;
                                                case 'cancelled': echo 'bg-info text-dark'; break;
                                                default: echo 'bg-secondary'; break;
                                            }
                                        ?>">
                                        <i class="fas 
                                            <?php
                                                switch ($incident['status']) {
                                                    case 'pending': echo 'fa-clock'; break;
                                                    case 'active': echo 'fa-exclamation-triangle'; break;
                                                    case 'resolved': echo 'fa-check-circle'; break;
                                                    case 'cancelled': echo 'fa-times-circle'; break;
                                                    default: echo 'fa-question'; break;
                                                }
                                            ?> me-1">
                                        </i>
                                        <?= htmlspecialchars(ucwords($incident['status'])) ?>
                                    </span>
                                    
                                    <!-- Quick Status Update -->
                                    <div class="mt-1">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-outline-danger btn-sm quick-status-btn" 
                                                    data-id="<?= htmlspecialchars($incident['incident_id']) ?>" 
                                                    data-status="active"
                                                    title="Mark as Active">
                                                <i class="fas fa-exclamation-triangle"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-success btn-sm quick-status-btn"
                                                    data-id="<?= htmlspecialchars($incident['incident_id']) ?>" 
                                                    data-status="resolved"
                                                    title="Mark as Resolved">
                                                <i class="fas fa-check-circle"></i>
                                            </button>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="?page=incidents&action=edit&id=<?= htmlspecialchars($incident['incident_id']) ?>" 
                                           class="btn btn-sm btn-warning me-1" title="Edit Incident">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger delete-incident-btn" 
                                                data-id="<?= htmlspecialchars($incident['incident_id']) ?>" 
                                                data-number="<?= htmlspecialchars($incident['incident_number']) ?>"
                                                title="Delete Incident">
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
                    <div class="text-muted small" id="incidentSummary">
                        Showing <?= count($incidents) ?> incident reports
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <nav aria-label="Incident pagination">
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
                <i class="fas fa-triangle-exclamation fa-3x text-muted mb-3"></i>
                <h5>No Incidents Reported</h5>
                <p class="text-muted">No emergency incidents have been reported yet. When an incident occurs, use the form above to log it.</p>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Ready to respond:</strong> Your incident management system is active and ready for emergency reporting.
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Incident-specific JavaScript
document.addEventListener('DOMContentLoaded', function() {
    initializeIncidentStatistics();
    initializeLocationSelection();
    initializeIncidentFilters();
    initializeQuickStatusUpdates();
    setupIncidentFormValidation();
    
    // Character count for description
    const description = document.getElementById('description');
    const charCount = document.getElementById('charCount');
    
    if (description && charCount) {
        description.addEventListener('input', function() {
            charCount.textContent = this.value.length;
        });
        charCount.textContent = description.value.length;
    }
});

function initializeIncidentStatistics() {
    const incidents = <?= json_encode($incidents) ?>;
    let pending = 0, active = 0, resolved = 0, cancelled = 0;
    
    incidents.forEach(incident => {
        switch(incident.status) {
            case 'pending': pending++; break;
            case 'active': active++; break;
            case 'resolved': resolved++; break;
            case 'cancelled': cancelled++; break;
        }
    });
    
    document.getElementById('pendingCount').textContent = pending;
    document.getElementById('activeCount').textContent = active;
    document.getElementById('resolvedCount').textContent = resolved;
    document.getElementById('cancelledCount').textContent = cancelled;
}

function initializeLocationSelection() {
    // Store all barangay data in JavaScript
    const barangayData = <?= json_encode($southern_leyte_locations) ?>;
    const editLocation = <?= json_encode($edit_incident_data['location'] ?? '') ?>;
    
    const municipalitySelect = document.getElementById('municipality');
    const barangaySelect = document.getElementById('barangay');
    const locationField = document.getElementById('location');
    
    if (municipalitySelect && barangaySelect && locationField) {
        municipalitySelect.addEventListener('change', function() {
            const municipality = this.value;
            
            // Clear existing options
            barangaySelect.innerHTML = '<option value="">Select Barangay</option>';
            
            if (municipality && barangayData[municipality]) {
                // Enable barangay dropdown
                barangaySelect.disabled = false;
                
                // Add barangays for selected municipality
                barangayData[municipality].forEach(function(barangay) {
                    const option = document.createElement('option');
                    option.value = barangay;
                    option.textContent = 'Barangay ' + barangay;
                    barangaySelect.appendChild(option);
                });
            } else {
                barangaySelect.disabled = true;
            }
            
            updateLocationField();
        });
        
        // Update barangay when selected
        barangaySelect.addEventListener('change', updateLocationField);
        
        function updateLocationField() {
            const municipality = municipalitySelect.value;
            const barangay = barangaySelect.value;
            
            if (municipality && barangay) {
                locationField.value = 'Barangay ' + barangay + ', ' + municipality;
            } else {
                locationField.value = '';
            }
        }
        
        // For edit mode: Pre-populate barangay dropdown
        if (editLocation) {
            const parts = editLocation.split(', ');
            if (parts.length > 1) {
                const barangayName = parts[0].replace('Barangay ', '');
                const municipality = municipalitySelect.value;
                
                if (municipality && barangayData[municipality]) {
                    barangaySelect.disabled = false;
                    barangaySelect.innerHTML = '<option value="">Select Barangay</option>';
                    
                    barangayData[municipality].forEach(function(barangay) {
                        const option = document.createElement('option');
                        option.value = barangay;
                        option.textContent = 'Barangay ' + barangay;
                        if (barangay === barangayName) {
                            option.selected = true;
                        }
                        barangaySelect.appendChild(option);
                    });
                    
                    updateLocationField();
                }
            }
        }
    }
}

function initializeIncidentFilters() {
    const statusFilter = document.getElementById('statusFilter');
    if (statusFilter) {
        statusFilter.addEventListener('change', filterIncidents);
    }
}

function filterIncidents() {
    const status = document.getElementById('statusFilter')?.value || '';
    const rows = document.querySelectorAll('.incident-row');
    let visibleCount = 0;
    
    rows.forEach(row => {
        const showRow = !status || row.dataset.status === status;
        row.style.display = showRow ? '' : 'none';
        if (showRow) visibleCount++;
    });
    
    document.getElementById('totalIncidents').textContent = visibleCount;
    document.getElementById('incidentSummary').textContent = `Showing ${visibleCount} of ${rows.length} incidents`;
}

function initializeQuickStatusUpdates() {
    document.querySelectorAll('.quick-status-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const incidentId = this.dataset.id;
            const newStatus = this.dataset.status;
            const incidentNumber = this.closest('tr').querySelector('.fw-bold').textContent;
            
            if (confirm(`Change incident ${incidentNumber} status to "${newStatus}"?`)) {
                updateIncidentStatus(incidentId, newStatus);
            }
        });
    });
}

function updateIncidentStatus(incidentId, newStatus) {
    dispatchSystem.showLoading('Updating incident status...');
    
    // Simulate API call - in real implementation, this would be an AJAX call
    setTimeout(() => {
        dispatchSystem.hideLoading();
        dispatchSystem.showNotification(`Incident status updated to ${newStatus}`, 'success');
        
        // Update UI
        const row = document.querySelector(`.quick-status-btn[data-id="${incidentId}"]`).closest('tr');
        const statusBadge = row.querySelector('.status-badge');
        
        if (statusBadge) {
            statusBadge.textContent = newStatus;
            statusBadge.className = `status-badge bg-${getIncidentStatusColor(newStatus)}`;
            
            // Update row data attribute
            row.dataset.status = newStatus;
        }
        
        // Refresh statistics
        initializeIncidentStatistics();
        filterIncidents();
    }, 1000);
}

function getIncidentStatusColor(status) {
    const colors = {
        'pending': 'secondary',
        'active': 'danger',
        'resolved': 'success',
        'cancelled': 'info'
    };
    return colors[status] || 'secondary';
}

function setupIncidentFormValidation() {
    const incidentForm = document.getElementById('incidentForm');
    if (incidentForm) {
        incidentForm.addEventListener('submit', function(e) {
            if (!validateIncidentForm()) {
                e.preventDefault();
                dispatchSystem.showNotification('Please check the form for errors', 'error');
            }
        });
    }
}

function validateIncidentForm() {
    const incidentType = document.getElementById('incident_type');
    const description = document.getElementById('description');
    const municipality = document.getElementById('municipality');
    const barangay = document.getElementById('barangay');
    
    let isValid = true;
    
    if (!incidentType.value) {
        showFieldError(incidentType, 'Please select an incident type');
        isValid = false;
    }
    
    if (!description.value.trim()) {
        showFieldError(description, 'Please provide an incident description');
        isValid = false;
    }
    
    if (!municipality.value) {
        showFieldError(municipality, 'Please select a municipality');
        isValid = false;
    }
    
    if (!barangay.value || barangay.disabled) {
        showFieldError(barangay, 'Please select a barangay');
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

// Enhanced delete with confirmation
document.querySelectorAll('.delete-incident-btn').forEach(button => {
    button.addEventListener('click', function() {
        const incidentId = this.dataset.id;
        const incidentNumber = this.dataset.number;
        
        if (confirm(`Are you sure you want to delete incident ${incidentNumber}? This action cannot be undone.`)) {
            document.getElementById('deleteIncidentId').value = incidentId;
            document.getElementById('deleteIncidentForm').submit();
        }
    });
});

// Make functions available globally
window.filterIncidents = filterIncidents;
window.updateIncidentStatus = updateIncidentStatus;
</script>

<!-- Hidden forms for actions -->
<form id="deleteIncidentForm" method="POST" action="?page=incidents" style="display: none;">
    <input type="hidden" name="delete_incident" value="1">
    <input type="hidden" name="incident_id" id="deleteIncidentId">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(get_csrf_token()) ?>">
</form>

<style>
/* Incident-specific styles */
.incident-number-badge .badge {
    font-size: 0.9rem;
    font-family: 'Courier New', monospace;
}

.emergency-level-card {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 8px;
    border: 1px solid #e9ecef;
}

.emergency-level-card .btn-group .btn {
    border-radius: 4px;
    font-weight: 600;
}

.incident-info .incident-description {
    line-height: 1.4;
    margin-top: 0.25rem;
}

.type-location-info,
.reporter-info,
.timeline-info {
    line-height: 1.4;
}

@media (max-width: 768px) {
    .emergency-level-card .btn-group {
        flex-direction: column;
    }
    
    .emergency-level-card .btn-group .btn {
        border-radius: 0;
        margin-bottom: 0.25rem;
    }
    
    .incident-info .fw-bold {
        font-size: 0.9rem;
    }
}
</style>

<?php include 'footer.php'; ?>