# Smart Warranty Management System (SWMS) 🛡️

A professional, high-performance web application designed to unify and automate the entire warranty claim lifecycle—from initial product registration to final resolution.

##  Vision
The Smart Warranty System streamlines the relationship between businesses and customers by providing a transparent, efficient, and data-driven platform for managing product guarantees. It features a unified 'Resolution' workflow that simplifies tracking while maintaining professional standards for repairs, replacements, and refunds.

##  Core Features
###  For Business Owners (Management Console)
- **Business Intelligence Dashboard**: Real-time tracking of success rates, total refunded value (KSh), and technician performance.
- **Product Catalog Management**: Dynamic 120+ item catalog with vertical sidebar filtering and instant search.
- **Resolution Authorization**: Centralized hub for approving or denying technical repair/replacement/refund decisions.
- **Inventory & Sold Items**: Full CRUD management of serialized pre-sold inventory.
- **Comprehensive Reports**: Monthly trend analysis and financial liability tracking.

###  For Technicians (Verification Portal)
- **Claim Verification**: Robust tools for documenting technical findings and recommended actions.
- **Queue Management**: Prioritized claim lists (Urgent, High, Medium, Low) for optimized workflow.
- **Resolution Logging**: Direct reporting on repair completions and replacement authorizations.

###  For Customers (Self-Service Portal)
- **Instant Warranty Check**: Mobile-friendly serial number verification with real-time expiry tracking.
- **Professional Claim Filing**: Intelligent claim restrictions preventing duplicate active requests.
- **Official Statements**: Downloadable/Printable Refund Resolution Statements and Repair Receipts.
- **Localized Experience**: Fully configured for Kenyan Shilling (KSh) and regional business standards.

##  Technology Stack
- **Backend**: PHP 8.x with PDO (MySQL) security.
- **Frontend**: Vanilla CSS3 (Modern Glassmorphism Design System) & JavaScript.
- **UI Components**: FontAwesome 6 icons, Google Fonts (Outfit/Inter).
- **Visualization**: Chart.js for business intelligence analytics.
- **Architecture**: Modular include-based structure with CSRF protection and session-based RBAC (Role-Based Access Control).

##  Installation (Local Environment - XAMPP)
1. Clone the repository into your `htdocs` directory.
2. Import the `database.sql` into your local phpMyAdmin.
3. Configure your database credentials in `config.php`.
4. Run the seed scripts in `/tests/` to populate your catalog with 100+ professional products.
5. Access the portal via `http://localhost/warranty_system`.

##  Security Standards
- **One Active Claim Policy**: Prevents customer confusion and duplicate workload.
- **Resolution Sealing**: Finalized claims (Refunds/Replacements) automatically void original coverage to prevent fraud.
- **CSRF & XSS Protection**: All management forms are secured against common web vulnerabilities.

---
**Smart Warranty System** — *Empowering transparency in every resolution.*
