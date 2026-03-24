<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Manage Appointments - RIYA MEDICARE Hospital Admin">
    <title>Manage Appointments - Admin Dashboard</title>
    <link rel="stylesheet" href="./css/style.css">
    <style>
        /* Admin Appointments Management Styles */
        .admin-container {
            display: grid;
            grid-template-columns: 250px 1fr;
            min-height: 100vh;
            background-color: var(--bg-dark);
        }

        .admin-sidebar {
            background-color: var(--bg-secondary);
            border-right: 2px solid var(--primary-color);
            padding: var(--spacing-lg);
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            box-shadow: 5px 0 20px rgba(181, 55, 242, 0.2);
        }

        .admin-main {
            padding: var(--spacing-2xl);
            overflow-y: auto;
        }

        .appointments-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: var(--spacing-2xl);
        }

        .appointments-header h1 {
            color: var(--primary-light);
            margin: 0;
            text-shadow: 0 0 15px rgba(181, 55, 242, 0.3);
        }

        .filter-controls {
            display: flex;
            gap: var(--spacing-md);
            margin-bottom: var(--spacing-2xl);
            flex-wrap: wrap;
        }

        .filter-controls select,
        .filter-controls input {
            padding: var(--spacing-sm) var(--spacing-md);
            border: 1px solid var(--primary-color);
            border-radius: var(--radius-md);
            background-color: var(--bg-tertiary);
            color: var(--text-primary);
            font-size: var(--font-size-sm);
        }

        .appointments-table-container {
            overflow-x: auto;
            background-color: var(--bg-tertiary);
            border: 1px solid var(--primary-color);
            border-radius: var(--radius-lg);
            box-shadow: 0 0 15px rgba(181, 55, 242, 0.2);
        }

        .appointments-table {
            width: 100%;
            border-collapse: collapse;
            color: var(--text-primary);
        }

        .appointments-table thead {
            background-color: rgba(181, 55, 242, 0.1);
            border-bottom: 2px solid var(--primary-color);
        }

        .appointments-table th {
            padding: var(--spacing-md);
            text-align: left;
            font-weight: 600;
            color: var(--primary-light);
            text-shadow: 0 0 10px rgba(181, 55, 242, 0.3);
            text-transform: uppercase;
            font-size: var(--font-size-xs);
            letter-spacing: 0.5px;
        }

        .appointments-table td {
            padding: var(--spacing-md);
            border-bottom: 1px solid var(--border-color);
        }

        .appointments-table tbody tr:hover {
            background-color: rgba(181, 55, 242, 0.05);
        }

        .status-badge {
            display: inline-block;
            padding: 0.4rem 0.8rem;
            border-radius: var(--radius-sm);
            font-weight: 600;
            font-size: var(--font-size-xs);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-pending {
            background-color: rgba(255, 193, 7, 0.2);
            color: #ffc107;
            border: 1px solid #ffc107;
        }

        .status-confirmed {
            background-color: rgba(16, 185, 129, 0.2);
            color: #10b981;
            border: 1px solid #10b981;
        }

        .status-completed {
            background-color: rgba(59, 130, 246, 0.2);
            color: #3b82f6;
            border: 1px solid #3b82f6;
        }

        .status-cancelled {
            background-color: rgba(239, 68, 68, 0.2);
            color: #ef4444;
            border: 1px solid #ef4444;
        }

        .action-buttons {
            display: flex;
            gap: var(--spacing-sm);
        }

        .action-btn {
            padding: 0.4rem 0.8rem;
            border: none;
            border-radius: var(--radius-sm);
            cursor: pointer;
            font-size: var(--font-size-xs);
            font-weight: 600;
            transition: all var(--transition-short);
            white-space: nowrap;
        }

        .btn-confirm {
            background-color: #10b981;
            color: white;
            box-shadow: 0 0 10px rgba(16, 185, 129, 0.3);
        }

        .btn-confirm:hover {
            box-shadow: 0 0 20px rgba(16, 185, 129, 0.6);
        }

        .btn-cancel {
            background-color: #ef4444;
            color: white;
            box-shadow: 0 0 10px rgba(239, 68, 68, 0.3);
        }

        .btn-cancel:hover {
            box-shadow: 0 0 20px rgba(239, 68, 68, 0.6);
        }

        .btn-view {
            background-color: var(--primary-color);
            color: #000000;
            box-shadow: 0 0 10px rgba(181, 55, 242, 0.3);
        }

        .btn-view:hover {
            box-shadow: 0 0 20px rgba(181, 55, 242, 0.6);
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            animation: fadeIn 0.3s ease-out;
        }

        .modal.show {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background-color: var(--bg-secondary);
            border: 2px solid var(--primary-color);
            border-radius: var(--radius-lg);
            padding: var(--spacing-2xl);
            max-width: 500px;
            width: 90%;
            box-shadow: 0 0 40px rgba(181, 55, 242, 0.4);
            max-height: 80vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: var(--spacing-lg);
            border-bottom: 1px solid var(--primary-color);
            padding-bottom: var(--spacing-md);
        }

        .modal-header h2 {
            color: var(--primary-light);
            margin: 0;
            text-shadow: 0 0 10px rgba(181, 55, 242, 0.3);
        }

        .close-modal {
            background: none;
            border: none;
            color: var(--text-secondary);
            font-size: 1.5rem;
            cursor: pointer;
        }

        .close-modal:hover {
            color: var(--primary-light);
        }

        .modal-body {
            display: grid;
            gap: var(--spacing-md);
        }

        .modal-field {
            display: grid;
            gap: var(--spacing-xs);
        }

        .modal-field label {
            color: var(--primary-light);
            font-weight: 600;
            font-size: var(--font-size-sm);
        }

        .modal-field p {
            color: var(--text-primary);
            margin: 0;
            word-break: break-word;
        }

        .modal-actions {
            display: flex;
            gap: var(--spacing-md);
            margin-top: var(--spacing-2xl);
            padding-top: var(--spacing-lg);
            border-top: 1px solid var(--primary-color);
        }

        .modal-actions button {
            flex: 1;
            padding: var(--spacing-md);
            border: none;
            border-radius: var(--radius-md);
            cursor: pointer;
            font-weight: 600;
            transition: all var(--transition-short);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: var(--spacing-lg);
            margin-bottom: var(--spacing-2xl);
        }

        .stat-card {
            background: linear-gradient(135deg, rgba(181, 55, 242, 0.1), rgba(0, 240, 255, 0.1));
            border: 1px solid var(--primary-color);
            border-radius: var(--radius-lg);
            padding: var(--spacing-lg);
            text-align: center;
            box-shadow: 0 0 15px rgba(181, 55, 242, 0.2);
        }

        .stat-icon {
            font-size: 2rem;
            margin-bottom: var(--spacing-sm);
        }

        .stat-value {
            font-size: var(--font-size-2xl);
            color: var(--primary-light);
            font-weight: 700;
            text-shadow: 0 0 10px rgba(181, 55, 242, 0.3);
        }

        .stat-label {
            color: var(--text-secondary);
            font-size: var(--font-size-sm);
            margin-top: var(--spacing-xs);
        }

        .empty-state {
            text-align: center;
            padding: var(--spacing-3xl) var(--spacing-xl);
            color: var(--text-secondary);
        }

        .empty-state-icon {
            font-size: 3rem;
            margin-bottom: var(--spacing-md);
        }

        @media (max-width: 768px) {
            .admin-container {
                grid-template-columns: 1fr;
            }

            .admin-sidebar {
                position: fixed;
                left: -250px;
                top: 0;
                z-index: 900;
                width: 250px;
                height: 100vh;
                transition: left 0.3s ease;
            }

            .admin-sidebar.show {
                left: 0;
            }

            .appointments-header {
                flex-direction: column;
                align-items: flex-start;
                gap: var(--spacing-lg);
            }

            .filter-controls {
                flex-direction: column;
            }

            .filter-controls select,
            .filter-controls input {
                width: 100%;
            }

            .action-buttons {
                flex-direction: column;
            }

            .action-btn {
                width: 100%;
            }

            .modal-content {
                width: 95%;
                max-width: 100%;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-logo">
                <div class="icon">⚕️</div>
                <span>Admin</span>
            </div>

            <ul class="sidebar-menu">
                <li><a href="./admin-dashboard.html">📊 Dashboard</a></li>
                <li><a href="./admin-appointments.php" class="active">📅 Appointments</a></li>
                <li><a href="./admin-dashboard.html">👨‍⚕️ Doctors</a></li>
                <li><a href="./admin-dashboard.html">👥 Patients</a></li>
                <li><a href="./admin-dashboard.html">📈 Reports</a></li>
                <li><a href="./admin-dashboard.html">⚙️ Settings</a></li>
            </ul>

            <hr style="border: 1px solid var(--primary-color); margin: var(--spacing-lg) 0;">

            <ul class="sidebar-menu">
                <li><a href="#" onclick="logoutUser(); return false;">🚪 Logout</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <div class="appointments-header">
                <h1>📅 Appointments Management</h1>
            </div>

            <!-- Statistics -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">📋</div>
                    <div class="stat-value" id="total-count">0</div>
                    <div class="stat-label">Total Appointments</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">⏳</div>
                    <div class="stat-value" id="pending-count">0</div>
                    <div class="stat-label">Pending</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">✅</div>
                    <div class="stat-value" id="confirmed-count">0</div>
                    <div class="stat-label">Confirmed</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">🏁</div>
                    <div class="stat-value" id="completed-count">0</div>
                    <div class="stat-label">Completed</div>
                </div>
            </div>

            <!-- Filter Controls -->
            <div class="filter-controls">
                <select id="statusFilter" onchange="filterAppointments()">
                    <option value="">All Statuses</option>
                    <option value="pending">Pending</option>
                    <option value="confirmed">Confirmed</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
                <input type="date" id="dateFilter" onchange="filterAppointments()" placeholder="Filter by date">
                <input type="text" id="searchFilter" onchange="filterAppointments()" placeholder="Search by patient name...">
            </div>

            <!-- Appointments Table -->
            <div class="appointments-table-container">
                <table class="appointments-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Patient Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Doctor</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="appointmentsTableBody">
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 2rem;">
                                <p style="color: var(--text-secondary); margin: 0;">Loading appointments...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Empty State -->
            <div id="emptyState" class="empty-state" style="display: none;">
                <div class="empty-state-icon">📭</div>
                <h3>No Appointments Found</h3>
                <p>There are no appointments matching your criteria.</p>
            </div>
        </main>
    </div>

    <!-- Appointment Details Modal -->
    <div id="appointmentModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Appointment Details</h2>
                <button class="close-modal" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body" id="modalBody">
                <!-- Content will be populated here -->
            </div>
            <div class="modal-actions">
                <button class="btn-confirm" onclick="updateStatus('confirmed')">✅ Confirm</button>
                <button class="btn-cancel" onclick="updateStatus('cancelled')">❌ Cancel</button>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="./js/auth.js"></script>
    <script>
        // Check admin login
        requireAdminLogin();

        let allAppointments = [];

        // Load appointments on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadAppointments();
        });

        /**
         * Load all appointments from API
         */
        function loadAppointments() {
            fetch('./api/appointments.php')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        allAppointments = data.data || [];
                        renderAppointments(allAppointments);
                        updateStatistics();
                    }
                })
                .catch(error => {
                    console.error('Error loading appointments:', error);
                    showTableError('Failed to load appointments');
                });
        }

        /**
         * Render appointments in table
         */
        function renderAppointments(appointments) {
            const tbody = document.getElementById('appointmentsTableBody');
            const emptyState = document.getElementById('emptyState');

            if (appointments.length === 0) {
                tbody.innerHTML = '';
                emptyState.style.display = 'block';
                return;
            }

            emptyState.style.display = 'none';
            tbody.innerHTML = appointments.map(apt => `
                <tr>
                    <td><strong>#${apt.id}</strong></td>
                    <td>${apt.patient_name}</td>
                    <td>${apt.patient_email}</td>
                    <td>${apt.patient_phone}</td>
                    <td>${apt.doctor}</td>
                    <td>${formatDate(apt.appointment_date)}</td>
                    <td>${apt.appointment_time}</td>
                    <td>
                        <span class="status-badge status-${apt.status}">
                            ${apt.status}
                        </span>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <button class="action-btn btn-view" onclick="viewDetails(${apt.id})">View</button>
                            ${apt.status === 'pending' ? `
                                <button class="action-btn btn-confirm" onclick="quickUpdate(${apt.id}, 'confirmed')">Confirm</button>
                            ` : ''}
                        </div>
                    </td>
                </tr>
            `).join('');
        }

        /**
         * View appointment details in modal
         */
        function viewDetails(id) {
            const appointment = allAppointments.find(a => a.id === id);
            if (!appointment) return;

            const modalBody = document.getElementById('modalBody');
            modalBody.innerHTML = `
                <div class="modal-field">
                    <label>Appointment ID</label>
                    <p>${appointment.id}</p>
                </div>
                <div class="modal-field">
                    <label>Patient Name</label>
                    <p>${appointment.patient_name}</p>
                </div>
                <div class="modal-field">
                    <label>Email</label>
                    <p><a href="mailto:${appointment.patient_email}" style="color: var(--primary-light);">${appointment.patient_email}</a></p>
                </div>
                <div class="modal-field">
                    <label>Phone</label>
                    <p><a href="tel:${appointment.patient_phone}" style="color: var(--primary-light);">${appointment.patient_phone}</a></p>
                </div>
                <div class="modal-field">
                    <label>Doctor</label>
                    <p>${appointment.doctor}</p>
                </div>
                <div class="modal-field">
                    <label>Appointment Date & Time</label>
                    <p>${formatDate(appointment.appointment_date)} at ${appointment.appointment_time}</p>
                </div>
                <div class="modal-field">
                    <label>Medical Notes</label>
                    <p>${appointment.message || 'No additional notes'}</p>
                </div>
                <div class="modal-field">
                    <label>Status</label>
                    <p>
                        <span class="status-badge status-${appointment.status}">
                            ${appointment.status.toUpperCase()}
                        </span>
                    </p>
                </div>
                <div class="modal-field">
                    <label>Booked On</label>
                    <p>${formatDateTime(appointment.created_at)}</p>
                </div>
            `;

            // Update modal actions based on status
            const modalActions = document.querySelector('.modal-actions');
            if (appointment.status === 'pending') {
                modalActions.innerHTML = `
                    <button class="btn-confirm" onclick="updateStatus('confirmed')">✅ Confirm</button>
                    <button class="btn-cancel" onclick="updateStatus('cancelled')">❌ Cancel</button>
                `;
            } else if (appointment.status === 'confirmed') {
                modalActions.innerHTML = `
                    <button class="btn-confirm" onclick="updateStatus('completed')">🏁 Mark as Completed</button>
                    <button class="btn-cancel" onclick="updateStatus('cancelled')">❌ Cancel</button>
                `;
            } else {
                modalActions.innerHTML = `
                    <button class="action-btn btn-view" onclick="closeModal()">Close</button>
                `;
            }

            // Store current appointment id for actions
            document.getElementById('appointmentModal').dataset.appointmentId = id;
            openModal();
        }

        /**
         * Quick update status
         */
        function quickUpdate(id, status) {
            updateAppointmentStatus(id, status);
        }

        /**
         * Update appointment status from modal
         */
        function updateStatus(status) {
            const id = document.getElementById('appointmentModal').dataset.appointmentId;
            updateAppointmentStatus(id, status);
        }

        /**
         * Update appointment status via API
         */
        function updateAppointmentStatus(id, status) {
            fetch(`./api/appointments.php?id=${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ status: status })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    showAlert(`✅ Appointment ${status} successfully!`, 'success');
                    loadAppointments();
                    closeModal();
                } else {
                    showAlert('Failed to update appointment', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('An error occurred', 'error');
            });
        }

        /**
         * Filter appointments
         */
        function filterAppointments() {
            const statusFilter = document.getElementById('statusFilter').value;
            const dateFilter = document.getElementById('dateFilter').value;
            const searchFilter = document.getElementById('searchFilter').value.toLowerCase();

            let filtered = allAppointments.filter(apt => {
                let match = true;

                if (statusFilter && apt.status !== statusFilter) {
                    match = false;
                }

                if (dateFilter && apt.appointment_date !== dateFilter) {
                    match = false;
                }

                if (searchFilter && !apt.patient_name.toLowerCase().includes(searchFilter)) {
                    match = false;
                }

                return match;
            });

            renderAppointments(filtered);
        }

        /**
         * Update statistics
         */
        function updateStatistics() {
            document.getElementById('total-count').textContent = allAppointments.length;
            document.getElementById('pending-count').textContent = allAppointments.filter(a => a.status === 'pending').length;
            document.getElementById('confirmed-count').textContent = allAppointments.filter(a => a.status === 'confirmed').length;
            document.getElementById('completed-count').textContent = allAppointments.filter(a => a.status === 'completed').length;
        }

        /**
         * Format date
         */
        function formatDate(dateStr) {
            const date = new Date(dateStr);
            return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
        }

        /**
         * Format date and time
         */
        function formatDateTime(dateStr) {
            const date = new Date(dateStr);
            return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
        }

        /**
         * Modal functions
         */
        function openModal() {
            document.getElementById('appointmentModal').classList.add('show');
        }

        function closeModal() {
            document.getElementById('appointmentModal').classList.remove('show');
        }

        // Close modal when clicking outside
        document.addEventListener('click', function(event) {
            const modal = document.getElementById('appointmentModal');
            if (event.target === modal) {
                closeModal();
            }
        });

        /**
         * Show alert message
         */
        function showAlert(message, type = 'info') {
            const alertDiv = document.createElement('div');
            alertDiv.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 1rem 1.5rem;
                border-radius: 8px;
                background-color: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6'};
                color: white;
                font-weight: 500;
                z-index: 9999;
                max-width: 400px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
                animation: slideIn 0.3s ease-out;
            `;

            alertDiv.textContent = message;
            document.body.appendChild(alertDiv);

            setTimeout(() => {
                alertDiv.style.animation = 'slideOut 0.3s ease-out';
                setTimeout(() => alertDiv.remove(), 300);
            }, 5000);
        }

        function showTableError(message) {
            const tbody = document.getElementById('appointmentsTableBody');
            tbody.innerHTML = `
                <tr>
                    <td colspan="9" style="text-align: center; color: var(--accent-red); padding: 2rem;">
                        ${message}
                    </td>
                </tr>
            `;
        }
    </script>
</body>
</html>
