# SEPS System Features Manual

## Document Information
- System Name: Suppliers Eligibility and Profiling System (SEPS)
- Version: Production build as of April 23, 2026
- Prepared For: End-users, Suppliers, TWG, and System Administrators

## Access Points
- Public Portal: https://seps.davaodelsur.gov.ph
- Admin Portal: https://seps.davaodelsur.gov.ph/admin/login
- Supplier Portal: https://seps.davaodelsur.gov.ph/supplier/login
- Supplier Registration: https://seps.davaodelsur.gov.ph/supplier/register

## User Roles Covered in this Manual
- System Administrator
- TWG
- Supplier
- End-user (limited access)

## Core Status Terms Used Across Modules
- Pending Review: Submitted and waiting for validation
- Validated: Approved as compliant
- Rejected: Reviewed but not compliant
- Expired: Previously valid document or status has lapsed

---

## Module 1: Suppliers Portal

### Objective
Allow suppliers to register, log in, set up their business profile, and keep company records updated.

### A. Supplier Account Registration
1. Open the Supplier Registration page.
2. Enter required account information:
   - First Name
   - Last Name
   - Contact Number
   - Email
   - Password and confirmation
3. Submit the registration form.
4. Check your email for account verification if prompted.
5. After successful registration, proceed to supplier login.

Screenshot placeholder:
- [Insert Screenshot 1.1 - Supplier Registration Form]

### B. Supplier Login
1. Open the Supplier Login page.
2. Enter registered email and password.
3. Complete multi-factor authentication if enabled.
4. Click Sign In.
5. On first login, proceed to My Business Profile.

Screenshot placeholder:
- [Insert Screenshot 1.2 - Supplier Login and MFA Prompt]

### C. Set Up Business Profile (My Business Profile)
1. In the supplier panel, open My Business Profile.
2. Click Setup Business Information.
3. Complete business details:
   - Business Name
   - Owner Name
   - Business Email
   - Website
   - Mobile/Landline
   - Supplier Type
4. Add business addresses:
   - Address label
   - Address lines
   - Province, Municipality, Barangay
   - Country and ZIP code
   - Site image upload
5. Add Line of Business entries (category and subcategory).
6. Save changes.
7. Confirm successful save notification.

Screenshot placeholders:
- [Insert Screenshot 1.3 - My Business Profile Overview]
- [Insert Screenshot 1.4 - Setup Business Information Modal]
- [Insert Screenshot 1.5 - Address and Line of Business Sections]

---

## Module 2: Eligibility Validation

### Objective
Assess supplier eligibility based on line of business and documentary compliance.

### A. Access LOB Validations (Admin/TWG)
1. Log in to the Admin portal.
2. Go to Suppliers > LOB Validations.
3. Select a review tab:
   - All Records
   - Pending Review
   - Validated
   - Rejected
   - Expired
4. Use search and status cues to prioritize records.

Screenshot placeholder:
- [Insert Screenshot 2.1 - LOB Validations Page with Tabs]

### B. Review Supplier Eligibility Status
1. Click View Status on a supplier row.
2. Review status history and remarks.
3. Verify completeness and compliance against requirements.

Screenshot placeholder:
- [Insert Screenshot 2.2 - View Status Slide-over]

### C. Update Eligibility Result
1. Click Update Status.
2. Select the new status (Pending Review, Validated, Rejected, Expired).
3. Enter remarks describing validation rationale.
4. Save changes.
5. Confirm success notification.

Important:
- LOB Reference Document must be configured in System Settings before status updates can proceed.

Screenshot placeholders:
- [Insert Screenshot 2.3 - Update Status Action]
- [Insert Screenshot 2.4 - Remarks and Save Confirmation]

---

## Module 3: Suppliers Documents Management

### Objective
Manage required supplier documentary submissions from upload to review.

### A. Configure Required Document Types (Admin)
1. In Admin panel, go to Administration > Documents.
2. Click Create Document.
3. Enter:
   - Document title
   - Description
   - Procurement type
   - Required/optional flag
4. Save the document template.

Screenshot placeholder:
- [Insert Screenshot 3.1 - Document Registry and Create Document]

### B. Supplier Upload or Replace Required Documents
1. Open a supplier record from View All Suppliers.
2. Go to the Supplier Documents relation section.
3. For each required item, click Attach/Replace Document.
4. Upload PDF file (max 5MB), set validity date if applicable, then save.
5. Verify successful upload message.

System behavior:
- New uploads are recorded with Pending Review status when submitted by supplier users.
- If uploaded by an administrator, status can be immediately marked as Validated.

Screenshot placeholders:
- [Insert Screenshot 3.2 - Supplier Documents Table]
- [Insert Screenshot 3.3 - Attach or Replace Document Modal]

### C. Review and Update Document Status (Admin/TWG)
1. Go to Suppliers > Supplier Attachments.
2. Filter by status tab or search by supplier/document.
3. Click View Document to inspect the uploaded PDF.
4. Click Update Status.
5. Set status and add remarks.
6. Save and confirm notification.

Screenshot placeholders:
- [Insert Screenshot 3.4 - Supplier Attachments List]
- [Insert Screenshot 3.5 - Update Attachment Status]

---

## Module 4: Administrator Review

### Objective
Provide centralized review actions for supplier profiling, assignment, and validation operations.

### A. Review Supplier Registry
1. Open Suppliers > View All Suppliers.
2. Review supplier summary details and status badges.
3. Use filters:
   - Supplier type
   - Eligibility status
   - Line of business
4. Open supplier details for deeper inspection.

Screenshot placeholder:
- [Insert Screenshot 4.1 - Official Supplier Registry]

### B. Assign Supplier to a User Account
1. In a supplier row action menu, click Assign/Edit User Account.
2. Select the user account to link.
3. Save changes.
4. Confirm assignment notification.

Screenshot placeholder:
- [Insert Screenshot 4.2 - Assign Supplier to User Modal]

### C. Edit Supplier Records and Supporting Data
1. Use Edit action on supplier rows.
2. Update profile information as needed.
3. Save updates.
4. Verify changes reflect in table and profile views.

Screenshot placeholder:
- [Insert Screenshot 4.3 - Edit Supplier Form]

---

## Module 5: Alerts and Notifications

### Objective
Notify stakeholders about document deadlines, expirations, and review outcomes.

### A. Configure Notification Lead Time (Admin)
1. Go to Administration > System Settings.
2. Set Document Expiry Notification Days (30, 15, or 7).
3. Save settings.

Screenshot placeholder:
- [Insert Screenshot 5.1 - System Settings Notification Configuration]

### B. Automatic Expiry Processing (System)
1. The scheduled process scans attachments by validity date.
2. Documents past validity are marked Expired.
3. Suppliers receive reminder notifications for near-expiry documents.
4. Administrator receives summary notification for documents expired today.

Operational note:
- Queue worker and scheduler should be active in production.

Screenshot placeholder:
- [Insert Screenshot 5.2 - Database Notifications Panel]

### C. User Notification Access
1. Click the notifications bell icon in the panel header.
2. Open unread notifications.
3. Use provided action links (for example, View Documents) to navigate directly to affected records.

Screenshot placeholder:
- [Insert Screenshot 5.3 - Notification Action Link Workflow]

---

## Module 6: Reporting and Analytics

### Objective
Provide operational insights through dashboard KPIs and trend charts.

### A. Open Dashboard Analytics (Admin/TWG)
1. Log in to Admin panel.
2. Open Dashboard.
3. Review top-level widgets:
   - Suppliers Overview
   - Documents Overview
   - Site Validations Overview

Screenshot placeholder:
- [Insert Screenshot 6.1 - Admin Dashboard Widgets]

### B. Interpret Supplier KPIs
1. Read metrics for:
   - Total Suppliers
   - Eligible Suppliers
   - Ineligible Suppliers
2. Review trend sparkline charts for recent periods.

Screenshot placeholder:
- [Insert Screenshot 6.2 - Suppliers Overview Breakdown]

### C. Interpret Document and Validation Trends
1. In Documents Overview, inspect totals by status.
2. In Site Validations chart, select year filter.
3. Compare monthly bar values for validation activity.

Screenshot placeholders:
- [Insert Screenshot 6.3 - Documents Overview by Status]
- [Insert Screenshot 6.4 - Site Validations Yearly Chart]

---

## Module 7: User Access Management

### Objective
Control system access, role assignments, and account state.

### A. Manage System Users (Admin)
1. Open Accounts > System Users.
2. Click Create User to add accounts.
3. Complete identity and contact fields.
4. Assign user role.
5. Save user record.

Screenshot placeholder:
- [Insert Screenshot 7.1 - System Users Registry]

### B. Update User Role/Status and Profile
1. Open an existing user and click Edit.
2. Update role, account status, and profile data as needed.
3. Save changes.
4. Confirm that list badges and role-specific descriptions are updated.

Screenshot placeholder:
- [Insert Screenshot 7.2 - Edit User Form]

### C. Security Controls in Access Flow
1. Ensure users verify email addresses during onboarding.
2. Enforce multi-factor authentication where required.
3. For inactive users, block login and route support requests to administrator.

Screenshot placeholder:
- [Insert Screenshot 7.3 - Login Inactive Account Message / MFA]

---

## Module 8: Audit and Compliance

### Objective
Maintain traceable records of critical system actions for accountability and compliance.

### A. Supplier Activity Logs
1. Go to Suppliers > View All Suppliers.
2. In row actions, click View Activity Logs.
3. Review event timeline for creation, updates, status changes, and document actions.

Screenshot placeholder:
- [Insert Screenshot 8.1 - Supplier Activity Logs]

### B. User Activity Logs
1. Go to Accounts > System Users.
2. Select a user and open View Activity Logs.
3. Review login-related, profile, role, and status change records.

Screenshot placeholder:
- [Insert Screenshot 8.2 - User Activity Logs]

### C. Compliance Review Procedure
1. Confirm each supplier has complete documentary submissions.
2. Validate that status changes include reviewer remarks.
3. Verify audit records exist for each key administrative action.
4. Export or archive reports as required by internal compliance policy.

Screenshot placeholder:
- [Insert Screenshot 8.3 - Compliance Checklist Evidence]

---

## Module 9: AI Chatbot Support

### Objective
Provide instant guided help and FAQ assistance through embedded AI chat.

### A. Access Chatbot
1. Open Public, Admin, or Supplier portal page.
2. Locate the floating chat button (SEPS Assistant).
3. Click to open chat window.

Screenshot placeholder:
- [Insert Screenshot 9.1 - Chatbot Floating Button]

### B. Use Suggested Prompts
1. Select a starter prompt (for example, supplier submission or validation FAQ).
2. Review generated response.
3. Ask follow-up questions for more details.

Screenshot placeholder:
- [Insert Screenshot 9.2 - Chatbot Starter Prompts]

### C. Best Practice for Support
1. Use chatbot for process guidance and FAQs.
2. For account-specific or approval-specific issues, escalate to system administrator.
3. Record recurring questions for future knowledge base updates.

Screenshot placeholder:
- [Insert Screenshot 9.3 - Chatbot Conversation Example]

---

## Suggested Screenshot Naming Convention
Use this structure to keep documentation assets organized:
- M1-01-Supplier-Registration.png
- M2-01-LOB-Validation-Tabs.png
- M3-01-Document-Attach-Modal.png
- M4-01-Supplier-Registry.png
- M5-01-System-Settings-Notifications.png
- M6-01-Admin-Dashboard-Analytics.png
- M7-01-System-Users.png
- M8-01-Audit-Logs.png
- M9-01-AI-Chatbot.png

## Quick Troubleshooting Notes
- Cannot update LOB status:
  - Ensure Line of Business Reference Document is configured in System Settings.
- No notification delivery:
  - Confirm queue worker is running and scheduler is active.
- Upload rejected:
  - Verify file is PDF and does not exceed configured upload size.
- Login denied:
  - Check account status (must be active) and complete MFA challenge.

## Approval and Revision Log
- Version: 1.0
- Revision Date: April 23, 2026
- Author: System-generated draft for SEPS
- Next Step: Insert validated UI screenshots per placeholder sections and submit for stakeholder review
