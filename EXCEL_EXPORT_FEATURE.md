# Excel Export Feature for Appointments

## Overview
The hospital management system now includes comprehensive Excel/CSV export functionality for appointments. Both patients and administrators can download their appointments data in a spreadsheet format.

## Features

### 1. Patient Appointments Export
**Location:** My Appointments Page (`my-appointments.html`)

#### How to Use:
1. Log in as a patient
2. Navigate to "My Appointments"
3. Click the **"📥 Download as Excel"** button
4. The file will be automatically downloaded with the format: `{PatientName}_Appointments_YYYY-MM-DD.xlsx`

#### What's Included:
- Patient Name
- Email
- Phone Number
- Doctor Name
- Appointment Date
- Appointment Time
- Medical Notes (if any)

#### Technology:
- Uses **SheetJS (XLSX.js)** library for client-side Excel generation
- No server processing required
- Works with localStorage appointments

---

### 2. Admin Appointments Export
**Location:** Admin Appointments Management (`admin-appointments.php`)

#### How to Use:
1. Log in as an admin
2. Navigate to "📅 Appointments Management"
3. Use the filter controls to narrow down appointments (optional):
   - Filter by Status (Pending, Confirmed, Completed, Cancelled)
   - Filter by Date
   - Search by Patient Name
4. Click the **"📥 Export Excel"** button
5. The file will be automatically downloaded with the format: `Hospital_Appointments_YYYY-MM-DD.xlsx`

#### What's Included:
- Appointment ID
- Patient Name
- Email
- Phone Number
- Doctor Name
- Appointment Date
- Appointment Time
- Status (Pending/Confirmed/Completed/Cancelled)
- Booked Date & Time
- Medical Notes

#### Technology:
- Uses **SheetJS (XLSX.js)** library for client-side Excel generation
- Exports currently visible/filtered appointments
- Dynamic filtering applied before export

---

### 3. Server-Side Export API (Optional)
**Endpoint:** `/api/export-appointments.php`

#### Usage:
```
GET /api/export-appointments.php?action=export&status=confirmed&date=2024-12-25&search=john
```

#### Query Parameters:
- `action=export` (Required) - Must be "export" to trigger export
- `status` (Optional) - Filter by appointment status: `pending`, `confirmed`, `completed`, `cancelled`
- `date` (Optional) - Filter by date in format YYYY-MM-DD
- `search` (Optional) - Search by patient name or email

#### Response:
- Returns a CSV file with comma-separated values
- Can be opened in Excel, Google Sheets, or any spreadsheet application
- UTF-8 encoded with BOM for proper character encoding

#### Example:
```
// Export all confirmed appointments for a specific date
/api/export-appointments.php?action=export&status=confirmed&date=2024-12-25

// Export all appointments for patient "John"
/api/export-appointments.php?action=export&search=john

// Export all pending appointments
/api/export-appointments.php?action=export&status=pending
```

---

## File Formats

### Excel Format (.xlsx)
- Client-side generation using SheetJS
- Supports:
  - Multiple sheets
  - Formatted headers
  - Adjusted column widths
  - Compatible with Excel, Google Sheets, LibreOffice

### CSV Format (.csv)
- Server-side generation
- Standard comma-separated values
- UTF-8 encoded with BOM
- Compatible with all spreadsheet applications

---

## Technical Details

### Client-Side Export (Patient & Admin Pages)
**Library:** SheetJS (XLSX.js)
- **CDN:** `https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.min.js`
- **Function:** Converts JSON data to XLSX format
- **Browser Support:** All modern browsers

### Server-Side Export (API)
**Technology:** PHP with PDO
- Reads from appointments database
- Applies filters via prepared statements (SQL injection safe)
- Generates CSV with proper escaping
- Returns file with appropriate headers

---

## Security Features

1. **Admin-Only Access:** Export functionality requires admin authentication
2. **Patient-Only Access:** Patients can only export their own appointments
3. **SQL Injection Protection:** Server-side API uses prepared statements
4. **XSS Protection:** Data properly escaped in output
5. **CSRF Protection:** Integrated with existing authentication system

---

## File Naming Convention

### Patient Export
`{PatientName}_Appointments_{YYYY-MM-DD}.xlsx`

Example: `John_Doe_Appointments_2024-12-25.xlsx`

### Admin Export
`Hospital_Appointments_{YYYY-MM-DD}.xlsx`

Example: `Hospital_Appointments_2024-12-25.xlsx`

### API Export
`Hospital_Appointments_{YYYY-MM-DD_HH-MM-SS}.csv`

Example: `Hospital_Appointments_2024-12-25_14-30-45.csv`

---

## Troubleshooting

### Export Button Not Visible
1. Ensure you're logged in with appropriate role (patient or admin)
2. Check if you have at least one appointment
3. Clear browser cache and reload

### Downloaded File is Empty
1. Verify that appointments exist in the system
2. Check that filters are not too restrictive
3. For server-side export, ensure database connection is working

### File Won't Open in Excel
1. Check file extension (.xlsx or .csv)
2. Try opening with Google Sheets if native Excel has issues
3. Verify file is not corrupted by checking file size

### Character Encoding Issues
1. Ensure you're using UTF-8 compatible spreadsheet application
2. Server-side export includes BOM for proper character recognition
3. Try opening with LibreOffice if Excel has encoding issues

---

## Future Enhancements

Potential improvements for future versions:
- [ ] Support for multiple export formats (PDF, XML)
- [ ] Batch scheduling of exports
- [ ] Email exports directly to patient inbox
- [ ] Advanced filtering and sorting options
- [ ] Custom report templates
- [ ] Export history and audit logs
- [ ] Export to cloud storage (Google Drive, OneDrive)

---

## Support

For issues or feature requests related to Excel export functionality:
1. Check this documentation first
2. Contact system administrator
3. Report bugs with details about steps to reproduce

---

**Version:** 1.0  
**Last Updated:** December 2024  
**Compatibility:** PHP 7.4+, Modern Browsers
