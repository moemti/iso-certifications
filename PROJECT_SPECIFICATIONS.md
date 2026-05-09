# Project Specifications: Certifications Portal

## 1. Product Vision
Build a web portal for certification projects managed per company and per project type.

The portal must support:
- Multiple project types.
- Project-level and site-level roles.
- A chapter-based project structure.
- A final output package containing a main Word document and uploaded evidence files.

## 2. Core Concept
- A company registers in the portal for a selected project type.
- The registering user becomes Project Admin for that project.
- Registration must be reviewed and approved by a Site Admin.
- After approval, project work is performed by chapters.

## 3. Main Actors

### 3.1 Site Users (Global Roles)
- Site Admin
  - Manages platform-level settings and users.
  - Manages project types and chapter templates.
  - Reviews and approves/rejects company project registration requests.
  - Can view all companies, projects, chapters, and deliverables.

### 3.2 Project Users (Per-Project Roles)
Each project has project-specific users and rights.

- Project Admin
  - Automatically assigned to the registering user.
  - Manages users and rights inside the project.
  - Manages project chapter content, uploads, and final output.

- Other Project Groups (examples)
  - Project Manager
  - Auditor
  - Contributor
  - Viewer

Note: Final project groups and rights should be configurable per project type.

## 4. Domain Model (High-Level)

### 4.1 Company
Represents an organization that owns certification projects.

Suggested fields:
- id
- legal_name
- registration_number
- country
- contact_email
- status (active, suspended)
- created_at
- updated_at

### 4.2 ProjectType
Represents a certification model/template (for example ISO families).

Suggested fields:
- id
- code
- name
- description
- is_active
- created_at
- updated_at

### 4.3 ProjectTypeChapterTemplate
Defines chapter templates managed by Site Admin for each project type.

Suggested fields:
- id
- project_type_id
- code
- title
- description
- admin_default_text (long text)
- sort_order
- is_required
- created_at
- updated_at

### 4.4 Project
Represents one company implementation of one project type.

Suggested fields:
- id
- company_id
- project_type_id
- name
- registration_status (pending_approval, approved, rejected)
- progress_status (partial_completed, completed)
- created_by_user_id
- approved_by_user_id (nullable)
- approved_at (nullable)
- rejection_reason (nullable)
- created_at
- updated_at

Rules:
- One project belongs to one company.
- One project belongs to one project type.
- One company can have many projects.

### 4.5 ProjectChapter
Runtime chapter instance inside a project.

Suggested fields:
- id
- project_id
- template_id (nullable, for future custom chapters)
- code
- title
- status (partial_completed, completed)
- sort_order
- created_at
- updated_at

### 4.6 ProjectChapterParagraph
Paragraph blocks inside a chapter, supporting text and images.

Suggested fields:
- id
- project_chapter_id
- title (nullable)
- content_html (long text)
- sort_order
- created_by_user_id
- created_at
- updated_at

### 4.7 ProjectAsset
Uploaded project files and images used as evidence.

Suggested fields:
- id
- project_id
- project_chapter_id (nullable)
- asset_type (main_word_document, chapter_image, chapter_file, supporting_file)
- document_type (nullable, for example contract, evidence, report, image)
- display_name
- original_name
- storage_path
- mime_type
- size_bytes
- uploaded_by_user_id
- uploaded_at
- created_at

### 4.8 User
Represents a person using the portal.

Suggested fields:
- id
- name
- email
- password_hash
- site_role (site_admin, site_user)
- is_active
- created_at
- updated_at

### 4.9 ProjectMembership
Connects users to projects with project-specific rights.

Suggested fields:
- id
- project_id
- user_id
- project_role
- invited_by_user_id (nullable)
- created_at
- updated_at

## 5. Status Model

### 5.1 Project Registration Status
- pending_approval
- approved
- rejected

### 5.2 Project Progress Status
- partial_completed
- completed

Rule:
- Project progress_status is derived from chapter statuses.
- If all required chapters are completed, project progress_status becomes completed.
- Otherwise it stays partial_completed.

### 5.3 Chapter Status
- partial_completed
- completed

## 6. Permission Model

### 6.1 Site-Level Permissions
- Site Admin can:
  - Approve/reject project registrations.
  - Create and edit project types.
  - Create and edit chapter templates and default text.
  - Access all portal data.

### 6.2 Project-Level Permissions
- Project Admin can:
  - Manage project users and roles.
  - Edit chapter content.
  - Add paragraphs (text and images).
  - Manage upload zones/files in the project.
  - Upload/replace the main Word output document.

- Other project users:
  - Can only see/edit projects and chapters where their role grants rights.

## 7. Registration and Approval Workflow

### 7.1 Company Registration for Project
1. A user creates an account or signs in.
2. The user submits company details and selects project type.
3. System creates project with registration_status pending_approval.
4. System creates project chapters from selected project type chapter templates.
5. System assigns submitting user as Project Admin for that project.
6. Site Admin is notified.

### 7.2 Site Admin Decision
1. Site Admin reviews request.
2. Site Admin approves or rejects.
3. If approved:
   - registration_status changes to approved.
   - Project becomes visible and usable by authorized project users.
4. If rejected:
   - registration_status changes to rejected.
   - rejection_reason is stored and visible to requester.

## 8. User Experience Requirements

### 8.1 User Dashboard
When a user logs in, the user sees only projects they are allowed to see.

Each visible project card/list row must show:
- Project name.
- Company.
- Project type.
- Registration status.
- Progress status (partial_completed or completed).

### 8.2 Project View Layout
Project page must include:
- Left side menu: ordered list of chapters.
- Main content panel: selected chapter content.
- Status indicators for each chapter and overall project.
- A separate Uploads area in the same project context that lists all uploaded documents/files.

### 8.3 Chapter Editor
A chapter contains:
- Admin-provided base text (from template).
- Project content paragraphs (rich text).
- Images inside paragraphs.
- Upload zones for project files/images.
- Links to files uploaded for that chapter/section.

## 9. Content and File Handling

### 9.1 Word/Paste Support
- Users can paste text copied from Word into chapter paragraphs.
- System should preserve basic formatting where possible (headings, bold, lists).

### 9.2 Upload Zones
Within project/chapter context, users with rights can upload:
- Images.
- Supporting files.
- Additional documents related to chapters.

### 9.3 Uploads List (Centralized)
Each project must have a dedicated Uploads list page/section in the same project area.

This list must include at least:
- File name (display name).
- Type (document type and/or file type).
- Upload date.
- Related chapter/section (if applicable).
- Uploaded by (user).
- Link to open/download the file.

Visibility rules:
- A file should appear as a link in the chapter/section where it is required or attached.
- The same file must also appear in the centralized project Uploads list.
- Users only see files from projects they have rights to access.

### 9.4 Final Project Outcome
Each completed project must produce a deliverable folder containing:
- Main Word document (official output).
- Uploaded supporting files from the project.

The folder can be generated as a downloadable package (for example ZIP).

## 10. Functional Requirements (MVP)
- Authentication (login/logout).
- Project registration flow and Site Admin approval.
- Site Admin project type editor (project types + chapter templates + default chapter text).
- Project chapter side menu and chapter status updates.
- Chapter paragraph editor with text + image support.
- Upload zones for files and images.
- Centralized project Uploads list with metadata (name, type, date, chapter/section, uploader).
- File shown in two places: chapter context link and centralized Uploads list.
- Main Word document upload and replacement.
- User dashboard showing only authorized projects and their statuses.
- Role-based authorization at site and project scopes.

## 11. Non-Functional Requirements
- Security:
  - Enforce authorization server-side for all sensitive actions.
- Auditability:
  - Log registration, approvals/rejections, role changes, chapter status changes, and file uploads.
- Scalability:
  - Support many companies, many projects, and many project types.

## 12. Acceptance Criteria
- Site Admin can create project types and chapter templates.
- Company registration creates pending project and auto-assigns requester as Project Admin.
- Site Admin can approve/reject registration requests.
- Approved project shows generated chapters in side menu.
- Project and chapters support statuses partial_completed and completed.
- User dashboard shows only permitted projects and their statuses.
- Authorized users can add chapter paragraphs with text/images and use upload zones.
- Uploaded files are visible in both required chapter context (as link) and centralized Uploads list.
- Uploads list shows name, type, and date for each file.
- Project has one main Word document plus supporting files.
- Completed project can generate a deliverable folder/package with the main Word document and uploaded files.

## 13. Suggested Next Implementation Steps
1. Create migrations for companies, project_types, project_type_chapter_templates, projects, project_chapters, chapter_paragraphs, project_assets, and project_memberships.
2. Add enums/constants for registration status, project progress status, and chapter status.
3. Implement role/permission policies for site-level and project-level actions.
4. Build Site Admin UI for project type and chapter template editor.
5. Build project dashboard, project detail view with left chapter menu, and chapter editor.
6. Implement upload zones and final deliverable folder generation.
7. Add tests for permissions, status propagation, and approval workflow.
