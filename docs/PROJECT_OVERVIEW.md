# Label Client Portal - Project Overview

## Project Description

The Label Client Portal is a comprehensive SaaS real estate client portal and internal CRM system designed to replace the Compass experience. This platform provides a centralized solution for managing clients, agents, and real estate operations with robust user management and role-based access control.

## Vision & Goals

### Primary Objectives
- Create a modern, user-friendly client portal for real estate operations
- Implement comprehensive user management with approval workflows
- Provide role-based access control for different user types
- Enable custom branding and white-label capabilities
- Track all user activities and system changes
- Build a scalable foundation for future CRM features

### Target Users
- **Admins**: Full system access, user management, settings configuration
- **Managers**: Team oversight, limited administrative capabilities
- **Users**: Standard access to client portal features

## Technology Stack

### Backend
- **Framework**: Laravel 11.x
- **PHP Version**: 8.3.14
- **Database**: MySQL
- **Authentication**: Laravel Breeze
- **Permissions**: Spatie Laravel Permission
- **Activity Logging**: Spatie Laravel Activity Log

### Frontend
- **Framework**: Vue 3 (Composition API)
- **Routing**: Inertia.js
- **Styling**: Tailwind CSS
- **Build Tool**: Vite
- **Icons**: Heroicons (via Tailwind)

### Development Tools
- **Package Manager**: Composer (PHP), NPM (JavaScript)
- **Version Control**: Git
- **Local Server**: WAMP64 (Apache, MySQL, PHP)

## Current Features (Phase 1 - Completed)

### Authentication System
- ✅ User registration with email verification
- ✅ Secure login/logout functionality
- ✅ Password reset and recovery
- ✅ Remember me functionality
- ✅ Email verification workflow
- ✅ User approval workflow (pending/approved/suspended states)

### User Management
- ✅ Complete CRUD operations for users
- ✅ User listing with pagination and filters
- ✅ Search functionality by name/email
- ✅ Filter by status (pending, approved, suspended)
- ✅ Filter by role (admin, manager, user)
- ✅ User approval/rejection workflow
- ✅ User suspension/unsuspension
- ✅ Soft delete with restore capability
- ✅ User statistics dashboard
- ✅ Activity history per user

### Role-Based Access Control (RBAC)
- ✅ Three default roles: Admin, Manager, User
- ✅ Permission-based authorization
- ✅ Role assignment during user creation
- ✅ Policy-based access control
- ✅ Middleware protection for routes
- ✅ Role-specific navigation menus

### Branding & Customization
- ✅ Custom application name
- ✅ Logo upload (light and dark themes)
- ✅ Favicon customization
- ✅ Logo management (upload/reset to defaults)
- ✅ Real-time preview of branding changes
- ✅ Theme-aware logo display

### Activity Logging
- ✅ Comprehensive activity tracking using Spatie Activity Log
- ✅ User action logging (create, update, delete, approve, suspend)
- ✅ Activity history display
- ✅ Causer and subject tracking
- ✅ Event-based logging

### UI/UX Features
- ✅ Fully responsive mobile design
- ✅ Dark mode support
- ✅ Vertically centered login page
- ✅ Mobile-friendly navigation
- ✅ Proper spacing on all screen sizes
- ✅ Toast notifications for user feedback
- ✅ Loading states and form validation
- ✅ Accessible forms and inputs

## Database Schema

### Users Table
- Personal information (name, email, password)
- Email verification fields
- Approval workflow fields (is_approved, approved_by, approved_at)
- Suspension fields (is_suspended, suspended_by, suspended_at)
- Timestamps and soft deletes

### Roles & Permissions Tables (Spatie)
- Roles (admin, manager, user)
- Permissions (granular access control)
- Role-Permission pivot
- User-Role pivot
- User-Permission pivot (direct permissions)

### Settings Table
- Key-value storage for application settings
- Supports branding configuration
- JSON data type support

### Activity Log Table (Spatie)
- Log name and description
- Subject and causer polymorphic relations
- Properties (JSON data)
- Event tracking
- Batch UUID support

## Application Structure

### Backend Architecture

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/
│   │   │   ├── UserController.php (User management)
│   │   │   └── SettingsController.php (Branding settings)
│   │   ├── Auth/ (Authentication controllers)
│   │   └── ProfileController.php
│   ├── Middleware/
│   │   └── EnsureUserIsApprovedAndActive.php
│   └── Requests/
├── Models/
│   ├── User.php (with roles and permissions)
│   └── Setting.php
├── Policies/
│   └── UserPolicy.php (Authorization rules)
└── Services/
    ├── UserService.php (User business logic)
    └── SettingsService.php (Settings management)
```

### Frontend Architecture

```
resources/js/
├── Components/
│   ├── ApplicationLogo.vue (Theme-aware logo)
│   ├── Buttons, Inputs, Modals, etc.
│   └── Navigation components
├── Layouts/
│   ├── AuthenticatedLayout.vue (Main app layout)
│   └── GuestLayout.vue (Login/Register layout)
└── Pages/
    ├── Admin/
    │   ├── Users/ (CRUD pages)
    │   └── Settings/ (Branding page)
    ├── Auth/ (Authentication pages)
    ├── Profile/ (User profile management)
    └── Dashboard.vue
```

## Key Design Decisions

### 1. Service Layer Pattern
- Business logic separated from controllers
- Reusable service methods
- Easier testing and maintenance

### 2. Policy-Based Authorization
- UserPolicy for granular access control
- Prevents users from managing themselves
- Role-based permission checks

### 3. Approval Workflow
- New registrations require admin approval
- Middleware prevents unapproved users from accessing the system
- Clear status indicators (pending, approved, suspended)

### 4. Activity Logging
- All critical actions are logged
- Audit trail for compliance
- User accountability

### 5. Theme-Aware Branding
- Separate logos for light/dark modes
- Automatic theme detection
- Consistent branding across all pages

### 6. Mobile-First Responsive Design
- Tailwind CSS utility classes
- Responsive breakpoints (sm, md, lg)
- Touch-friendly interfaces

## Security Features

### Authentication
- Password hashing using bcrypt
- CSRF protection on all forms
- Email verification requirement
- Password confirmation for sensitive actions

### Authorization
- Role-based access control
- Policy-based permissions
- Middleware protection on routes
- Admin-only access to management features

### Data Protection
- Input validation on all forms
- SQL injection prevention (Eloquent ORM)
- XSS protection (Vue template escaping)
- Secure session management

### User Privacy
- Soft deletes (data retention)
- Activity logging (transparency)
- Email verification (authenticity)

## Current System Statistics

### Default Credentials
- **Admin Email**: admin@labelsalesagents.com
- **Admin Password**: Thelabel99!

### Default Roles
- Admin (full access)
- Manager (limited admin access)
- User (standard access)

### Default Assets
- Logo: `public/images/logos/label-logo.png`
- Dark Logo: `public/images/logos/label-white-logo.png`
- Favicon: `public/images/logos/label-white-favicon.png`

## Development Workflow

### Local Development
1. WAMP64 server running (Apache, MySQL, PHP)
2. Vite dev server (`npm run dev`)
3. Laravel dev server (`php artisan serve`)
4. Access at http://127.0.0.1:8000

### Git Workflow
- Main branch: Production-ready code
- Feature commits with descriptive messages
- Regular pushes to GitHub

## Project Metrics

### Codebase Size
- **Backend**: ~30 files
- **Frontend**: ~40 Vue components
- **Database**: 9 migrations
- **Routes**: ~20 defined routes

### Performance
- Page load: Fast (SPA with Inertia.js)
- Database queries: Optimized with eager loading
- Asset compilation: Vite for fast HMR

## Known Limitations & Considerations

### Current Limitations
- No email sending configured (uses log driver)
- No file upload size limits enforced
- No rate limiting on API endpoints
- No two-factor authentication

### Future Considerations
- Implement real email service (SendGrid, Mailgun)
- Add file upload validations and storage limits
- Implement API rate limiting
- Add 2FA for enhanced security
- Consider caching for better performance

## Documentation

All project documentation is located in the `/docs` folder:
- `PROJECT_OVERVIEW.md` - This file
- `PHASES.md` - Project phases and roadmap
- `SETUP.md` - Installation and setup guide

## Support & Resources

### Official Documentation
- [Laravel Documentation](https://laravel.com/docs/11.x)
- [Vue 3 Documentation](https://vuejs.org/)
- [Inertia.js Documentation](https://inertiajs.com/)
- [Tailwind CSS Documentation](https://tailwindcss.com/)
- [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission)

### Repository
- GitHub: https://github.com/toxickim24/label-portal

---

**Last Updated**: March 23, 2026
**Version**: 1.0.0 (Phase 1 Complete)
**Maintained By**: Development Team
