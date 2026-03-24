# PHP Backend Setup & Documentation

## Overview
This hospital website now includes a complete PHP backend for managing patient appointments with a responsive admin interface. The system stores appointments in an SQLite database and provides RESTful API endpoints for CRUD operations.

## Features Implemented

### 1. **Form Handling**
- ✅ Appointment booking form with validation
- ✅ Contact form submission with database storage
- ✅ Real-time form validation
- ✅ Success/error notifications with toast alerts

### 2. **PHP Database Storage**
- ✅ SQLite database (no external setup required)
- ✅ Automatic database initialization with table creation
- ✅ Sample doctor data pre-populated
- ✅ Secure input validation and prepared statements

### 3. **Responsive UI**
- ✅ Mobile-optimized appointment booking form
- ✅ Responsive admin dashboard with sidebar
- ✅ Modern card-based layout
- ✅ Touch-friendly buttons and controls
- ✅ Adaptive grid layout for all screen sizes

## Directory Structure

```
hospital-website/
├── config.php                      # Database configuration & initialization
├── admin-appointments.php          # Admin appointments management page
├── api/
│   ├── appointments.php            # Appointments API endpoints (GET, POST, PUT, DELETE)
│   ├── contact.php                 # Contact form handler
│   └── doctors.php                 # Doctors list API endpoint
├── data/
│   └── hospital.db                 # SQLite database (auto-created)
├── js/
│   ├── appointments.js             # Appointment form handler
│   ├── contact.js                  # Contact form handler
│   ├── script.js                   # Main scripts
│   └── auth.js                     # Authentication helper
└── appointments.html               # Appointment booking page
└── contact.html                    # Contact page
```

## API Endpoints

### Appointments Endpoint: `/api/appointments.php`

#### GET - Retrieve Appointments
```bash
# Get all appointments
GET /api/appointments.php

# Get specific appointment
GET /api/appointments.php?id=1

# Filter by status
GET /api/appointments.php?filter=pending
```

**Response:**
```json
{
  "status": "success",
  "count": 5,
  "data": [
    {
      "id": 1,
      "patient_name": "John Doe",
      "patient_email": "john@example.com",
      "patient_phone": "+1-555-0123",
      "doctor": "Dr. Sarah Mitchell",
      "appointment_date": "2024-04-15",
      "appointment_time": "14:30",
      "message": "Initial consultation",
      "status": "pending",
      "created_at": "2024-03-24T10:30:00"
    }
  ]
}
```

#### POST - Create New Appointment
```bash
POST /api/appointments.php
Content-Type: application/json

{
  "patient_name": "Jane Smith",
  "patient_email": "jane@example.com",
  "patient_phone": "+1-555-0456",
  "doctor": "Dr. James Chen",
  "appointment_date": "2024-04-20",
  "appointment_time": "15:00",
  "message": "Follow-up visit"
}
```

**Response:**
```json
{
  "status": "success",
  "message": "Appointment created successfully",
  "id": 6,
  "appointment": {
    "id": 6,
    "patient_name": "Jane Smith",
    "patient_email": "jane@example.com",
    "status": "pending"
  }
}
```

#### PUT - Update Appointment
```bash
PUT /api/appointments.php?id=1
Content-Type: application/json

{
  "status": "confirmed"
}
```

**Response:**
```json
{
  "status": "success",
  "message": "Appointment updated successfully"
}
```

#### DELETE - Cancel Appointment
```bash
DELETE /api/appointments.php?id=1
```

**Response:**
```json
{
  "status": "success",
  "message": "Appointment deleted successfully"
}
```

### Doctors Endpoint: `/api/doctors.php`

#### GET - Retrieve All Doctors
```bash
GET /api/doctors.php
```

**Response:**
```json
{
  "status": "success",
  "count": 6,
  "data": [
    {
      "id": 1,
      "name": "Dr. Sarah Mitchell",
      "specialty": "Cardiology",
      "email": "sarah.mitchell@riyamedicare.com",
      "phone": "+1-555-0201"
    }
  ]
}
```

### Contact Endpoint: `/api/contact.php`

#### POST - Submit Contact Form
```bash
POST /api/contact.php
Content-Type: application/json

{
  "full-name": "Robert Johnson",
  "email": "robert@example.com",
  "phone": "+1-555-0789",
  "subject": "Inquiry about services",
  "message": "I would like to know more about your cardiology services."
}
```

**Response:**
```json
{
  "status": "success",
  "message": "Thank you for contacting us! We will get back to you soon."
}
```

## Database Schema

### Appointments Table
```sql
CREATE TABLE appointments (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    patient_name TEXT NOT NULL,
    patient_email TEXT NOT NULL,
    patient_phone TEXT NOT NULL,
    doctor TEXT NOT NULL,
    appointment_date TEXT NOT NULL,
    appointment_time TEXT NOT NULL,
    message TEXT,
    status TEXT DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
)
```

### Doctors Table
```sql
CREATE TABLE doctors (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    specialty TEXT NOT NULL,
    email TEXT NOT NULL,
    phone TEXT NOT NULL,
    available INTEGER DEFAULT 1
)
```

### Contacts Table
```sql
CREATE TABLE contacts (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    full_name TEXT NOT NULL,
    email TEXT NOT NULL,
    phone TEXT NOT NULL,
    subject TEXT NOT NULL,
    message TEXT NOT NULL,
    status TEXT DEFAULT 'unread',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)
```

## Key Features

### 1. **Appointment Management**
- Patients can book appointments with automatic form validation
- Admin can view all appointments with filters by status and date
- Update appointment status (pending → confirmed → completed)
- Cancel appointments
- View detailed appointment information
- Real-time statistics dashboard

### 2. **Form Validation**
- Client-side validation in JavaScript
- Server-side validation in PHP
- Email format validation
- Required field checks
- Detailed error messages

### 3. **Security**
- Prepared statements to prevent SQL injection
- Input sanitization
- CORS headers configured
- Error handling with appropriate HTTP status codes

### 4. **Responsive Design**
- Mobile-first approach
- Breakpoints for tablets and desktops
- Touch-friendly interface
- Adaptive grid layouts
- Hamburger menu for mobile navigation

## Frontend Integration

### Appointment Booking Form
The form automatically:
1. Loads doctors from the API
2. Validates input before submission
3. Sends data to PHP backend
4. Displays success/error notifications
5. Resets form on successful submission
6. Stores backup in localStorage

### Contact Form
The form:
1. Validates all required fields
2. Sends data to contact API
3. Displays confirmation message
4. Stores contact data in database

## Admin Dashboard

Access the admin appointments management at: `./admin-appointments.php`

### Features:
- **Dashboard**: View appointment statistics
- **Appointments List**: Searchable, filterable table of all appointments
- **Status Management**: Quickly update appointment status
- **Detailed View**: Modal popup with full appointment details
- **Quick Actions**: Confirm, cancel, or mark as completed

### Filters Available:
- By Status (Pending, Confirmed, Completed, Cancelled)
- By Date
- By Patient Name

## PHP Requirements

- PHP 5.3.0 or higher
- PDO extension enabled (usually default)
- SQLite3 extension enabled (usually default)
- Write permissions on the `/data` directory

## Configuration

The system is configured in `config.php`:

```php
// Database file location
define('DB_PATH', __DIR__ . '/data/hospital.db');

// Database initialization automatically creates tables on first run
// Sample doctors are pre-populated
```

## Error Handling

All API endpoints return appropriate HTTP status codes:
- **200**: Success (GET, PUT)
- **201**: Created (POST)
- **400**: Bad Request (validation errors)
- **404**: Not Found
- **405**: Method Not Allowed
- **500**: Server Error

## Usage Examples

### Submit Appointment via JavaScript
```javascript
const appointmentData = {
    patient_name: "John Doe",
    patient_email: "john@example.com",
    patient_phone: "+1-555-0123",
    doctor: "Dr. Sarah Mitchell",
    appointment_date: "2024-04-15",
    appointment_time: "14:30",
    message: "Initial consultation"
};

fetch('./api/appointments.php', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json'
    },
    body: JSON.stringify(appointmentData)
})
.then(response => response.json())
.then(data => console.log(data));
```

### Update Appointment Status
```javascript
fetch('./api/appointments.php?id=1', {
    method: 'PUT',
    headers: {
        'Content-Type': 'application/json'
    },
    body: JSON.stringify({ status: 'confirmed' })
})
.then(response => response.json())
.then(data => console.log(data));
```

## Appointment Status Values

- **pending**: Appointment just booked, awaiting confirmation
- **confirmed**: Admin confirmed the appointment
- **completed**: Appointment has been completed
- **cancelled**: Appointment was cancelled

## Data Persistence

All appointment and contact data is persisted in the SQLite database located at `/data/hospital.db`. The database is automatically created on the first access.

## Notes

- The system maintains backward compatibility with localStorage for appointments
- All timestamps are stored as ISO 8601 format
- Doctors list is pre-populated with sample data on first initialization
- Database automatically initializes on first API call to any endpoint
- No external dependencies required - uses PHP's built-in PDO and SQLite

## Troubleshooting

### Database not creating
- Ensure `/data` directory exists and is writable
- Check PHP permissions and error logs

### API returning errors
- Verify JSON in request body is valid
- Check that required fields are provided
- Review error message in response

### Forms not submitting
- Check browser console for JavaScript errors
- Verify API endpoints are accessible
- Ensure proper CORS headers are configured

---

**Version**: 1.0.0  
**Last Updated**: 2024-03-24  
**Created for**: RIYA MEDICARE Hospital Website
