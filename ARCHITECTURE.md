# 🏗️ Architecture & Design Decisions

This document explains the architectural and design decisions made during the development of the Shipping Label Generator application.

---

## 🎯 Core Architecture Decisions

### 1. Monolithic Laravel + Inertia.js (SPA-like)

**Decision:** Use Laravel with Inertia.js instead of separate API + SPA

**Reasoning:**
- **Simpler deployment:** Single codebase, one server, easier to maintain
- **Better security:** No CORS issues, session-based auth is more secure than tokens
- **Faster development:** Share routes, validators, and types between backend/frontend
- **SPA experience:** Vue.js with Inertia provides modern UX without API complexity
- **Built-in CSRF protection:** Laravel handles this automatically

**Alternative Considered:** Laravel API + Vue SPA (separate deployments)
- **Rejected because:** 
  - More complex auth (JWT/Sanctum tokens management)
  - CORS configuration and security concerns
  - Two separate deployments to manage
  - More API boilerplate code
  - Harder to share validation rules

---

### 2. Service Layer Pattern (EasyPostService)

**Decision:** Encapsulate all EasyPost API logic in a dedicated service class

**Location:** `app/Services/EasyPostService.php`

**Reasoning:**
- **Separation of concerns:** Controllers stay thin and focused on HTTP
- **Reusability:** Service can be used from jobs, commands, or other services
- **Testability:** Easy to mock in unit tests without hitting real API
- **Maintainability:** All API logic in one place, easier to update when API changes
- **Error handling:** Centralized exception handling for API failures

**Key Methods:**
```php
- createShipment()    // Create and purchase cheapest USPS rate
- getRates()          // Get all available rates without purchasing
- refundShipment()    // Cancel and refund a label
```

**Design Pattern Applied:** **Adapter Pattern**
- Adapts EasyPost SDK to our application's specific needs
- Hides SDK complexity from controllers
- Provides consistent interface even if we change shipping provider

---

### 3. Database Schema Design

**Decision:** Store all address and package data in individual columns (not JSON)

**Table:** `shipping_labels` (44 columns)

**Reasoning:**
- **Queryable:** Can search by city, state, ZIP without JSON parsing or extraction
- **Indexed:** Better query performance on commonly searched fields
- **Type-safe:** Database validates data types at storage level
- **Reportable:** Easy to generate aggregate reports and statistics
- **Maintainable:** Clear schema, easier for other developers to understand

**Alternative Considered:** Store addresses as JSON columns
- **Rejected because:** 
  - Harder to query and filter
  - No database-level validation
  - Poor indexing performance
  - Difficult to generate reports
  - MySQL JSON functions are less performant than native columns

**Key Schema Features:**
```sql
- user_id (foreign key, indexed)     // User isolation
- easypost_shipment_id (unique)      // EasyPost reference
- from_* / to_* (14 fields each)     // Full address details
- weight, length, width, height      // Package attributes (decimal)
- carrier, service, rate             // Shipping details
- tracking_code (indexed)            // USPS tracking
- label_url, label_pdf_url           // Printable labels
- status (enum, indexed)             // Lifecycle: created, purchased, cancelled
```

---

### 4. Authentication Strategy

**Decision:** Use Laravel Breeze with Inertia.js

**Reasoning:**
- **Official:** Laravel's recommended starter kit for Inertia
- **Complete:** Login, Register, Password Reset, Email Verification out-of-the-box
- **Vue.js ready:** Pre-configured with Vue 3 and Composition API
- **Customizable:** Easy to extend and modify for specific needs
- **Maintained:** Official Laravel package, well-supported

**Security Features Implemented:**
- Session-based authentication (more secure than tokens for web apps)
- CSRF protection enabled on all forms
- Password hashing with bcrypt (cost factor 12)
- User isolation via Eloquent relationships
- Remember me functionality
- Password confirmation for sensitive actions

**Why not Laravel Sanctum?**
- Sanctum is for API tokens (SPA or mobile apps)
- This is a traditional web app with Inertia
- Session auth is more appropriate and secure

---

### 5. Frontend Architecture

**Decision:** Vue 3 + Composition API + Tailwind CSS

**Component Structure:**
```
resources/js/Pages/
├── ShippingLabels/
│   ├── Index.vue    // List with filters and pagination
│   ├── Create.vue   // Multi-step form with rate preview
│   └── Show.vue     // Details view with PDF preview
├── Dashboard.vue    // Statistics and quick actions
└── Auth/*          // Breeze auth pages (Login, Register, etc.)

resources/js/Components/
├── ApplicationLogo.vue
├── PrimaryButton.vue
└── TextInput.vue    // Reusable form components

resources/js/Layouts/
├── AuthenticatedLayout.vue  // Main app layout
└── GuestLayout.vue          // Auth pages layout
```

**Reasoning:**
- **Vue 3:** Modern, performant, excellent TypeScript support (future-ready)
- **Composition API:** Better code organization and reusability than Options API
- **Tailwind CSS:** Rapid UI development, consistent design system, no CSS conflicts
- **Inertia.js:** No API needed, data passed directly from controller as props
- **Component-based:** Reusable UI components for consistency

**Key Features Implemented:**
- Reactive forms with client-side validation
- Optimistic UI updates for better UX
- Real-time search filtering without page reload
- Pagination with query string preservation
- Dark mode support via Tailwind classes
- Mobile-responsive design (mobile-first)

---

### 6. Validation Strategy

**Decision:** Dual validation (backend + frontend)

**Backend:** `CreateShippingLabelRequest` (Form Request)
```php
Rules:
- US states: Must be 2-letter codes (CA, NY, TX...)
- ZIP codes: Regex validation (12345 or 12345-6789)
- Weight: 0.1 to 1120 oz (70 lbs max)
- Required fields enforced with clear error messages
- Phone numbers: Optional, max 20 characters
```

**Frontend:** Vue + native HTML5 validation
```vue
Features:
- Select dropdowns for states (prevent invalid input)
- Number inputs with min/max attributes
- Required fields marked with asterisk
- Real-time error messages below inputs
- Submit button disabled during processing
```

**Reasoning:**
- **Security:** Never trust frontend, always validate backend (critical!)
- **UX:** Frontend validation provides instant feedback to users
- **DX:** Form Request keeps controller clean and testable
- **Consistency:** Same validation rules in both layers

---

### 7. Docker Infrastructure

**Decision:** Multi-container Docker setup with Docker Compose

**Architecture:**
```yaml
Services:
  app:        // PHP 8.3 + Laravel + Node.js
  mysql:      // MySQL 8.0 for persistence
  nginx:      // Web server (reverse proxy to PHP-FPM)

Networks:
  shipping_network (bridge)

Volumes:
  mysql_data (persistent)
  ./:/var/www (bind mount for development)
```

**Reasoning:**
- **Consistency:** Same environment for all developers (no "works on my machine")
- **Isolation:** Dependencies don't pollute host system
- **Portability:** Works on Windows, Mac, Linux without changes
- **Production-ready:** Same containers can be used in production
- **Easy onboarding:** New developers can start with `docker-compose up`

**Network Design:**
- Internal network: Containers communicate via service names (e.g., `mysql:3306`)
- External ports: 8000 (app), 3307 (mysql) exposed to host
- Volume mounts: MySQL data persisted, code shared for live reload

**Why Docker over Traditional LAMP?**
- Easier PHP version management
- No conflicts with system packages
- Reproducible builds
- CI/CD friendly

---

### 8. Error Handling Strategy

**Decision:** Multi-layer error handling with user-friendly messages

**Flow:**
```
1. Service Layer (EasyPostService)
   - Try-catch around API calls
   - Log technical errors to Laravel log
   - Throw exceptions with user-friendly messages

2. Controller Layer
   - Catch service exceptions
   - Return JSON errors for AJAX or redirect for forms
   - Preserve user input on validation errors

3. Frontend Layer
   - Display errors with alert() for MVP (simple)
   - Could upgrade to toast notifications (e.g., Notyf)
   - Show validation errors below form fields
```

**Example Flow:**
```php
EasyPost API Error (e.g., invalid address)
  → Caught in EasyPostService::createShipment()
  → Logged: Log::error('EasyPost API Error: ' . $e->getMessage())
  → Thrown as Exception with user message
  → Caught in ShippingLabelController::store()
  → Returned: response()->json(['success' => false, 'message' => ...])
  → Displayed in Create.vue: alert('Error creating label: ...')
```

**Logging Strategy:**
- API errors: Logged with full stack trace
- User errors: Not logged (e.g., validation failures)
- System errors: Logged and reported (e.g., database connection)

---

## 🔒 Security Considerations

### 1. User Isolation (Critical!)

**Implementation:**
```php
// ✅ CORRECT: Always scope queries to authenticated user
$labels = auth()->user()->shippingLabels()->get();

// ❌ WRONG: Never expose all labels
$labels = ShippingLabel::all(); // Security vulnerability!
```

**Enforced in:**
- Controller: All queries use `auth()->user()->shippingLabels()`
- Model: `belongsTo` relationship ensures proper scoping
- Routes: All label routes protected by `auth` middleware

**Why this matters:**
- Users must only see their own labels (privacy)
- Prevents unauthorized access to sensitive data (addresses, tracking)
- Complies with data protection regulations (GDPR, CCPA)

---

### 2. API Key Protection

**Implementation:**
```php
// ✅ Stored in .env file (not committed to Git)
EASYPOST_API_KEY=EZTK_...

// ✅ Accessed via config
config('services.easypost.api_key')

// ✅ Never exposed to frontend
// API calls only from backend (EasyPostService)
```

**Security measures:**
- `.env` in `.gitignore` (never committed)
- `.env.example` provided without real keys
- Config cached in production (`php artisan config:cache`)
- API key never sent to browser (JavaScript)

---

### 3. SQL Injection Prevention

**Implementation:**
- Eloquent ORM with parameter binding (automatic)
- No raw queries with user input
- Query builder uses prepared statements

**Example:**
```php
// ✅ SAFE: Parameter binding
$labels = ShippingLabel::where('user_id', $userId)->get();

// ❌ UNSAFE: String concatenation
$labels = DB::select("SELECT * FROM shipping_labels WHERE user_id = $userId");
```

---

### 4. XSS Prevention

**Implementation:**
- Vue automatically escapes HTML in templates
- Blade escapes output by default (`{{ $var }}`)
- CSP headers could be added for extra protection

**Example:**
```vue
<!-- ✅ SAFE: Automatically escaped -->
<p>{{ label.from_name }}</p>

<!-- ❌ UNSAFE: Raw HTML (don't use unless necessary) -->
<p v-html="label.from_name"></p>
```

---

### 5. CSRF Protection

**Implementation:**
- Laravel CSRF middleware enabled globally
- All POST/PUT/DELETE requests require CSRF token
- Axios configured to send token automatically

**Configuration:**
```javascript
// resources/js/bootstrap.js
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.withCredentials = true; // Send cookies
axios.defaults.withXSRFToken = true;   // Send CSRF token
```

---

## 📊 Performance Optimizations

### 1. Database Indexing

**Indexes created:**
```php
$table->index('user_id');        // For user queries
$table->index('tracking_code');  // For search
$table->index('status');         // For filters
$table->unique('easypost_shipment_id'); // For EasyPost lookups
```

**Why these indexes?**
- `user_id`: Every query filters by user (most common)
- `tracking_code`: Users search by tracking number
- `status`: Filters (active, cancelled) are common
- `easypost_shipment_id`: Unique constraint + fast lookups

### 2. Query Optimization

**Pagination:**
```php
// Paginate instead of loading all records
$labels = $query->paginate(10);
```

**Lazy Loading (Future):**
```php
// Could add eager loading if needed
$labels = ShippingLabel::with('user')->paginate(10);
```

### 3. Asset Optimization

**Vite configuration:**
- CSS/JS minification in production
- Code splitting for smaller bundles
- Tree shaking to remove unused code
- Asset versioning for cache busting

**Build command:**
```bash
npm run build  // Optimized production build
```

### 4. Caching (Future Enhancement)

**Could add:**
- Query result caching for expensive reports
- Rate limiting for API calls
- CDN for static assets

---

## 🎨 Design Patterns Used

### 1. **Service Layer Pattern**
- `EasyPostService` encapsulates API logic
- Controllers delegate business logic to services

### 2. **Repository Pattern** (Implicit)
- Eloquent Models act as repositories
- Could extract to explicit repositories if needed

### 3. **Form Request Pattern**
- `CreateShippingLabelRequest` handles validation
- Keeps controllers thin

### 4. **Factory Pattern**
- Model Factories for testing (future)
- Could generate test data easily

### 5. **Adapter Pattern**
- `EasyPostService` adapts SDK to our needs
- Hides third-party complexity

### 6. **Observer Pattern**
- Vue reactivity system
- Model events (could use for logging)

---

## 🚀 Assumptions Made

### 1. USPS Only
- **Assumption:** Only USPS shipping (not FedEx, UPS, DHL)
- **Reasoning:** Test requirement explicitly mentions "USPS label"
- **Implementation:** `getCheapestUSPSRate()` filters only USPS rates
- **Future:** Could add multi-carrier support

### 2. US Addresses Only
- **Assumption:** Domestic US shipping only (no international)
- **Reasoning:** Test explicitly requires "US addresses only"
- **Implementation:** Country hardcoded as 'US', state dropdown with 50 US states
- **Future:** International shipping requires customs forms

### 3. Cheapest Rate Auto-Selection
- **Assumption:** Users want the cheapest shipping option
- **Reasoning:** Simplify UX for MVP, most users prioritize cost
- **Implementation:** `getCheapestUSPSRate()` auto-selects lowest price
- **Future:** Allow manual rate selection

### 4. Single Package per Label
- **Assumption:** One label = one package
- **Reasoning:** Most common use case, simpler UX
- **Implementation:** Single parcel in `CreateShippingLabelRequest`
- **Future:** Multi-package shipments

### 5. English Language Only
- **Assumption:** US English interface
- **Reasoning:** US market focus, test doesn't mention i18n
- **Implementation:** All labels and messages in English
- **Future:** Add Spanish, French support

### 6. Docker for Development
- **Assumption:** Docker is available on developer machines
- **Reasoning:** Industry standard, easier onboarding
- **Implementation:** Complete `docker-compose.yml`
- **Future:** Also provide non-Docker setup docs

### 7. Test Environment
- **Assumption:** Using EasyPost test API keys
- **Reasoning:** Safe for development, no real charges
- **Implementation:** `.env.example` shows test key format
- **Future:** Production keys in production environment only

---

## 🔮 What I Would Do Next

### Short-term (1-2 weeks)

#### 1. Comprehensive Testing
```php
// Unit Tests
- EasyPostService (mock API calls)
- Validation rules
- Model relationships

// Feature Tests
- Complete label creation flow
- User isolation (critical!)
- Cancel/refund flow
- Authentication flows
```

#### 2. Address Book Feature
```php
// Database
- Create user_addresses table
- Store frequently used addresses

// UI
- Select from saved addresses
- Add new address to book
- Edit/delete saved addresses
```

#### 3. Manual Rate Selection
```vue
// Frontend
- Show all available rates with prices
- Radio buttons to select preferred rate
- Display estimated delivery time

// Backend
- Accept selected_rate_id in request
- Use specified rate instead of cheapest
```

#### 4. Email Notifications
```php
// Laravel Mail
- Send email when label created
- Include label PDF as attachment
- Tracking link in email

// Queue
- Use Laravel queues for async sending
- Don't block label creation
```

---

### Medium-term (1-2 months)

#### 5. Batch Label Creation
```php
// CSV Upload
- Accept CSV with multiple addresses
- Validate all rows before processing
- Create labels in background job
- Send email when batch complete

// UI
- Upload component with progress bar
- Preview addresses before submit
- Download results CSV
```

#### 6. Advanced Search & Filters
```vue
// Filters
- Date range picker (created_at)
- Price range (min/max rate)
- Multiple status selection
- City/state filters

// Export
- Export filtered results to CSV
- Export to PDF with label images
```

#### 7. Webhooks for Tracking
```php
// EasyPost Webhooks
- Register webhook URL with EasyPost
- Receive tracking updates
- Update label status in real-time
- Notify user when delivered

// Webhook Controller
- Verify webhook signature
- Process tracking events
- Queue notification jobs
```

#### 8. Address Validation
```php
// Before Label Creation
- Validate address via EasyPost API
- Show suggested corrections
- Reduce failed deliveries

// Service Method
EasyPostService::validateAddress($address)
```

---

### Long-term (3-6 months)

#### 9. Multi-Carrier Support
```php
// Database
- Add carrier_id field (support FedEx, UPS, DHL)
- Carrier comparison table

// UI
- Compare rates across carriers
- Filter by carrier
- Carrier preferences

// Service
- Abstract carrier interface
- Multiple service implementations
```

#### 10. Analytics Dashboard
```vue
// Statistics
- Total spent by month/year
- Most common destinations (heatmap)
- Average package weight
- Carrier cost comparison

// Charts
- Use Chart.js or ApexCharts
- Interactive visualizations
```

#### 11. Mobile App
```javascript
// Technology
- React Native or Flutter
- Share API with web app

// Features
- Scan barcode for tracking
- Mobile camera for address capture
- Print via Bluetooth printer
- Push notifications for delivery
```

#### 12. API for Integrations
```php
// RESTful API
- OAuth2 authentication
- Rate limiting
- API documentation (OpenAPI/Swagger)

// Use Cases
- E-commerce platform integration
- Third-party app integrations
- Automated shipping workflows
```

#### 13. International Shipping
```php
// Requirements
- Customs forms (CN22, CN23)
- HS codes for products
- Duties and taxes calculation
- Country-specific regulations

// Complexity
- Much more complex than domestic
- Would need dedicated sprint
```

---

## 📚 Technology Choices Explained

### Why Laravel 12?
- **Latest stable:** Released December 2024
- **Modern PHP 8.3:** Typed properties, enums, attributes
- **Excellent ecosystem:** Packages for everything
- **Built-in tools:** Eloquent, Queue, Cache, Events
- **Active community:** Easy to find help and resources
- **Long-term support:** 2 years security fixes

### Why Vue.js 3?
- **Lightweight:** Smaller bundle than React
- **Learning curve:** Easier for new developers
- **Composition API:** Modern, functional approach
- **TypeScript support:** Future-ready
- **Perfect with Inertia:** Official Inertia adapter

### Why Inertia.js?
- **No API boilerplate:** No need for REST endpoints
- **Type-safe props:** Data passed directly from controller
- **Simpler auth:** Session-based, no token management
- **SPA feel:** Page transitions without full reload
- **Laravel-first:** Made by Laravel community

### Why Tailwind CSS?
- **Utility-first:** Rapid prototyping
- **No naming conflicts:** No BEM or other methodologies needed
- **Consistent design:** Built-in design system
- **Tree-shaking:** Only used classes in production
- **Mobile-first:** Responsive by default

### Why MySQL?
- **Test requirement:** Specified in assignment
- **Proven technology:** Battle-tested at scale
- **Excellent Laravel support:** Native Eloquent support
- **JSON support:** MySQL 8.0 has good JSON functions
- **Free & open-source:** No licensing costs

### Why Docker?
- **Consistency:** Eliminates environment issues
- **Easy onboarding:** New dev setup in minutes
- **Portability:** Works on any OS
- **Production parity:** Same containers everywhere
- **Industry standard:** Expected in modern development

---

## 🎓 Lessons Learned

### Technical Insights

1. **EasyPost API is well-designed**
   - Clear documentation with examples
   - Good PHP SDK with type hints
   - Helpful test mode with realistic data
   - Webhook support for real-time updates

2. **Inertia.js is powerful**
   - Eliminates need for separate API
   - Great developer experience
   - Perfect for Laravel + Vue teams
   - Simpler than full SPA architecture

3. **Form validation is critical**
   - US state/ZIP validation prevents API errors
   - Backend validation is non-negotiable for security
   - Frontend validation improves UX significantly
   - Consistent rules between layers important

4. **Docker simplifies development**
   - No "works on my machine" issues
   - Easy to onboard new developers
   - Same environment for everyone
   - Cloud deployment is straightforward

### Business Insights

1. **Shipping is complex**
   - Many edge cases (PO boxes, military addresses)
   - Users need clear error messages
   - Cost is primary concern for most users
   - Tracking is essential feature

2. **User experience matters**
   - Form design affects completion rate
   - Clear CTAs increase conversions
   - Error recovery is crucial
   - Mobile usage is growing

---

## 📞 Questions for Product Owner

### Business Rules
1. **Label lifecycle:** What happens to cancelled labels? Soft delete or permanent?
2. **Credits/wallet:** Should users pre-pay or pay-as-you-go?
3. **Restrictions:** Any weight/size limits beyond USPS restrictions?
4. **Pricing:** Do we charge markup or pass-through EasyPost costs?

### Features
1. **International:** Timeline for international shipping support?
2. **Carriers:** Priority for adding FedEx, UPS, DHL?
3. **Returns:** Do we need return label generation?
4. **Insurance:** Should we offer shipment insurance?

### Compliance
1. **Data retention:** How long to keep address data?
2. **Privacy:** GDPR/CCPA compliance requirements?
3. **PCI:** If adding payments, need PCI compliance?
4. **Audit:** Need audit trail for all actions?

### Scale & Performance
1. **Expected volume:** Users per day? Labels per day?
2. **Peak times:** Any seasonal spikes?
3. **SLA requirements:** Uptime expectations?
4. **Geographic distribution:** Need CDN or multi-region deployment?

### Integration
1. **E-commerce:** Plans to integrate with Shopify, WooCommerce, etc.?
2. **Accounting:** Need QuickBooks or similar integration?
3. **Shipping software:** Integrate with existing shipping tools?
4. **Webhooks:** What events need webhook notifications?

---

## 📖 Further Reading

### Laravel
- [Laravel Documentation](https://laravel.com/docs)
- [Laravel Best Practices](https://github.com/alexeymezenin/laravel-best-practices)
- [Laravel Design Patterns](https://refactoring.guru/design-patterns)

### Vue.js
- [Vue.js 3 Guide](https://vuejs.org/guide/)
- [Vue Composition API](https://vuejs.org/guide/extras/composition-api-faq.html)
- [Vue Best Practices](https://vuejs.org/style-guide/)

### Inertia.js
- [Inertia.js Documentation](https://inertiajs.com/)
- [Inertia.js Laravel Adapter](https://inertiajs.com/server-side-setup)

### EasyPost
- [EasyPost API Docs](https://docs.easypost.com/)
- [EasyPost PHP SDK](https://github.com/EasyPost/easypost-php)

### Docker
- [Docker Best Practices](https://docs.docker.com/develop/dev-best-practices/)
- [Docker Compose Documentation](https://docs.docker.com/compose/)

---

**Document Version:** 1.0  
**Last Updated:** December 2024  
**Author:** Shipping Label Team

