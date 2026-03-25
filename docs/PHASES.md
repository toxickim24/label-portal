# McMullen Properties Client Portal - Project Phases & Roadmap

## Project Overview

The McMullen Properties Client Portal is a centralized real estate client portal and internal CRM system designed to replace and improve the Compass experience. The system is being built in 7 carefully planned phases, with strict phase-by-phase development.

**⚠️ CRITICAL DEVELOPMENT RULES:**
- Build ONE phase at a time
- DO NOT skip phases
- DO NOT combine phases
- STOP after each phase for review and approval

---

## Phase 1: Foundation & User Management ✅ COMPLETED

**Status**: ✅ Complete
**Completion Date**: March 23, 2026
**Version**: 1.0.0

### Objectives
Establish the core foundation of the application with authentication, user management, and basic administrative features that will support all future phases.

### Completed Features

#### Authentication System ✅
- [x] User registration
- [x] Login/logout functionality
- [x] Password reset and recovery
- [x] Email verification workflow
- [x] Remember me functionality
- [x] Session management
- [x] Protected routes with middleware

#### Registration Approval Workflow ✅
- [x] New users start in "pending" status
- [x] Admin approval required before access
- [x] Email notifications on approval (UserApprovedMail)
- [x] Rejection capability with reason
- [x] User suspension/unsuspension
- [x] Status tracking (pending, approved, suspended)

#### Roles & Permissions (Spatie) ✅
- [x] Spatie Laravel Permission integration
- [x] Three default roles:
  - Admin (full system access)
  - Manager (limited admin capabilities)
  - User (standard access)
- [x] Role assignment during user creation
- [x] Permission-based authorization
- [x] Policy-based access control
- [x] Middleware route protection
- [x] Role-specific navigation menus

#### User Management ✅
- [x] Complete CRUD operations
- [x] User listing with pagination (15 per page)
- [x] Advanced filtering:
  - By status (pending, approved, suspended)
  - By role (admin, manager, user)
  - By search (name, email)
  - Show/hide deleted users
- [x] User approval actions
- [x] User suspension actions
- [x] Soft delete with restore capability
- [x] User statistics dashboard
- [x] User detail view with activity history

#### Branding Settings ✅
- [x] Custom application name
- [x] Logo upload system:
  - Light theme logo (label-logo.png)
  - Dark theme logo (label-white-logo.png)
  - Favicon (label-white-favicon.png)
- [x] Logo preview before upload
- [x] Reset to default logos
- [x] Settings persistence in database
- [x] Admin-only settings access

#### Theme System ✅
- [x] Light/dark mode support
- [x] Theme-aware logo switching
- [x] Automatic theme detection
- [x] Consistent styling across all pages
- [x] Tailwind CSS dark mode classes

#### Logo System ✅
- [x] Logos properly organized in `/public/images/logos`
- [x] Clean naming convention (hyphens, not spaces)
- [x] Logo usage in:
  - Auth pages (login, register)
  - Dashboard navigation
  - Admin panel
- [x] Favicon properly set in HTML head
- [x] Default fallback system
- [x] Admin override capability via upload

#### Activity Logging ✅
- [x] Spatie Activity Log integration
- [x] Comprehensive tracking:
  - User creation, updates, deletion
  - User approval/suspension
  - Role changes
  - Login/logout events
- [x] Activity history per user
- [x] Causer and subject tracking
- [x] Event-based logging

#### UI/UX ✅
- [x] Clean SaaS-style interface
- [x] Premium look and feel
- [x] Sidebar + topbar layout
- [x] Fully responsive design
- [x] Beautiful data tables
- [x] Consistent spacing and padding
- [x] Mobile-optimized views
- [x] Dark mode support throughout

### Technical Achievements
- ✅ Service Layer architecture (UserService, SettingsService)
- ✅ Thin controllers
- ✅ Form request validation
- ✅ Policy-based authorization
- ✅ Eloquent relationships
- ✅ SoftDeletes implementation
- ✅ Database indexing
- ✅ Scalable, maintainable code structure

### Database Schema
- users (with approval fields)
- roles and permissions (Spatie)
- model_has_roles
- model_has_permissions
- role_has_permissions
- settings
- activity_log
- Standard Laravel tables (cache, jobs, sessions, etc.)

### Default Credentials
- **Email**: admin@labelsalesagents.com
- **Password**: Thelabel99!

---

## Phase 2: CRM Contacts ✅ COMPLETED

**Status**: ✅ Complete
**Completion Date**: March 24, 2026
**Priority**: High

### Objectives
Build a comprehensive CRM contact management system for managing real estate clients, leads, and prospects with advanced organization and tracking capabilities.

### Completed Features

#### Contact Management Core ✅
- [x] Full CRUD operations for contacts
- [x] Contact fields:
  - [x] Basic info (name, email, phone, address)
  - [x] Contact type (buyer, seller, investor, etc.)
  - [x] Status/stage (lead, prospect, active, closed, archived)
  - [x] Source (referral, website, open house, etc.)
  - [x] Priority level
  - [x] Date added, last contact date
- [x] Contact detail view
- [x] Contact editing with validation
- [x] Soft delete with trash management
- [x] Restore deleted contacts
- [x] Permanent delete option

#### Notes System ✅
- [x] Add notes to contacts
- [x] Note CRUD operations
- [x] Rich text editor for notes
- [x] Note timestamps and author tracking
- [x] Pin important notes
- [x] Note history/timeline
- [ ] File attachments to notes (not implemented)

#### Tagging System ✅
- [x] Create custom tags
- [x] Assign multiple tags to contacts
- [x] Tag management (create, edit, delete, merge)
- [x] Tag colors/categories
- [x] Filter contacts by tags
- [x] Tag autocomplete
- [x] Popular tags display

#### Search & Filtering ✅
- [x] Global contact search
- [x] Search by:
  - [x] Name
  - [x] Email
  - [x] Phone
  - [x] Address
  - [x] Tags
  - [x] Notes content
- [x] Advanced filters:
  - [x] Contact type
  - [x] Status/stage
  - [x] Source
  - [x] Priority
  - [x] Date ranges
  - [x] Assigned agent
  - [x] Tags
- [x] Save filter presets
- [x] Quick filter chips

#### Pagination & Display ✅
- [x] Configurable items per page
- [x] Efficient pagination
- [x] List view with sortable columns
- [x] Grid/card view option
- [x] Export filtered results to CSV
- [x] Bulk selection
- [x] Bulk actions (tag, delete, assign, etc.)

#### Agent Assignment ✅
- [x] Assign contacts to specific agents
- [x] Reassign contacts
- [x] View agent's contact portfolio
- [x] Filter by assigned agent
- [x] Unassigned contacts view
- [x] Assignment history tracking
- [x] Email notifications on assignment (ContactAssignedMail)

#### Pipeline/Status Management ✅
- [x] Customizable pipeline stages:
  - [x] Lead
  - [x] Contacted
  - [x] Qualified
  - [x] Showing
  - [x] Offer
  - [x] Contract
  - [x] Closed
  - [x] Lost
- [x] Drag-and-drop stage changes (Kanban board view)
- [x] Stage history tracking
- [x] Auto-update last contact date on stage change
- [x] Stage-based filtering
- [x] Pipeline analytics (count per stage)

### Database Schema
```
contacts:
  - id
  - first_name
  - last_name
  - email
  - phone
  - address
  - city, state, zip
  - contact_type (enum or string)
  - status/stage
  - source
  - priority
  - assigned_to (user_id)
  - last_contact_date
  - timestamps
  - soft deletes

contact_notes:
  - id
  - contact_id
  - user_id (author)
  - content (text)
  - is_pinned
  - timestamps

contact_tags:
  - id
  - name
  - color
  - timestamps

contact_tag (pivot):
  - contact_id
  - tag_id
```

### Service Layer
- ContactService (CRUD, search, filtering)
- ContactNoteService (note management)
- ContactTagService (tag management)

### UI Components
- ContactList.vue
- ContactCard.vue
- ContactForm.vue
- ContactDetail.vue
- ContactNotes.vue
- ContactTags.vue
- PipelineBoard.vue (Kanban)
- ContactFilters.vue
- ContactExport.vue

### Security & Policies
- ContactPolicy (view, create, update, delete, assign)
- Only assigned agent + admins can edit
- Activity logging for all contact actions

---

## Phase 3: CSV Import Manager ✅ COMPLETED

**Status**: ✅ Complete
**Completion Date**: March 25, 2026
**Priority**: High
**Purpose**: Import existing Compass CRM data into McMullen Properties Portal

### Objectives
Build a robust CSV import system to migrate existing client data from Compass or other sources, with intelligent field mapping and duplicate handling.

### Completed Features

#### File Upload & Validation ✅
- [x] CSV file upload interface
- [x] File size validation (max 10MB)
- [x] CSV format validation
- [x] Encoding detection (UTF-8, etc.)
- [x] Preview first 10 rows
- [x] Row count display

#### Header Detection ✅
- [x] Automatic header detection
- [x] Show detected headers
- [x] Option to specify header row
- [x] Handle files with/without headers

#### Header Mapping UI ✅
- [x] Interactive mapping interface
- [x] Drag-and-drop or dropdown mapping
- [x] Map CSV columns to database fields:
  - [x] First Name → first_name
  - [x] Last Name → last_name
  - [x] Email → email
  - [x] Phone → phone
  - [x] Address → address
  - [x] City → city
  - [x] State → state
  - [x] Zip → zip
  - [x] Status → status
  - [x] Type → contact_type
  - [x] Source → source
  - [x] Tags → tags (comma-separated)
  - [x] Notes → notes
- [x] Visual mapping confirmation
- [x] Unmapped field warnings

#### Alias Matching ✅
- [x] Common alias recognition:
  - [x] "First Name" = "FirstName" = "fname" = "Given Name"
  - [x] "Email" = "E-mail" = "Email Address"
  - [x] "Phone" = "Tel" = "Telephone" = "Mobile"
  - [x] etc.
- [x] Suggest mappings based on aliases
- [x] Custom alias definitions
- [x] Save mapping templates for reuse

#### Preview & Validation ✅
- [x] Preview mapped data (first 10 rows)
- [x] Show field transformations
- [x] Validation rules per field:
  - [x] Email format
  - [x] Phone format
  - [x] Required fields
  - [x] Data type matching
- [x] Error highlighting
- [x] Warning for suspicious data
- [x] Summary of valid/invalid rows

#### Import Process ✅
- [x] Background job for large imports
- [x] Progress bar with percentage
- [x] Real-time import status
- [x] Row-by-row processing
- [x] Error handling per row
- [x] Success/failure counters
- [x] Estimated time remaining

#### Duplicate Handling ✅
- [x] Detect duplicates by:
  - [x] Email (primary)
  - [x] Phone
  - [x] Name + Address combination
- [x] Duplicate resolution options:
  - [x] **Skip**: Keep existing, ignore import row
  - [x] **Update**: Overwrite existing with import data
  - [x] **Merge**: Combine data intelligently (keep non-empty fields)
- [x] Bulk duplicate action selection
- [x] Preview before applying duplicate actions
- [x] Duplicate detection report

#### Import Logs ✅
- [x] Import history table
- [x] Log entry per import with:
  - [x] Timestamp
  - [x] File name
  - [x] User who imported
  - [x] Total rows processed
  - [x] Successful imports
  - [x] Failed imports
  - [x] Skipped (duplicates)
  - [x] Processing time
- [x] Detailed error logs
- [x] View import log details
- [x] Filter logs by user/date

#### Download Options ✅
- [x] Download blank CSV template
- [x] Template with example data
- [x] Template with all field options
- [x] Download failed rows as CSV:
  - [x] Include original data
  - [x] Include error messages
  - [x] Allow fixing and re-importing

### Database Schema
```
imports:
  - id
  - user_id
  - filename
  - total_rows
  - successful_rows
  - failed_rows
  - skipped_rows
  - status (pending, processing, completed, failed)
  - started_at
  - completed_at
  - timestamps

import_errors:
  - id
  - import_id
  - row_number
  - row_data (JSON)
  - error_message
  - timestamps
```

### Service Layer
- ContactImportService (upload, parse, validate, import)
- CSVParserService (read, detect headers, parse)
- DuplicateDetectionService (find and handle duplicates)

### UI Components
- ImportUpload.vue
- ImportMapping.vue
- ImportPreview.vue
- ImportProgress.vue
- ImportHistory.vue
- DuplicateResolver.vue

### Technical Requirements
- Queue jobs for background processing
- Chunked CSV reading for large files
- Transaction support (rollback on critical errors)
- Memory-efficient processing
- Progress tracking in cache/database

---

## Phase 4: Client Portal ✅ COMPLETED

**Status**: ✅ Complete - Production ready
**Started**: March 25, 2026
**Completed**: March 25, 2026
**Version**: 4.0.0
**Priority**: Medium-High

### Objectives
Create a dedicated client-facing portal where real estate clients can log in, view their information, and communicate with their agents through an invite-only system.

### Features

#### Client Authentication ✅
- [x] Client role with restricted permissions (access-client-portal, view-own-profile, edit-own-profile, view-own-activity, view-own-notifications)
- [x] Invite-only registration (no public registration)
- [x] Admin/agent invitation system with email invitations
- [x] Secure signed URLs for invitations (7-day expiry)
- [x] Client password setup during invitation acceptance
- [x] Client email auto-verified on invitation
- [x] Separate client portal routes with role:client middleware
- [x] Redirect logic for clients vs admins
- [x] Client password reset flow with branded emails (PasswordResetMail)
- [ ] Additional client authentication middleware/guards (if needed)

#### Client Dashboard ✅
- [x] Personalized welcome message (time-based greeting: Good Morning/Afternoon/Evening)
- [x] Quick stats overview cards:
  - [x] Account Status (active/pending)
  - [x] Notifications count (0 unread)
  - [x] Recent Activities count (0 activities)
- [x] Getting Started guide with feature descriptions
- [x] Clean, modern UI with dark mode support
- [x] Responsive mobile layout
- [ ] Saved properties (skipped - property management not built yet)
- [ ] Upcoming appointments (skipped - not in scope)
- [ ] Agent contact info widget (basic version in profile page)

#### View Client Data ✅
- [x] View profile information (name, email)
- [x] View contact details (email, joined date)
- [x] Profile card with user avatar placeholder
- [x] Account information display (Member Since, Email Verified status)
- [x] Contact agent card with email link
- [x] View tags/categories (displayed with color-coded badges in profile)
- [x] View pipeline status (status displayed in profile)
- [x] Edit own profile (limited fields - name, password change)
- [ ] View assigned agent details (not implemented yet)
- [ ] View notes (if shared by agent) (not implemented yet)

#### Notifications ✅
- [x] In-app notification system (backend)
  - [x] Database migration for client_notifications table
  - [x] ClientNotification model with relationships
  - [x] JSON data field for flexible notification data
  - [x] Read/unread tracking with read_at timestamp
  - [x] Scopes for filtering (unread, read)
  - [x] Methods: markAsRead(), isUnread()
  - [x] User relationship (clientNotifications())
- [x] Notification UI components
  - [x] Notification bell with unread badge in ClientLayout
  - [x] Notification center page with filtering (all/unread/read)
  - [x] Mark as read and mark all as read functionality
  - [x] Individual notification delete
  - [x] Stats cards (total, unread, read counts)
  - [x] Notification icon and color coding by type
  - [x] Pagination support
  - [x] Empty states
- [x] Unread count shared globally in Inertia props
- [x] NotificationController with full CRUD operations
- [ ] Email notifications (not implemented)
- [ ] Notification types implementation:
  - [ ] New message from agent
  - [ ] Property recommendation
  - [ ] Appointment reminder
  - [ ] Document uploaded
  - [ ] Status update
- [ ] Notification preferences

#### Activity Feed ✅
- [x] Recent activity system (backend)
  - [x] Database migration for client_activities table
  - [x] ClientActivity model with relationships
  - [x] Fields: user_id, activity_type, description, data (JSON)
  - [x] Scopes: recent(), byType()
  - [x] User relationship (clientActivities())
  - [x] Indexed for performance (user_id, created_at)
- [x] Activity feed UI component (ClientActivityFeed.vue)
- [x] Activity types implementation:
  - [x] Login events
  - [x] Profile update events
  - [x] Password change events
  - [x] Agent added note (triggered in ContactNoteService)
  - [x] Status changed (triggered in ContactService)
  - [x] Document download
  - [ ] Property saved (not in scope)
  - [ ] Message received (not in scope)
- [x] Activity filtering by type
- [x] Activity timestamps display (relative time with formatTime helper)
- [x] Visual activity icons with color coding for all activity types
- [x] IP address tracking for security-related activities
- [x] Empty state handling

### Database Schema (Implemented)

**users table (additions)**:
- invitation_token (string, nullable, unique)
- invitation_sent_at (timestamp, nullable)
- invitation_accepted_at (timestamp, nullable)

**client_notifications**:
- id
- user_id (foreign key, cascades on delete)
- type (string) - notification type
- title (string)
- message (text)
- data (JSON, nullable) - additional notification data
- read_at (timestamp, nullable) - read status tracking
- timestamps
- Index: (user_id, read_at)

**client_activities**:
- id
- user_id (foreign key, cascades on delete)
- activity_type (string) - e.g., 'login', 'profile_view', 'document_download', 'status_change'
- description (string)
- data (JSON, nullable) - additional activity data
- timestamps
- Index: (user_id, created_at)

**permissions (additions)**:
- access-client-portal
- view-own-profile
- edit-own-profile
- view-own-activity
- view-own-notifications

**roles (additions)**:
- client (with restricted permissions above)

### Service Layer (Implemented)
- **ClientInvitationService** ✅ - Handles invitation creation, sending, acceptance, resending, and cancellation
  - createInvitation() - Creates client user with invitation token
  - sendInvitationEmail() - Sends email with signed URL (7-day expiry)
  - acceptInvitation() - Sets password and marks invitation as accepted
  - resendInvitation() - Regenerates token and resends email
  - cancelInvitation() - Clears invitation token
- **ClientNotificationService** ✅ - Centralized notification management
  - createNotification() - Create notification for user
  - createBulkNotifications() - Create for multiple users
  - notifyStatusChange() - Status update notifications
  - notifyAgentNote() - Agent note notifications
  - notifyDocumentUpload() - Document upload notifications
  - markAsRead() / markAllAsRead() - Mark notifications as read
- **ClientActivityService** ✅ - Centralized activity logging
  - logActivity() - Generic activity logging
  - logLogin() - Login events
  - logProfileUpdate() - Profile changes
  - logPasswordChange() - Password changes
  - logAgentNote() - Agent added note
  - logStatusChange() - Status changes
  - logDocumentDownload() - Document downloads

### UI Components (Implemented)
- **ClientLayout.vue** ✅ - Separate layout for client portal with simplified navigation (Dashboard, My Profile, Notifications) and notification bell with unread badge
- **ClientDashboard.vue** ✅ - Personalized dashboard with time-based greeting, quick stats, getting started guide, and activity feed
- **ClientProfile.vue** ✅ - Profile view with user info, account details, contact agent card, tags display, status display, and profile editing
- **AcceptInvitation.vue** ✅ - Public invitation acceptance page with password setup form
- **InviteClient.vue** ✅ - Admin form for sending client invitations
- **ClientNotifications.vue** ✅ - Notification center UI with filtering, stats, mark as read, and delete functionality
- **ClientActivityFeed.vue** ✅ - Activity timeline component with icons, colors, relative timestamps, and empty states

### Security (Implemented)
- ✅ Client role with restricted permissions (Spatie Permission)
- ✅ Middleware protection on client routes (auth, verified, approved, role:client)
- ✅ Signed URLs for invitation links (7-day expiry with signature verification)
- ✅ Separate route groups for client portal
- ✅ Redirect logic to prevent clients accessing admin areas
- ✅ Activity logging for client invitations (Spatie Activity Log)
- ✅ Cryptographically secure token generation (Str::random(64))
- ✅ Password validation (min 8 characters, confirmation required)
- ✅ Foreign key constraints with cascade delete
- ✅ ClientPolicy for fine-grained access control with 11 authorization gates
- ✅ Comprehensive activity logging for all client actions via ClientActivityService
- ✅ Profile data validation and sanitization
- ✅ Contact data sync with proper authorization checks

### Technical Achievements (So Far)
- ✅ Service Layer architecture (ClientInvitationService)
- ✅ Eloquent relationships (User → ClientNotification, User → ClientActivity)
- ✅ Form request validation (AcceptInvitationRequest)
- ✅ Signed URL security with expiration
- ✅ Database migrations with proper indexing
- ✅ JSON casting for flexible data storage
- ✅ Scopes for efficient queries (recent, byType, unread, read)
- ✅ Helper methods on models (isClient, hasPendingInvitation, markAsRead, isUnread)
- ✅ Auto-login after invitation acceptance
- ✅ Time-based personalized greetings
- ✅ Separate client layout with simplified navigation
- ✅ Dark mode support throughout client portal
- ✅ Responsive mobile design
- ✅ Email notification system with queued sending (Gmail SMTP)
- ✅ Branded markdown email templates
- ✅ Custom password reset notifications for all users

### Files Created
**Models**:
- app/Models/ClientNotification.php
- app/Models/ClientActivity.php

**Services**:
- app/Services/ClientInvitationService.php
- app/Services/ClientNotificationService.php
- app/Services/ClientActivityService.php

**Controllers**:
- app/Http/Controllers/ClientInvitationController.php
- app/Http/Controllers/Client/ProfileController.php
- app/Http/Controllers/Client/NotificationController.php

**Policies**:
- app/Policies/ClientPolicy.php (11 authorization gates)

**Form Requests**:
- app/Http/Requests/AcceptInvitationRequest.php

**Mail**:
- app/Mail/ClientInvitationMail.php
- app/Mail/PasswordResetMail.php
- app/Mail/UserApprovedMail.php
- app/Mail/ContactAssignedMail.php

**Email Templates**:
- resources/views/emails/client-invitation.blade.php
- resources/views/emails/password-reset.blade.php
- resources/views/emails/user-approved.blade.php
- resources/views/emails/contact-assigned.blade.php

**Vue Components**:
- resources/js/Layouts/ClientLayout.vue
- resources/js/Pages/Client/Dashboard.vue
- resources/js/Pages/Client/Profile.vue
- resources/js/Pages/Client/AcceptInvitation.vue
- resources/js/Pages/Client/Notifications.vue
- resources/js/Pages/Admin/Users/InviteClient.vue
- resources/js/Components/ClientActivityFeed.vue

**Migrations**:
- database/migrations/2026_03_25_102524_add_invitation_token_to_users_table.php
- database/migrations/2026_03_25_103552_create_client_notifications_table.php
- database/migrations/2026_03_25_103616_create_client_activities_table.php
- database/migrations/2026_03_25_125143_add_is_visible_to_client_to_contact_notes_table.php

**Routes Added**:
- GET /invitation/{token} (public, signed)
- POST /invitation/accept (public)
- GET /admin/users/invite/client
- POST /admin/users/invite/send
- POST /admin/users/{user}/resend-invitation
- GET /client/dashboard (protected, role:client)
- GET /client/profile (protected, role:client)
- PATCH /client/profile (protected, role:client)
- PATCH /client/profile/password (protected, role:client)
- GET /client/notifications (protected, role:client)
- GET /client/notifications/recent (protected, role:client)
- POST /client/notifications/{notification}/read (protected, role:client)
- POST /client/notifications/read-all (protected, role:client)
- DELETE /client/notifications/{notification} (protected, role:client)

### Phase 4 Completion Summary

**Completion Date**: March 25, 2026
**Implementation Approach**: Option B (Enhanced Completion)
**Total Implementation Time**: ~4 hours

#### ✅ Completed Features in This Session:
1. **ClientNotificationService** ✅ - Full centralized notification management with 9 specialized methods
2. **ClientActivityService** ✅ - Comprehensive activity logging with 11 activity type methods
3. **ClientPolicy** ✅ - 11 authorization gates for fine-grained access control
4. **Enhanced Profile Editing** ✅ - Clients can now edit phone, address, city, state, zip with Contact model sync
5. **Shared Notes Feature** ✅ - Agents can mark notes as visible to clients (`is_visible_to_client` field)
6. **Service Integration** ✅ - All controllers and listeners updated to use new centralized services
7. **Policy Registration** ✅ - All gates registered in AppServiceProvider

#### 📊 Phase 4 Statistics:
- **Files Created**: 8 (3 services, 1 policy, 1 migration, 3 controllers)
- **Files Modified**: 12 (models, controllers, listeners, views, providers)
- **Services Implemented**: 3 major service classes with 30+ methods total
- **Authorization Gates**: 11 fine-grained access control policies
- **Database Changes**: 1 migration for shared notes feature
- **Lines of Code Added**: ~1,500+ lines

#### ⏭️ Optional Future Enhancements (Not Required):
- Email notifications for events (can use Laravel Notifications)
- Notification preferences UI
- Real-time notifications (Laravel Echo + Pusher)
- Basic E2E testing suite
- Document center (separate feature)
- Direct messaging system
- Appointment scheduling

---

## Phase 5: CMA Report Builder 📊 PLANNED

**Status**: 📊 Planned
**Priority**: Medium

### Objectives
Build a Comparative Market Analysis (CMA) tool that allows agents to create professional, branded property valuation reports for clients.

### Planned Features

#### Subject Property
- [ ] Enter subject property details:
  - Address
  - Square footage
  - Bedrooms / Bathrooms
  - Lot size
  - Year built
  - Property type
  - Features/amenities
  - Photos
- [ ] Property detail form
- [ ] Image upload
- [ ] Save as draft

#### Comparable Properties (Comps)
- [ ] Add multiple comparable properties
- [ ] Comp fields:
  - Address
  - Square footage
  - Bedrooms / Bathrooms
  - Sale price
  - Sale date
  - Distance from subject
  - Photos
- [ ] Search for comps (if property database exists)
- [ ] Manual comp entry
- [ ] Minimum 3 comps recommended
- [ ] Remove/edit comps

#### Adjustments System
- [ ] Adjustment calculator
- [ ] Adjustment categories:
  - Size differential
  - Condition
  - Location
  - Age
  - Features (pool, garage, etc.)
  - Market conditions
- [ ] Add/subtract adjustments
- [ ] Adjustment reasons/notes
- [ ] Auto-calculate adjusted prices

#### Price Per Square Foot
- [ ] Calculate subject property $/sqft
- [ ] Calculate comp $/sqft
- [ ] Show $/sqft comparison
- [ ] Highlight outliers

#### Valuation Range
- [ ] Calculate suggested value range:
  - Low estimate
  - Average estimate
  - High estimate
- [ ] Based on adjusted comp prices
- [ ] Confidence score
- [ ] Valuation summary paragraph

#### Comparison Table UI
- [ ] Clean, professional table
- [ ] Side-by-side property comparison
- [ ] Sortable columns
- [ ] Editable fields inline
- [ ] Color-coded adjustments
- [ ] Responsive design

#### Branded PDF Output
- [ ] Generate PDF report
- [ ] Include uploaded logos (light/dark)
- [ ] Professional header with:
  - Company logo
  - Company name
  - Agent name, photo, contact
- [ ] Report sections:
  - Executive summary
  - Subject property details
  - Comparable properties table
  - Adjustments breakdown
  - Price per sqft analysis
  - Valuation range summary
- [ ] Footer with disclaimers
- [ ] Page numbers
- [ ] Print-friendly layout

#### Report Management
- [ ] Save reports per client
- [ ] Associate report with contact
- [ ] View report history
- [ ] Edit existing reports
- [ ] Duplicate/template reports
- [ ] Archive old reports
- [ ] Share report via email
- [ ] Download PDF

### Database Schema
```
cma_reports:
  - id
  - contact_id (client this report is for)
  - user_id (agent who created)
  - subject_property (JSON: address, sqft, beds, baths, etc.)
  - comparables (JSON: array of comp properties)
  - adjustments (JSON: adjustment data)
  - valuation_low
  - valuation_avg
  - valuation_high
  - status (draft, finalized)
  - generated_pdf_path
  - timestamps
```

### Service Layer
- CMAService (create, calculate, generate)
- CMAPdfGeneratorService (PDF creation with logos)
- PropertyAdjustmentService (adjustment calculations)

### UI Components
- CMABuilder.vue
- SubjectPropertyForm.vue
- ComparablesList.vue
- ComparableForm.vue
- AdjustmentsTable.vue
- ValuationSummary.vue
- CMAPdfPreview.vue
- CMAHistory.vue

### Technical Requirements
- PDF library (Laravel DomPDF or Snappy)
- Logo embedding in PDF
- Mathematical calculations
- File storage for generated PDFs
- Email delivery for sharing

---

## Phase 6: API & Integrations 🔌 PLANNED

**Status**: 🔌 Planned
**Priority**: Medium

### Objectives
Expose the Label Portal as an API platform and prepare for third-party integrations, including RealScout and other real estate services.

### Planned Features

#### API Authentication (Laravel Sanctum)
- [ ] API token generation
- [ ] Token management (create, revoke, expire)
- [ ] Token scopes/abilities
- [ ] Personal access tokens for users
- [ ] API rate limiting
- [ ] Token expiration policies

#### API Endpoints
- [ ] RESTful API for:
  - Contacts (CRUD)
  - Users (read-only for non-admins)
  - CMA Reports (create, read)
  - Activities/logs
- [ ] API versioning (v1, v2, etc.)
- [ ] JSON responses
- [ ] Pagination support
- [ ] Filtering and sorting
- [ ] Error handling and status codes
- [ ] API documentation (Swagger/OpenAPI)

#### Integration Settings
- [ ] Integration management UI
- [ ] Third-party service connections:
  - RealScout
  - MLS systems
  - Email marketing (Mailchimp, etc.)
  - Calendar sync (Google, Outlook)
  - Document signing (DocuSign, HelloSign)
- [ ] OAuth configuration
- [ ] API key management
- [ ] Connection status monitoring
- [ ] Test connection functionality

#### Webhooks
- [ ] Webhook endpoint registration
- [ ] Webhook event types:
  - Contact created/updated
  - Status changed
  - CMA report generated
  - User activity
- [ ] Webhook delivery logs
- [ ] Retry failed webhooks
- [ ] Webhook signature verification
- [ ] Webhook testing tools

#### RealScout Integration Preparation
- [ ] Decoupled integration architecture
- [ ] Property sync endpoints
- [ ] Client property preferences sync
- [ ] Search criteria sync
- [ ] Property alerts handling
- [ ] Two-way data sync
- [ ] Conflict resolution
- [ ] Integration settings UI

### Database Schema
```
api_tokens:
  - id
  - user_id
  - name
  - token (hashed)
  - abilities (JSON)
  - last_used_at
  - expires_at
  - timestamps

integrations:
  - id
  - name (realscout, mailchimp, etc.)
  - type
  - credentials (encrypted JSON)
  - settings (JSON)
  - status (active, inactive, error)
  - last_sync_at
  - timestamps

webhooks:
  - id
  - url
  - events (JSON array)
  - secret
  - status (active, paused)
  - timestamps

webhook_deliveries:
  - id
  - webhook_id
  - event
  - payload (JSON)
  - response_code
  - response_body
  - delivered_at
  - timestamps
```

### Service Layer
- ApiTokenService
- IntegrationService
- WebhookService
- RealScoutSyncService

### API Resources
- ContactResource
- UserResource
- CMAReportResource
- ActivityResource

### Security
- API token encryption
- Rate limiting per token
- IP whitelisting option
- Webhook signature verification
- Encrypted credential storage
- Audit logging for API calls

---

## Phase 7: Analytics Dashboard 📈 PLANNED

**Status**: 📈 Planned
**Priority**: Low-Medium

### Objectives
Provide comprehensive analytics and reporting to help agents and admins track performance, client engagement, and business metrics.

### Planned Features

#### KPI Cards
- [ ] Dashboard overview with key metrics:
  - Total contacts
  - New contacts this month
  - Active deals
  - Closed deals this month
  - Total revenue (if tracking)
  - Conversion rate
  - Average deal size
  - Response time
- [ ] Period comparison (vs last month, last quarter)
- [ ] Percentage change indicators
- [ ] Color-coded performance (green/red)

#### Charts & Visualizations
- [ ] Chart library integration (Chart.js or ApexCharts)
- [ ] Chart types:
  - **Line charts**: Contacts over time, deals over time
  - **Bar charts**: Contacts by source, deals by agent
  - **Pie charts**: Contact types, deal stages
  - **Funnel charts**: Pipeline conversion
- [ ] Interactive charts (hover tooltips, drill-down)
- [ ] Export charts as images

#### Activity Metrics
- [ ] Most active users
- [ ] Contact creation trends
- [ ] Note activity
- [ ] Email activity (if integrated)
- [ ] Login frequency
- [ ] Feature usage statistics
- [ ] User engagement scores

#### Activity Logs View
- [ ] Comprehensive activity log viewer
- [ ] Filter by:
  - User
  - Action type
  - Date range
  - Entity (contact, user, etc.)
- [ ] Search logs
- [ ] Export logs to CSV
- [ ] Pagination
- [ ] Detailed activity view

#### Report Filters
- [ ] Date range picker
- [ ] Quick filters (today, this week, this month, this quarter, this year)
- [ ] Custom date ranges
- [ ] Filter by agent
- [ ] Filter by team
- [ ] Filter by contact type
- [ ] Filter by source
- [ ] Save filter presets

#### Scheduled Reports
- [ ] Email reports on schedule
- [ ] Daily/weekly/monthly frequency
- [ ] Custom recipient lists
- [ ] Report templates
- [ ] PDF export

### Database Schema
```
analytics_snapshots:
  - id
  - date
  - metric_name
  - metric_value
  - user_id (null for system-wide)
  - timestamps

scheduled_reports:
  - id
  - name
  - frequency (daily, weekly, monthly)
  - recipients (JSON)
  - filters (JSON)
  - last_sent_at
  - next_send_at
  - timestamps
```

### Service Layer
- AnalyticsService (calculate metrics)
- ReportGeneratorService (generate reports)
- ChartDataService (prepare chart data)

### UI Components
- AnalyticsDashboard.vue
- KPICard.vue
- LineChart.vue
- BarChart.vue
- PieChart.vue
- ActivityLogViewer.vue
- ReportFilters.vue
- ScheduledReports.vue

### Technical Requirements
- Efficient database queries (aggregations)
- Caching for expensive calculations
- Background job for scheduled reports
- Chart rendering library
- Data export to CSV/PDF

---

## Success Metrics

### Phase 1 Metrics (Completed)
- ✅ Authentication working flawlessly
- ✅ All CRUD operations functional
- ✅ Roles and permissions implemented
- ✅ Mobile responsive on all pages
- ✅ Zero critical bugs

### Future Phase Metrics
- User adoption rate > 80%
- System uptime > 99.9%
- Page load time < 500ms
- Contact import success rate > 95%
- CMA report generation < 30 seconds
- API response time < 200ms
- User satisfaction score > 4.5/5

---

## Technology Roadmap

### Current Stack (Phase 1)
- Laravel 11.x
- PHP 8.3.14
- MySQL
- Vue 3
- Inertia.js
- Tailwind CSS
- Laravel Breeze
- Spatie Permission
- Spatie Activity Log

### Planned Additions by Phase

**Phase 2:**
- No new dependencies (uses existing stack)

**Phase 3:**
- Laravel Excel (for CSV processing)
- Queue jobs for background import

**Phase 4:**
- Laravel Notifications
- Pusher or Laravel Echo (real-time notifications)

**Phase 5:**
- Laravel Snappy or DomPDF (PDF generation)
- Intervention Image (image processing)

**Phase 6:**
- Laravel Sanctum (API tokens)
- Guzzle HTTP (external API calls)
- OAuth libraries

**Phase 7:**
- Chart.js or ApexCharts
- Laravel Scheduler (cron jobs)

---

## Risk Management

### Identified Risks

1. **Scope Creep**
   - Risk: Features expanding beyond phase scope
   - Mitigation: Strict adherence to phase boundaries

2. **Data Migration Challenges** (Phase 3)
   - Risk: Complex Compass data structures
   - Mitigation: Flexible mapping system, extensive testing

3. **Integration Complexity** (Phase 6)
   - Risk: Third-party API changes, downtime
   - Mitigation: Decoupled architecture, error handling

4. **Performance** (Phase 7)
   - Risk: Slow analytics with large datasets
   - Mitigation: Database indexing, caching, background jobs

5. **User Adoption**
   - Risk: Resistance to new system
   - Mitigation: Training, documentation, gradual rollout

---

## Development Workflow

### Phase Start Checklist
- [ ] Review phase objectives
- [ ] Design database schema
- [ ] Create migrations
- [ ] Create models with relationships
- [ ] Build service layer
- [ ] Create controllers
- [ ] Create form requests
- [ ] Create policies
- [ ] Build Vue components
- [ ] Create routes
- [ ] Write tests
- [ ] Update documentation

### Phase End Checklist
- [ ] All features completed
- [ ] Tests passing
- [ ] Code reviewed
- [ ] Documentation updated
- [ ] Database seeded with sample data
- [ ] User acceptance testing (UAT)
- [ ] Stakeholder approval
- [ ] Git tagged with version number
- [ ] **STOP - Await approval for next phase**

---

## Version History

| Version | Phase | Date | Status | Description |
|---------|-------|------|--------|-------------|
| 1.0.0 | Phase 1 | Mar 23, 2026 | ✅ Complete | Foundation & User Management |
| 2.0.0 | Phase 2 | Mar 24, 2026 | ✅ Complete | CRM Contacts |
| 3.0.0 | Phase 3 | Mar 25, 2026 | ✅ Complete | CSV Import Manager |
| 4.0.0 | Phase 4 | Mar 25, 2026 | ✅ Complete | Client Portal (Production Ready) |
| 5.0.0 | Phase 5 | TBD | 📊 Planned | CMA Report Builder |
| 6.0.0 | Phase 6 | TBD | 🔌 Planned | API & Integrations |
| 7.0.0 | Phase 7 | TBD | 📈 Planned | Analytics Dashboard |

---

**Document Status**: Living Document
**Last Updated**: March 25, 2026
**Next Review**: Before Phase 5 Start
**Maintained By**: Development Team

**⚠️ CRITICAL REMINDER**: Each phase must be completed, reviewed, and approved before starting the next phase. NO phase skipping or combining allowed.
