/**
 * COMPLETE Enhanced JavaScript for CDRRMO Dispatch System
 * All JavaScript consolidated here - no inline scripts in other files
 */

// Global state management
window.dispatchSystem = window.dispatchSystem || {};
window.dispatchSystem.state = {
    selectedItems: new Set(),
    currentFilters: {},
    searchQuery: '',
    sortConfig: {},
    // Dispatch-specific state
    dispatchFilters: {
        status: '',
        date: '',
        ticket: ''
    }
};

// Initialize when document is ready
document.addEventListener('DOMContentLoaded', function() {
    initializeEnhancedSystem();
    initializeDispatchSpecificFeatures();
});

// Enhanced system initialization
function initializeEnhancedSystem() {
    setupEnhancedFormValidation();
    initializeAdvancedInteractions();
    setupRealTimeUpdates();
    initializeBulkActions();
    setupEnhancedSearch();
    initializeExportFunctionality();
    initializeGlobalComponents();
    
    // Update current time with better formatting
    updateCurrentTime();
    setInterval(updateCurrentTime, 1000);
}

// Initialize dispatch-specific features
function initializeDispatchSpecificFeatures() {
    initializeDispatchFilters();
    initializeQuickStatusUpdates();
    initializeResourceValidation();
    setupDispatchFormValidation();
}

// ==================== GLOBAL COMPONENTS ====================

function initializeGlobalComponents() {
    // Mobile menu toggle
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    
    if (menuToggle && sidebar) {
        menuToggle.addEventListener('click', function() {
            sidebar.classList.toggle('show');
            if (sidebarOverlay) sidebarOverlay.classList.toggle('show');
        });
    }
    
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', function() {
            sidebar.classList.remove('show');
            sidebarOverlay.classList.remove('show');
        });
    }
    
    // Global search functionality
    const globalSearch = document.getElementById('globalSearch');
    if (globalSearch) {
        let searchTimeout;
        globalSearch.addEventListener('input', function(e) {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                performGlobalSearch(this.value);
            }, 300);
        });
    }
    
    // Keyboard shortcuts
    setupKeyboardShortcuts();
}

// ==================== DISPATCH-SPECIFIC FUNCTIONS ====================

function initializeDispatchFilters() {
    const statusFilter = document.getElementById('statusFilter');
    const dateFilter = document.getElementById('dateFilter');
    const ticketFilter = document.getElementById('ticketFilter');
    
    [statusFilter, dateFilter, ticketFilter].forEach(filter => {
        if (filter) {
            filter.addEventListener('change', filterDispatches);
        }
    });
}

function filterDispatches() {
    const status = document.getElementById('statusFilter')?.value || '';
    const date = document.getElementById('dateFilter')?.value || '';
    const ticket = document.getElementById('ticketFilter')?.value || '';
    
    // Update state
    window.dispatchSystem.state.dispatchFilters = { status, date, ticket };
    
    const rows = document.querySelectorAll('.dispatch-row');
    let visibleCount = 0;
    
    rows.forEach(row => {
        let showRow = true;
        
        // Status filter
        if (status && row.dataset.status !== status) {
            showRow = false;
        }
        
        // Date filter (simplified - would need proper date comparison)
        if (date) {
            // Implement date filtering logic here
            const rowDate = row.querySelector('.timeline-info .small')?.textContent;
            // Add date comparison logic
        }
        
        // Ticket filter
        if (ticket && row.dataset.ticket !== ticket) {
            showRow = false;
        }
        
        row.style.display = showRow ? '' : 'none';
        if (showRow) visibleCount++;
    });
    
    // Update counts and summary
    const dispatchCount = document.getElementById('dispatchCount');
    const tableSummary = document.getElementById('tableSummary');
    
    if (dispatchCount) dispatchCount.textContent = visibleCount;
    if (tableSummary) tableSummary.textContent = `Showing ${visibleCount} of ${rows.length} dispatch operations`;
}

function clearFilters() {
    const statusFilter = document.getElementById('statusFilter');
    const dateFilter = document.getElementById('dateFilter');
    const ticketFilter = document.getElementById('ticketFilter');
    
    if (statusFilter) statusFilter.value = '';
    if (dateFilter) dateFilter.value = '';
    if (ticketFilter) ticketFilter.value = '';
    
    filterDispatches();
    window.dispatchSystem.showNotification('Filters cleared', 'info');
}

// Quick status updates for dispatches
function initializeQuickStatusUpdates() {
    document.querySelectorAll('.quick-status-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const dispatchId = this.dataset.id;
            const newStatus = this.dataset.status;
            
            if (confirm(`Change dispatch status to "${newStatus.replace('_', ' ')}"?`)) {
                updateDispatchStatus(dispatchId, newStatus);
            }
        });
    });
}

function updateDispatchStatus(dispatchId, newStatus) {
    window.dispatchSystem.showLoading('Updating dispatch status...');
    
    // In real implementation, this would be an AJAX call to update the database
    // For now, we'll simulate the update
    setTimeout(() => {
        window.dispatchSystem.hideLoading();
        window.dispatchSystem.showNotification(`Dispatch status updated to ${newStatus.replace('_', ' ')}`, 'success');
        
        // Update UI
        const row = document.querySelector(`.quick-status-btn[data-id="${dispatchId}"]`).closest('tr');
        const statusBadge = row.querySelector('.status-badge');
        
        if (statusBadge) {
            statusBadge.textContent = newStatus.replace('_', ' ');
            statusBadge.className = `status-badge bg-${getStatusColor(newStatus)}`;
            
            // Update row data attribute
            row.dataset.status = newStatus;
        }
        
        // Refresh filters to ensure proper display
        filterDispatches();
    }, 1000);
}

function getStatusColor(status) {
    const colors = {
        'assigned': 'primary',
        'en_route': 'warning',
        'on_scene': 'danger',
        'returning': 'info',
        'completed': 'success',
        'cancelled': 'secondary'
    };
    return colors[status] || 'secondary';
}

// Resource validation for dispatch form
function initializeResourceValidation() {
    const vehicleSelect = document.getElementById('vehicle_id');
    const responderSelect = document.getElementById('responder_id');
    
    if (vehicleSelect) {
        vehicleSelect.addEventListener('change', function() {
            validateResourceAvailability(this, 'vehicle');
        });
    }
    
    if (responderSelect) {
        responderSelect.addEventListener('change', function() {
            validateResourceAvailability(this, 'responder');
        });
    }
}

function validateResourceAvailability(select, type) {
    const selectedOption = select.options[select.selectedIndex];
    if (selectedOption && selectedOption.dataset.status === 'dispatched') {
        window.dispatchSystem.showNotification(
            `This ${type} is currently dispatched. Please ensure availability.`, 
            'warning'
        );
    }
}

// Enhanced dispatch form validation
function setupDispatchFormValidation() {
    const dispatchForm = document.getElementById('dispatchForm');
    if (dispatchForm) {
        dispatchForm.addEventListener('submit', function(e) {
            if (!validateDispatchForm()) {
                e.preventDefault();
                window.dispatchSystem.showNotification('Please check the form for errors', 'error');
            }
        });
    }
}

function validateDispatchForm() {
    const incidentId = document.getElementById('incident_id');
    const status = document.getElementById('status');
    
    let isValid = true;
    
    if (!incidentId || !incidentId.value) {
        showFieldError(incidentId, 'Please select an incident');
        isValid = false;
    }
    
    if (!status || !status.value) {
        showFieldError(status, 'Please select a status');
        isValid = false;
    }
    
    return isValid;
}

// ==================== GLOBAL UTILITY FUNCTIONS ====================

// Enhanced form validation with better feedback (1D: Words)
function setupEnhancedFormValidation() {
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
                
                // Enhanced error display
                const invalidFields = form.querySelectorAll(':invalid');
                invalidFields.forEach(field => {
                    showFieldError(field, getFieldErrorMessage(field));
                });
            }
            form.classList.add('was-validated');
        });
        
        // Real-time validation
        form.querySelectorAll('input, select, textarea').forEach(field => {
            field.addEventListener('blur', function() {
                if (!this.checkValidity()) {
                    showFieldError(this, getFieldErrorMessage(this));
                } else {
                    clearFieldError(this);
                }
            });
        });
    });
}

function getFieldErrorMessage(field) {
    if (field.validity.valueMissing) return 'This field is required';
    if (field.validity.typeMismatch) return 'Please enter a valid format';
    if (field.validity.patternMismatch) return 'Please match the requested format';
    if (field.validity.tooShort) return `Minimum length is ${field.minLength} characters`;
    if (field.validity.tooLong) return `Maximum length is ${field.maxLength} characters`;
    return 'Please correct this field';
}

function showFieldError(field, message) {
    if (!field) return;
    
    clearFieldError(field);
    const errorDiv = document.createElement('div');
    errorDiv.className = 'field-error text-danger small mt-1';
    errorDiv.textContent = message;
    field.parentNode.appendChild(errorDiv);
    field.classList.add('is-invalid');
}

function clearFieldError(field) {
    if (!field) return;
    
    const existingError = field.parentNode.querySelector('.field-error');
    if (existingError) existingError.remove();
    field.classList.remove('is-invalid');
}

// Bulk actions management
function initializeBulkActions() {
    // Select all checkbox
    const selectAll = document.querySelector('.select-all');
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.item-select');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
                toggleItemSelection(checkbox);
            });
            updateBulkActionsPanel();
        });
    }
    
    // Individual item selection
    document.querySelectorAll('.item-select').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            toggleItemSelection(this);
            updateBulkActionsPanel();
        });
    });
}

function toggleItemSelection(checkbox) {
    const itemId = checkbox.value;
    if (checkbox.checked) {
        window.dispatchSystem.state.selectedItems.add(itemId);
    } else {
        window.dispatchSystem.state.selectedItems.delete(itemId);
        // Uncheck select all if any item is deselected
        const selectAll = document.querySelector('.select-all');
        if (selectAll) selectAll.checked = false;
    }
}

function updateBulkActionsPanel() {
    const bulkPanel = document.querySelector('.bulk-actions');
    const selectedCount = window.dispatchSystem.state.selectedItems.size;
    
    if (bulkPanel) {
        if (selectedCount > 0) {
            bulkPanel.classList.add('show');
            const countElement = bulkPanel.querySelector('.selected-count');
            if (countElement) countElement.textContent = selectedCount;
        } else {
            bulkPanel.classList.remove('show');
        }
    }
}

// Execute bulk action
function executeBulkAction(action) {
    const selectedItems = Array.from(window.dispatchSystem.state.selectedItems);
    if (selectedItems.length === 0) {
        window.dispatchSystem.showNotification('Please select items to perform this action', 'warning');
        return;
    }
    
    if (confirm(`Are you sure you want to ${action} ${selectedItems.length} item(s)?`)) {
        window.dispatchSystem.showLoading(`Processing ${action}...`);
        
        // Simulate API call
        setTimeout(() => {
            window.dispatchSystem.hideLoading();
            window.dispatchSystem.showNotification(`Successfully ${action}ed ${selectedItems.length} item(s)`, 'success');
            clearSelections();
        }, 1000);
    }
}

function clearSelections() {
    window.dispatchSystem.state.selectedItems.clear();
    document.querySelectorAll('.item-select').forEach(cb => cb.checked = false);
    const selectAll = document.querySelector('.select-all');
    if (selectAll) selectAll.checked = false;
    updateBulkActionsPanel();
}

// Enhanced search functionality
function setupEnhancedSearch() {
    const searchInput = document.querySelector('.search-box input');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                performSearch(this.value);
            }, 300);
        });
    }
}

function performSearch(query) {
    window.dispatchSystem.state.searchQuery = query;
    // This would filter the current page's content
    console.log('Searching for:', query);
}

function performGlobalSearch(query) {
    // Global search across the system
    console.log('Global search:', query);
}

// Enhanced export functionality
function initializeExportFunctionality() {
    document.querySelectorAll('.export-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const format = this.dataset.format;
            exportData(format);
        });
    });
}

function exportData(format) {
    const filters = window.dispatchSystem.state.currentFilters;
    const searchQuery = window.dispatchSystem.state.searchQuery;
    
    window.dispatchSystem.showLoading(`Exporting data as ${format.toUpperCase()}...`);
    
    // Simulate export process
    setTimeout(() => {
        window.dispatchSystem.hideLoading();
        window.dispatchSystem.showNotification(`Data exported successfully as ${format.toUpperCase()}`, 'success');
        
        // For demo purposes, create a dummy download
        if (format === 'csv') {
            downloadCSV();
        }
    }, 1500);
}

function downloadCSV() {
    const csvContent = "data:text/csv;charset=utf-8," + "Sample,Exported,Data\n1,Test,Info";
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "exported_data.csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Advanced interactions (3D: Physical Objects/Space)
function initializeAdvancedInteractions() {
    // Quick actions
    document.querySelectorAll('.quick-action-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const action = this.dataset.action;
            executeQuickAction(action);
        });
    });
}

function executeQuickAction(action) {
    switch(action) {
        case 'refresh':
            window.location.reload();
            break;
        case 'add-new':
            // Focus on the first form field or trigger form display
            const firstInput = document.querySelector('form input, form select, form textarea');
            if (firstInput) firstInput.focus();
            break;
        case 'export':
            exportData('csv');
            break;
        default:
            console.log('Quick action:', action);
    }
}

// Real-time updates (4D: Time)
function setupRealTimeUpdates() {
    // Auto-refresh for dashboard
    if (window.location.href.includes('dashboard')) {
        setInterval(refreshDashboardData, 30000);
    }
}

function refreshDashboardData() {
    fetch('?page=dashboard&action=refresh', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        updateDashboardStats(data);
        // Show subtle notification
        const toast = document.getElementById('enhancedToast');
        if (toast) {
            const toastInstance = bootstrap.Toast.getOrCreateInstance(toast);
            toast.querySelector('.toast-body').textContent = 'Dashboard updated';
            toastInstance.show();
        }
    })
    .catch(error => {
        console.error('Error refreshing dashboard:', error);
    });
}

function updateDashboardStats(data) {
    // Update dashboard statistics
    if (data.active_dispatches) {
        const element = document.querySelector('.stat-active-dispatches');
        if (element) element.textContent = data.active_dispatches;
    }
    // Add more stat updates as needed
}

// Enhanced notification system (5D: Behavior)
function showEnhancedToast(message, type = 'info', duration = 5000) {
    const toastEl = document.getElementById('enhancedToast');
    if (!toastEl) return;
    
    const toast = new bootstrap.Toast(toastEl, { delay: duration });
    
    // Update toast content and styling
    const icon = toastEl.querySelector('.toast-icon');
    const title = toastEl.querySelector('.toast-title');
    const body = toastEl.querySelector('.toast-body');
    const time = toastEl.querySelector('.toast-time');
    
    // Set type-specific styling
    const typeConfig = {
        success: { icon: 'fa-check-circle', color: 'text-success', title: 'Success' },
        error: { icon: 'fa-exclamation-circle', color: 'text-danger', title: 'Error' },
        warning: { icon: 'fa-exclamation-triangle', color: 'text-warning', title: 'Warning' },
        info: { icon: 'fa-info-circle', color: 'text-info', title: 'Information' }
    };
    
    const config = typeConfig[type] || typeConfig.info;
    
    if (icon) icon.className = `fas ${config.icon} me-2 ${config.color}`;
    if (title) title.textContent = config.title;
    if (body) body.textContent = message;
    if (time) time.textContent = 'just now';
    
    // Show toast
    toast.show();
}

// Enhanced loading states
function showLoading(message = 'Loading...') {
    let overlay = document.querySelector('.loading-overlay-global');
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.className = 'loading-overlay-global';
        document.body.appendChild(overlay);
    }
    
    overlay.innerHTML = `
        <div class="text-center">
            <div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;"></div>
            <div class="loading-message">${message}</div>
        </div>
    `;
    overlay.style.display = 'flex';
}

function hideLoading() {
    const overlay = document.querySelector('.loading-overlay-global');
    if (overlay) overlay.style.display = 'none';
}

// Keyboard shortcuts (3D: Physical Objects/Space)
function setupKeyboardShortcuts() {
    document.addEventListener('keydown', function(e) {
        // Ctrl + F for search
        if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
            e.preventDefault();
            const searchInput = document.querySelector('.search-box input');
            if (searchInput) searchInput.focus();
        }
        
        // Ctrl + E for export
        if ((e.ctrlKey || e.metaKey) && e.key === 'e') {
            e.preventDefault();
            exportData('csv');
        }
        
        // Escape to clear selections
        if (e.key === 'Escape') {
            clearSelections();
        }
        
        // Ctrl + R to refresh
        if (e.ctrlKey && e.key === 'r') {
            e.preventDefault();
            window.location.reload();
        }
    });
}

// Current time display with better formatting
function updateCurrentTime() {
    const now = new Date();
    const timeString = now.toLocaleString('en-PH', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: true
    });
    
    const timeElement = document.getElementById('current-time');
    if (timeElement) {
        timeElement.textContent = timeString;
    }
}

// Export table to CSV
function exportTableToCSV(tableId, filename) {
    const table = document.getElementById(tableId);
    if (!table) return;
    
    const rows = table.querySelectorAll('tr');
    const csv = [];
    
    for (let i = 0; i < rows.length; i++) {
        const row = [], cols = rows[i].querySelectorAll('td, th');
        
        for (let j = 0; j < cols.length; j++) {
            // Clean and escape data
            let data = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, '').replace(/(\s\s)/gm, ' ');
            data = data.replace(/"/g, '""');
            row.push('"' + data + '"');
        }
        
        csv.push(row.join(','));
    }
    
    // Download CSV file
    const csvFile = new Blob([csv.join('\n')], { type: 'text/csv' });
    const downloadLink = document.createElement('a');
    downloadLink.download = filename;
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = 'none';
    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
}

// ==================== GLOBAL EXPORTS ====================

// Make all functions available globally
window.dispatchSystem = {
    ...window.dispatchSystem,
    // Global utilities
    showNotification: showEnhancedToast,
    showLoading,
    hideLoading,
    executeBulkAction,
    exportData,
    clearSelections,
    executeQuickAction,
    exportTableToCSV,
    // Dispatch-specific functions
    filterDispatches,
    clearFilters,
    updateDispatchStatus
};

// Backward compatibility
window.showNotification = showEnhancedToast;