# Eventify — System Diagrams

> Auto-generated from codebase analysis. Render with any Mermaid-compatible viewer
> (GitHub, VS Code Mermaid Preview, Notion, Obsidian, etc.)

---

## Table of Contents

1. [Project Summary](#1-project-summary)
2. [Main Modules](#2-main-modules)
3. [Actors](#3-actors)
4. [Database Entities](#4-database-entities)
5. [ERD — Entity Relationship Diagram](#5-erd--entity-relationship-diagram)
6. [Use Case Diagram](#6-use-case-diagram)
7. [Activity Diagram](#7-activity-diagram)
8. [System Flowchart](#8-system-flowchart)

---

## 1. Project Summary

**Eventify** is a full-stack PHP/MySQL event management platform with role-based access control, a
commission negotiation workflow between organizers and admins, eSewa v2 payment integration, and a
Groq-powered AI chatbot. The app uses an MVC pattern with a single front controller (`index.php`),
PDO for database access, CSRF protection, bcrypt password hashing, and both session-based (web)
and token-based (API) authentication.

---

## 2. Main Modules

| # | Module | Description |
|---|--------|-------------|
| 1 | **Authentication** | Register, login, password reset via OTP, session & API token management |
| 2 | **Event Management** | Create/edit/delete events, admin approval, status lifecycle |
| 3 | **Commission Negotiation** | Organizer-Admin negotiation of platform fee before event goes live |
| 4 | **Ticket & Booking** | Ticket tiers, seat tracking, booking lifecycle |
| 5 | **Payment Processing** | Direct, eSewa v2 (HMAC-SHA256), Khalti, PayPal, Bank Transfer |
| 6 | **Search & Discovery** | Full-text MySQL search, category filtering, live autocomplete |
| 7 | **Reviews & Ratings** | 1–5 star ratings, comments, one review per attendee per event |
| 8 | **Analytics & Reporting** | Role-aware dashboards, revenue reports, bar charts |
| 9 | **AI Chatbot** | Groq LLM (llama-3.3-70b) for customer support |
| 10 | **REST API** | Token-authenticated JSON API for all core resources |

---

## 3. Actors

| Actor | Role | Key Capabilities |
|-------|------|-----------------|
| **Guest** | Unauthenticated visitor | Browse events, search, view details, register, login |
| **Attendee** | Registered user | Book tickets, pay, cancel bookings, rate events, view dashboard |
| **Organizer** | Event creator | Create/manage own events, negotiate commission, view revenue stats |
| **Admin** | Platform administrator | Approve/reject events, manage all users, view all bookings & revenue |
| **Vendor** | Future role | Defined in schema, not yet implemented |
| **Groq AI** | External AI service | Powers the chatbot assistant |
| **eSewa Gateway** | Payment processor | Handles eSewa v2 payment flow with HMAC verification |

---

## 4. Database Entities

12 tables: `users`, `event_categories`, `events`, `tickets`, `bookings`, `payments`,
`waitlist`, `reviews`, `password_reset_tokens`, `api_tokens`, `event_negotiations`, `audit_logs`

---

## 5. ERD — Entity Relationship Diagram

```mermaid
erDiagram
    USERS ||--o{ EVENTS : "organizes"
    USERS ||--o{ BOOKINGS : "makes"
    USERS ||--o{ REVIEWS : "writes"
    USERS ||--o{ WAITLIST : "joins"
    USERS ||--o{ PASSWORD_RESET_TOKENS : "requests"
    USERS ||--o{ API_TOKENS : "owns"
    USERS ||--o{ AUDIT_LOGS : "generates"
    EVENT_CATEGORIES ||--o{ EVENTS : "classifies"
    EVENTS ||--o{ TICKETS : "has tiers"
    EVENTS ||--o{ BOOKINGS : "receives"
    EVENTS ||--o{ REVIEWS : "receives"
    EVENTS ||--o{ WAITLIST : "has"
    EVENTS ||--|| EVENT_NEGOTIATIONS : "has one"
    TICKETS ||--o{ BOOKINGS : "used in"
    BOOKINGS ||--o{ PAYMENTS : "paid via"

    USERS {
        int id PK
        varchar name
        varchar email "UNIQUE"
        varchar phone
        varchar password_hash
        enum role "admin,organizer,attendee,vendor"
        varchar profile_picture
        tinyint is_verified
        tinyint is_active
        timestamp created_at
        timestamp updated_at
    }

    EVENT_CATEGORIES {
        tinyint id PK
        varchar name "UNIQUE"
        varchar slug "UNIQUE"
        timestamp created_at
    }

    EVENTS {
        int id PK
        int organizer_id FK
        tinyint category_id FK
        varchar title
        varchar slug "UNIQUE"
        text description
        varchar venue
        varchar city
        datetime date_start
        datetime date_end
        smallint capacity
        varchar cover_image
        tinyint is_recurring
        enum recurrence "none,daily,weekly,monthly"
        enum status "draft,published,cancelled,completed"
        timestamp created_at
        timestamp updated_at
    }

    TICKETS {
        int id PK
        int event_id FK
        varchar name
        decimal price
        smallint quantity
        smallint quantity_sold
        datetime sale_start
        datetime sale_end
        timestamp created_at
    }

    BOOKINGS {
        int id PK
        varchar booking_ref "UNIQUE EVT-XXXXXX"
        int attendee_id FK
        int event_id FK
        int ticket_id FK
        tinyint quantity
        decimal total_amount
        enum status "pending,confirmed,cancelled,attended"
        varchar qr_code
        timestamp cancelled_at
        timestamp created_at
        timestamp updated_at
    }

    PAYMENTS {
        int id PK
        int booking_id FK
        decimal amount
        varchar currency
        enum method "card,esewa,khalti,paypal,bank_transfer,promo_code"
        varchar gateway_txn_id
        enum status "pending,success,failed,refunded"
        timestamp paid_at
        timestamp created_at
    }

    WAITLIST {
        int id PK
        int event_id FK
        int user_id FK
        timestamp created_at
    }

    REVIEWS {
        int id PK
        int event_id FK
        int user_id FK
        tinyint rating "CHECK 1-5"
        text comment
        timestamp created_at
    }

    PASSWORD_RESET_TOKENS {
        int id PK
        int user_id FK
        varchar token_hash
        datetime expires_at
        tinyint is_used
        timestamp created_at
    }

    API_TOKENS {
        int id PK
        int user_id FK
        varchar token_hash "UNIQUE SHA256"
        datetime expires_at
        timestamp created_at
    }

    EVENT_NEGOTIATIONS {
        int id PK
        int event_id FK "UNIQUE"
        decimal organizer_offer_percent
        decimal admin_counter_percent
        decimal agreed_percent
        varchar organizer_note
        varchar admin_note
        enum status "pending_admin,countered,agreed,rejected"
        timestamp created_at
        timestamp updated_at
    }

    AUDIT_LOGS {
        bigint id PK
        int user_id FK
        varchar action
        varchar entity_type
        int entity_id
        varchar ip_address
        json meta
        timestamp created_at
    }
```

---

## 6. Use Case Diagram

```mermaid
graph LR
    Guest(["👤 Guest"])
    Attendee(["👤 Attendee"])
    Organizer(["👤 Organizer"])
    Admin(["👤 Admin"])
    Groq(["🤖 Groq AI"])
    eSewa(["💳 eSewa Gateway"])

    subgraph PUB["Public — No Login Required"]
        UC1["Browse & Search Events"]
        UC2["View Event Details & Reviews"]
        UC3["Use AI Chatbot"]
        UC4["Register Account"]
        UC5["Login"]
        UC6["Forgot / Reset Password"]
    end

    subgraph ATT["Attendee Features"]
        UC7["Book Tickets"]
        UC8["Checkout & Pay"]
        UC9["View My Bookings"]
        UC10["Cancel Booking"]
        UC11["Rate & Review Event"]
        UC12["Edit Profile"]
        UC13["View Attendee Dashboard"]
    end

    subgraph PAY["Payment Methods"]
        UC8a["Pay via eSewa v2"]
        UC8b["Pay Direct / Cash"]
        UC8c["Pay via Khalti / PayPal"]
    end

    subgraph ORG["Organizer Features"]
        UC14["Create Event + Ticket Tiers"]
        UC15["Edit / Delete Own Event"]
        UC16["Submit Commission Offer"]
        UC17["Accept or Reject Counter Offer"]
        UC18["View Organizer Dashboard"]
    end

    subgraph ADM["Admin Features"]
        UC19["View Admin Dashboard"]
        UC20["Manage All Users"]
        UC21["Enable / Disable User Account"]
        UC22["Manage All Events"]
        UC23["Approve or Reject Event"]
        UC24["Counter Commission Offer"]
        UC25["View Revenue Reports"]
        UC26["View All Bookings"]
    end

    Guest --> UC1
    Guest --> UC2
    Guest --> UC3
    Guest --> UC4
    Guest --> UC5
    Guest --> UC6

    Attendee --> UC1
    Attendee --> UC2
    Attendee --> UC3
    Attendee --> UC5
    Attendee --> UC7
    Attendee --> UC8
    Attendee --> UC9
    Attendee --> UC10
    Attendee --> UC11
    Attendee --> UC12
    Attendee --> UC13

    Organizer --> UC1
    Organizer --> UC2
    Organizer --> UC3
    Organizer --> UC5
    Organizer --> UC7
    Organizer --> UC9
    Organizer --> UC12
    Organizer --> UC14
    Organizer --> UC15
    Organizer --> UC16
    Organizer --> UC17
    Organizer --> UC18

    Admin --> UC5
    Admin --> UC1
    Admin --> UC19
    Admin --> UC20
    Admin --> UC21
    Admin --> UC22
    Admin --> UC23
    Admin --> UC24
    Admin --> UC25
    Admin --> UC26

    UC8 --> UC8a
    UC8 --> UC8b
    UC8 --> UC8c

    UC8a <--> eSewa
    UC3 <--> Groq
```

---

## 7. Activity Diagram

```mermaid
flowchart TD
    Start(["🚀 User Visits Eventify"])

    Start --> Home["View Homepage\n— Featured Events —"]
    Home --> IsAuth{Logged In?}

    IsAuth -->|No| AuthOpt{"Choose\nAction"}
    AuthOpt -->|Register| RegForm["Fill Registration Form\nname · email · phone · password · role"]
    RegForm --> ValidReg{Input Valid?\nEmail unique?}
    ValidReg -->|No - show errors| RegForm
    ValidReg -->|Yes| HashPwd["Hash Password\nbcrypt cost=12"]
    HashPwd --> SaveUser["Insert User\nis_verified=0 is_active=1"]
    SaveUser --> LoginPage["Redirect → Login"]

    AuthOpt -->|Login| LoginPage
    LoginPage --> Credentials["Submit Email + Password"]
    Credentials --> ValidCreds{Credentials\nValid?}
    ValidCreds -->|No| LoginPage
    ValidCreds -->|Yes| GenSession["Regenerate Session ID\nSet user_id · role · name · email"]
    GenSession --> RoleRoute{Role?}
    RoleRoute -->|Admin| AdminDash["Admin Dashboard"]
    RoleRoute -->|Organizer| OrgDash["Organizer Dashboard"]
    RoleRoute -->|Attendee| BrowseEvents

    AuthOpt -->|Browse anyway| BrowseEvents

    AuthOpt -->|Forgot Password| ForgotForm["Enter Email Address"]
    ForgotForm --> GenToken["Generate Reset Token\nSend Email with Link"]
    GenToken --> OTPVerify["Verify OTP"]
    OTPVerify --> ResetForm["New Password Form"]
    ResetForm --> UpdatePwd["Hash & Save Password\nMark Token Used"]
    UpdatePwd --> LoginPage

    IsAuth -->|Yes| BrowseEvents
    BrowseEvents["Browse Published Events\n— 6 per page —"]
    BrowseEvents --> SearchFilter{Apply\nFilters?}
    SearchFilter -->|Category / City / Text| FilteredList["Filtered Results\nFull-text MySQL Search"]
    SearchFilter -->|No filter| EventList["Event Card List"]
    FilteredList --> EventList

    EventList --> PickEvent["Select an Event"]
    PickEvent --> EventDetail["View Event Detail\nTickets · Venue · Reviews · Rating"]

    EventDetail --> WantBook{Book\nTickets?}
    WantBook -->|No| BrowseEvents

    WantBook -->|Yes| AuthForBook{Authenticated?}
    AuthForBook -->|No| LoginPage
    AuthForBook -->|Yes| SelectTier["Select Ticket Tier & Quantity\nmax 5 per order"]

    SelectTier --> SeatsOK{Seats\nAvailable?}
    SeatsOK -->|No| Waitlist["Join Waitlist"]
    Waitlist --> BrowseEvents

    SeatsOK -->|Yes| Checkout["Checkout Page\nOrder Summary + Total"]
    Checkout --> PayChoice{Payment\nMethod?}

    PayChoice -->|Direct / Cash| DirectConfirm["Create CONFIRMED Booking\nDecrement quantity_sold"]

    PayChoice -->|eSewa| CreatePending["Create PENDING Booking\nReserve Seats"]
    CreatePending --> HMACSign["Generate HMAC-SHA256\nSignature + Params"]
    HMACSign --> EsewaRedirect["Redirect → eSewa\nPayment Gateway"]
    EsewaRedirect --> EsewaResult{Payment\nOutcome?}

    EsewaResult -->|Failure| CancelPending["Cancel Pending Booking\nRestore Seats"]
    CancelPending --> Checkout

    EsewaResult -->|Success| VerifyHMAC["Verify Response\nHMAC Signature"]
    VerifyHMAC --> HMACOk{Signature\nValid?}
    HMACOk -->|No| CancelPending
    HMACOk -->|Yes| VerifyAPI["Confirm Status with\neSewa API"]
    VerifyAPI --> APIConfirmed{Gateway\nConfirmed?}
    APIConfirmed -->|No| CancelPending
    APIConfirmed -->|Yes| DirectConfirm

    DirectConfirm --> RecordPayment["Record Payment\ngateway_txn_id · method · status=success"]
    RecordPayment --> GenRef["Generate Booking Ref\nEVT-XXXXXX"]
    GenRef --> GenQR["Generate QR Code"]
    GenQR --> ConfirmPage["Booking Confirmation Page\nRef · QR · Event Details"]

    ConfirmPage --> PostActions{Next Action?}
    PostActions -->|View All Bookings| MyBookings["My Bookings\nUpcoming / Past Tabs"]
    PostActions -->|Browse More| BrowseEvents

    MyBookings --> BookingAction{Action?}
    BookingAction -->|Cancel| CancelBooking["Update status=cancelled\ncancelled_at = NOW()"]
    CancelBooking --> MyBookings

    BookingAction -->|Review| EventDone{Event\nCompleted?}
    EventDone -->|No - can't review yet| MyBookings
    EventDone -->|Yes| RateForm["Rate & Review Form\n1-5 Stars + Comment"]
    RateForm --> SubmitReview["Save Review\nOne per attendee per event"]
    SubmitReview --> MyBookings

    OrgDash --> OrgActions{Action?}
    OrgActions -->|Create Event| EventForm["Event Creation Form\ntitle · venue · date · tickets · image"]
    EventForm --> ValidEvent{Valid?}
    ValidEvent -->|No| EventForm
    ValidEvent -->|Yes| SaveDraft["Save Event status=draft\nCreate EVENT_NEGOTIATIONS\nstatus=pending_admin"]
    SaveDraft --> AwaitAdmin["Awaiting Admin Review"]

    OrgActions -->|Edit or Delete| ManageOwn["Edit or Delete Own Events\nOwnership Verified"]
    ManageOwn --> OrgDash

    OrgActions -->|Commission| CommStatus["View Negotiation Status"]
    CommStatus --> OrgDecision{Admin\nCountered?}
    OrgDecision -->|Yes - Accept| OrgAccept["Accept Counter Offer\nstatus=agreed → event published"]
    OrgDecision -->|Yes - Reject| OrgReject["Reject Counter\nstatus=rejected"]
    OrgAccept --> OrgDash
    OrgReject --> OrgDash

    AdminDash --> AdminActions{Action?}

    AdminActions -->|Review Commissions| CommList["View Commission Offers\npending_admin queue"]
    CommList --> AdminDecision{Decision?}
    AdminDecision -->|Accept| AcceptComm["agreed_percent saved\nEvent → status=published"]
    AdminDecision -->|Counter| CounterOffer["Set admin_counter_percent\nstatus=countered\nNotify Organizer"]
    AdminDecision -->|Reject| RejectComm["status=rejected\nEvent → cancelled"]

    AcceptComm --> AdminDash
    CounterOffer --> AwaitAdmin
    RejectComm --> AdminDash

    AdminActions -->|Manage Users| UserList["User List\nSearch by name / email / role"]
    UserList --> ToggleUser["Enable / Disable Account\nis_active toggle"]
    ToggleUser --> AdminDash

    AdminActions -->|Manage Events| AllEvents["All Events Table\nFilter by status / category"]
    AllEvents --> EventOps["Edit · Delete · Approve"]
    EventOps --> AdminDash

    AdminActions -->|Revenue Report| RevenueChart["Revenue by Event\nBar Chart + Table"]
    RevenueChart --> AdminDash

    End(["🔚 Logout / Session Expires"])
    MyBookings -.->|Logout| End
    OrgDash -.->|Logout| End
    AdminDash -.->|Logout| End
```

---

## 8. System Flowchart

```mermaid
flowchart TD
    HTTPReq(["🌐 HTTP Request\nindex.php or api/index.php"])

    HTTPReq --> SessionStart["Start / Resume PHP Session\nsession_helper.php"]
    SessionStart --> IdleCheck{Session\nIdle > 2h?}
    IdleCheck -->|Yes| DestroySession["Destroy Session\nRedirect → Login"]
    IdleCheck -->|No| UpdateActivity["Update last_activity timestamp"]

    UpdateActivity --> Router{Request\nType?}

    Router -->|Web page= param| WebRouter["index.php Router\nSwitch on page param"]

    WebRouter --> PageType{Page Group?}

    PageType -->|auth/*| AuthCtrl["AuthController\nlogin · register · forgot · reset · OTP"]
    PageType -->|events/*| EventCtrl["EventController\nbrowse · detail · create · edit · delete"]
    PageType -->|attendee/*| BookCtrl_A["BookingController\ndashboard · bookings · checkout · confirm · cancel · rate"]
    PageType -->|organizer/*| EventCtrl_O["EventController\ndashboard · commission"]
    PageType -->|admin/*| EventCtrl_Ad["EventController\nusers · events · commission · revenue · bookings"]
    PageType -->|esewa/*| EsewaHandler["eSewa Redirect Handler\nsuccess · failure"]

    AuthCtrl --> RoleGuard{Auth\nRequired?}
    EventCtrl --> RoleGuard
    BookCtrl_A --> RoleGuard
    EventCtrl_O --> RoleGuard
    EventCtrl_Ad --> RoleGuard

    RoleGuard -->|Not logged in| RedirectLogin["Redirect → Login"]
    RoleGuard -->|Wrong role| RedirectHome["Redirect → Home / 403"]
    RoleGuard -->|Authorized| CSRFCheck{POST\nRequest?}

    CSRFCheck -->|Yes| ValidCSRF{CSRF\nToken Valid?}
    ValidCSRF -->|No| CSRFError["403 CSRF Error"]
    ValidCSRF -->|Yes| Controller
    CSRFCheck -->|GET| Controller

    Router -->|API /api/index.php| APIRouter["API Router\nCORS Headers\nresource param dispatch"]

    APIRouter --> APIResource{Resource?}
    APIResource -->|auth| AuthAPI["api/auth.php\nlogin · register · logout · me"]
    APIResource -->|events| EventsAPI["api/events.php\nCRUD + approve + reviews"]
    APIResource -->|bookings| BookingsAPI["api/bookings.php\ncreate · cancel · stats · all"]
    APIResource -->|tickets| TicketsAPI["api/tickets.php\nlookup by booking / ref"]
    APIResource -->|users| UsersAPI["api/users.php\nprofile read / update"]
    APIResource -->|reviews| ReviewsAPI["api/reviews.php\ncreate · list by event"]
    APIResource -->|categories| CatsAPI["api/categories.php\nlist all categories"]
    APIResource -->|search| SearchAPI["api/search.php\nautocomplete suggestions"]
    APIResource -->|chatbot| ChatbotAPI["api/chatbot.php\nGroq AI message relay"]

    AuthAPI --> TokenCheck{Bearer\nToken?}
    EventsAPI --> TokenCheck
    BookingsAPI --> TokenCheck
    TicketsAPI --> TokenCheck
    UsersAPI --> TokenCheck
    ReviewsAPI --> TokenCheck

    TokenCheck -->|Missing or Expired| Return401["401 Unauthorized"]
    TokenCheck -->|Valid| APIController

    SearchAPI --> APIController
    CatsAPI --> APIController
    ChatbotAPI --> APIController

    Controller["Controller Method\nbusiness logic + validation"]
    APIController["API Handler\nbusiness logic + validation"]

    Controller --> ModelLayer
    APIController --> ModelLayer

    ModelLayer["Model Layer\nUser · Event · Booking · Ticket · EventNegotiation"]
    ModelLayer --> DBQuery["PDO Prepared Statement\nParameterized Query"]
    DBQuery --> MySQL[("MySQL 8\nEventify Database")]

    MySQL --> QueryResult["Query Result\nrows / affected count"]
    QueryResult --> BusinessLogic{Business\nDecision?}

    BusinessLogic -->|Event CRUD| EventLogic["Create Draft\nValidate Ownership\nUpdate Status"]
    BusinessLogic -->|Booking Create| BookLogic["Check Seat Availability\nCreate Pending Booking\nDecrement quantity_sold"]
    BusinessLogic -->|Payment| PayLogic["eSewa HMAC Sign\nGateway Redirect\nVerify Callback\nRecord Payment"]
    BusinessLogic -->|Commission| CommLogic["Organizer → pending_admin\nAdmin → accept / counter / reject\nPublish on agreed"]
    BusinessLogic -->|Auth| AuthLogic["Bcrypt Verify\nSession Regenerate\nToken Generate / Revoke"]

    EventLogic --> Response
    BookLogic --> Response
    PayLogic --> Response
    CommLogic --> Response
    AuthLogic --> Response

    Response{Response\nType?}
    Response -->|Web| RenderView["Render PHP View\nviews/*/*.php with layout"]
    Response -->|API| JSONResponse["JSON Response\n{message, data, errors}"]
    Response -->|Redirect| HTTPRedirect["HTTP 302 Redirect"]

    RenderView --> Browser["Browser renders HTML\nVanilla JS + Material Icons\nCSS Design System"]
    JSONResponse --> APIClient["API Client\nFetch / Mobile App"]
    HTTPRedirect --> HTTPReq

    PayLogic <-->|HMAC + Payment| EsewaGW["eSewa v2\nrc-epay.esewa.com.np"]
    ChatbotAPI <-->|LLM Query| GroqAI["Groq API\nllama-3.3-70b-versatile"]
    AuthLogic <-->|Send Email| Mailer["PHP mail()\nPassword Reset OTP"]
    BookLogic <-->|Upload Image| FileSystem["public/uploads/\nEvent Cover Images"]
```

---

*Generated on 2026-05-19 — Eventify Event Management System*
