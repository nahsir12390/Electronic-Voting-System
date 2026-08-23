# Electronic Voting System

## 1. Project Overview

This application is a secure, web-based electronic voting platform built with Laravel. It allows an administrator to create elections, manage candidates, register eligible voters, and monitor vote submissions. Voters can log in, view open elections, and cast a single vote per election.

The project is designed as a practical academic or business-ready voting system that demonstrates:

- Role-based access control
- Election lifecycle management
- Candidate and ballot management
- One-person-one-vote enforcement
- Vote receipt hashing for auditability
- Admin dashboard and voter dashboard
- Data integrity and activity tracking

The system is meant to be understandable and defendable during project presentation, demo, and technical discussion.

---

## 2. Why This Project Was Built

Modern voting systems need to be:

- Transparent
- Secure
- Easy to manage
- Traceable
- User-friendly

This project addresses those needs by combining a Laravel backend with a modern Blade + Tailwind frontend. It is useful for:

- Student union elections
- School elections
- Small organizational voting
- Internal committee selection
- Demo environments for election systems

It is not a national-scale government system, but it is a well-structured real-world voting system that shows how a voting platform can work in a production-style Laravel application.

---

## 3. Technology Stack

This application was built using the following technologies:

### Backend
- PHP 8.3
- Laravel 13
- Eloquent ORM
- Blade templating
- Laravel Fortify / authentication flow

### Frontend
- Blade templates
- Tailwind CSS
- Vite for asset bundling

### Database
- SQLite by default for local development
- Laravel database migrations and models

### Testing
- PHPUnit
- Pest PHP

### Package Management
- Composer for PHP dependencies
- npm for frontend assets

---

## 4. Project Architecture

The application follows the standard Laravel MVC architecture.

### Main folders

- app/
  - Controllers for admin and vote logic
  - Models for User, Election, Candidate, and Vote
- routes/
  - Web routes for home, login, dashboard, and admin actions
- resources/views/
  - Blade files for UI screens
- database/migrations/
  - Schema definitions for the database tables
- tests/
  - Feature tests covering admin voter functionality

### Core entities

#### User
The User model represents both administrators and voters.

Important fields:
- name
- email
- password
- is_admin
- email_verified_at

Admin users are flagged by the is_admin field. Voters are regular users without admin privileges.

#### Election
The Election model represents a ballot or election event.

Important fields:
- title
- description
- starts_at
- ends_at
- is_active
- show_results

The app uses the isOpen() method to determine if voting is currently allowed.

#### Candidate
Each candidate belongs to an election.

Fields include:
- election_id
- name
- position
- manifesto

#### Vote
Each vote is a record of one user's selection in one election.

Fields include:
- election_id
- candidate_id
- user_id
- vote_hash
- ip_hash
- user_agent_hash

The unique constraint on (election_id, user_id) ensures one vote per voter in each election.

---

## 5. How the Application Works

### User roles

1. Administrator
   - Creates elections
   - Adds candidates
   - Creates voter accounts
   - Deletes inactive or invalid accounts
   - Reviews vote totals and voter ledger

2. Voter
   - Logs in with an approved account
   - Views open elections
   - Selects a candidate
   - Submits one ballot

### Election lifecycle

The election object has schedule controls:

- starts_at: when voting begins
- ends_at: when voting ends
- is_active: whether the election is enabled

The system checks if an election is open through the method:

- is_active == true
- start time is not in the future
- end time is not in the past

If these conditions are met, the election is considered open for voting.

### Voting process

When a voter clicks a candidate:

1. The request is sent to the VoteController
2. The system checks whether the election is open
3. It validates the candidate belongs to that election
4. It checks if the voter has already voted in that election
5. If allowed, the vote is recorded
6. The system generates a hash receipt for the ballot

This prevents duplicate voting and keeps a verifiable ballot record.

### Audit/receipt system

A vote record is created with a generated SHA-256 hash using important identifying data such as:

- election id
- candidate id
- user id
- timestamp
- random token

This produces a unique receipt value stored as vote_hash.

It also stores hashed metadata for:
- client IP address
- user-agent string

This makes the application more traceable and suitable for basic audit scenarios.

---

## 6. Important Features

### 6.1 Admin election management
The administrator can:
- create elections
- edit election details
- set start and end dates
- activate or deactivate elections
- control whether results are visible
- delete elections

This is implemented in the ElectionController and the admin elections view.

### 6.2 Candidate management
Admin can:
- add candidate names
- add position titles
- include short manifestos
- update candidate details
- remove candidates if no votes exist

### 6.3 Voter management
Admin can:
- create voter accounts
- assign a name and email
- set a temporary password
- view voter status
- delete inactive accounts
- prevent deletion if the account has vote history

### 6.4 Dashboard for voters
The user dashboard shows:
- all elections
- election status
- candidate list
- number of votes received
- whether the user already voted
- receipt if already submitted

### 6.5 Results view
Admin can view:
- total votes per election
- per-candidate vote count
- percentage distribution
- voter ledger with names, candidate selections, and receipt hashes
- filtering by voter name/email and candidate

### 6.6 Security and validation rules
The app includes several safety checks:
- only admins can access admin routes
- only open elections accept votes
- candidates must belong to the proper election
- one vote per user per election
- user cannot delete a voter with existing vote records
- candidate with votes cannot be deleted
- request validation is enforced for form inputs

---

## 7. Core Pages and Their Purpose

### Home page
The landing page introduces the system and gives access to login and admin overview.

### Login and authentication
Users authenticate through the standard Laravel authentication system. Only verified accounts can access protected voting features.

### Dashboard page
Authenticated users see the list of all elections and can vote if eligible.

### Admin command center
This is the main administration page. It allows election creation, updates, and candidate roster management.

### Voter management page
Used for adding and reviewing approved voters.

### Election result page
Shows summary data and the voter ledger for a selected election.

---

## 8. Project Flow

### Admin flow
1. Admin logs in
2. Admin creates an election
3. Admin adds candidates
4. Admin creates voter accounts
5. Admin monitors vote count and election status
6. Admin reviews the final results

### Voter flow
1. Voter logs in
2. Voter visits dashboard
3. Voter sees open elections
4. Voter selects a candidate
5. Vote is validated and stored
6. Voter receives a ballot receipt hash

---

## 9. Main Routes

The system uses route groups to separate voter and admin functions.

### Public routes
- / : home page
- /login : user login

### Authenticated routes
- /dashboard : voter dashboard
- /elections/{election}/vote : cast vote

### Admin-only routes
- /admin/elections
- /admin/elections/{election}
- /admin/voters
- /admin/candidates/{candidate}

All admin routes enforce a check such as:
- abort_unless($request->user()?->is_admin, 403);

---

## 10. Database Design

The schema was created using migrations.

### users
Stores authentication details and role information.

### elections
Stores election details and timing configuration.

### candidates
Stores candidate details under an election.

### votes
Stores each submitted ballot and its receipt/hash information.

### Important database relation rules
- Election has many Candidates
- Election has many Votes
- Candidate belongs to one Election
- User has many Votes
- Vote belongs to one Election, one Candidate, one User

---

## 11. Security Considerations

This project demonstrates basic security features expected in a voting system:

- role-based administration
- input validation using Laravel request validation
- one vote per user per election via unique constraint
- secure hash generation for receipts
- vote metadata hashing for IP and user-agent
- middleware-protected admin routes
- prevention of deleting candidates or voters with recorded votes

Important note:
This is a practical academic voting system, not a complete government-grade cryptographic voting solution. It is appropriate for learning, simulation, and presentation, but it does not replace legal or enterprise-level voting infrastructure.

---

## 12. How It Was Built Step by Step

The project was built as a Laravel application using a typical modern development workflow:

1. Set up Laravel project
2. Configure database and environment settings
3. Create user authentication system
4. Add the is_admin field to users
5. Create elections, candidates, and votes tables
6. Build models and relationships
7. Create admin controllers for election and voter management
8. Build Blade views for dashboard and administration
9. Add vote submission logic and validation
10. Implement receipt hashing and one-vote enforcement
11. Add tests for key functional behavior
12. Style the interface using Tailwind
13. Run migrations and verify features

This means the application is a real software project with clean separation of concerns.

---

## 13. How to Set Up and Run the Application

### Requirements
- PHP 8.3+
- Composer
- Node.js and npm
- SQLite support enabled in PHP

### Installation steps

1. Open the project folder
2. Install PHP dependencies:

```bash
composer install
```

3. Copy the example environment file:

```bash
copy .env.example .env
```

4. Generate the app key:

```bash
php artisan key:generate
```

5. Run database migrations:

```bash
php artisan migrate
```

6. Install frontend dependencies:

```bash
npm install
```

7. Build frontend assets:

```bash
npm run build
```

8. Start the app:

```bash
php artisan serve
```

Then open the app in the browser at:

```text
http://localhost:8000
```

---

## 14. How to Use the Application

### For an administrator

1. Log in as an admin user
2. Go to the admin election page
3. Create a new election with title, dates, and activity status
4. Add candidates to the election
5. Create voter accounts
6. Wait for or open the election window
7. Review ballots and results from the admin result screen

### For a voter

1. Log in with a registered voter account
2. Open the dashboard
3. Select an open election
4. Choose a candidate
5. Submit the ballot
6. Receive the receipt hash after successful voting

### Things to note
- A voter cannot vote twice in the same election
- If an election is closed, voting is blocked
- When results are hidden, voters will not see live counts
- Admin may delete elections or candidates only when safe to do so

---

## 15. Example Use Case

Imagine the system is used for a student government election:

- Admin creates election called “Student Council Election”
- Admin adds 3 candidates with their positions and manifestos
- Admin creates approved student voter accounts
- Voting window opens for 3 days
- Students log in and cast votes
- Admin monitors turnout and final results
- Final result screen shows vote totals and receipt ledger

This is a realistic scenario the project perfectly demonstrates.

---

## 16. Testing and Validation

The project includes feature tests, especially around voter management and admin behavior.

Some tested scenarios include:
- admin can create voter account
- admin can view voter management page
- admin can delete a voter without recorded votes
- admin cannot delete a voter with recorded votes

This shows that the system is not only built but also validated through automated tests.

---

## 17. Strengths of the Project

This project is strong because it demonstrates several real software engineering principles:

- clean Laravel architecture
- role-based access control
- proper database schema design
- business logic validation
- audit-friendly vote storage
- user-centric dashboard
- simple but modern interface
- practical and defendable presentation value

---

## 18. Limitations

This project is still a learning and demo-oriented system. It does not include advanced protections such as:

- end-to-end cryptographic voting
- biometric identity verification
- multi-tier secure election infrastructure
- distributed consensus or blockchain-based verification
- legal-grade election compliance

However, it is a strong foundational system for understanding how an electronic voting system works in a real application.

---

## 19. Project Summary

This Electronic Voting System is a Laravel-based application for managing elections, candidates, and voter participation. The main purpose is to automate the voting process while keeping candidate management, voter access, and results tracking simple, digital, and manageable.

It is suitable for:
- demonstrations
- academic defense
- small organizational elections
- presentation projects
- learning secure application workflows

---

## 20. Final Defense Statement

This application was designed to show how a modern voting system can be implemented using a robust web framework. It combines user authentication, role separation, data validation, secure vote hashing, and result tracking into a single application. In short, it demonstrates how digital voting can be organized, monitored, and audited using practical software engineering principles.

A candidate presenting this project should explain that the project is not just a form-based app, but a structured voting platform with real workflow logic and database integrity checks.

---

## 21. Quick Project Summary for Presentation

- Type: Web-based electronic voting system
- Framework: Laravel
- Frontend: Blade + Tailwind CSS
- Database: SQLite
- Main actors: Admin and Voter
- Main function: Create elections, manage candidates, allow voting, track results
- Key security feature: One vote per election per user and hashed ballot receipt

---

## 22. Suggested Presentation Talking Points

When defending the project, a presenter can say:

- “This project was built to automate a voting process while maintaining accountability.”
- “It uses Laravel because it provides a clean MVC architecture and built-in security features.”
- “The app distinguishes between admin and voter roles to maintain control and integrity.”
- “The system prevents duplicate votes and stores ballot receipts in a verifiable form.”
- “It is a practical, scalable base for real-world voting systems.”

---

## 23. Conclusion

This project is a complete, study-friendly electronic voting system that demonstrates the logic, flow, and architecture behind a functional digital election platform. It is suitable for explanation, demonstration, and defense because it clearly shows how the system was developed, how it works, what it does, and how it can be used.
