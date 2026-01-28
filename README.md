# EduSphere – Student Social Academic Platform (PHP + MySQL)

EduSphere is a university-oriented platform designed to strengthen relationships between **students**, **professors**, and the **university** by combining social and academic tools in one place.

The project integrates four main modules:
1. **Social Network** (profiles, posts, groups)
2. **Forum** (open discussions and Q&A)
3. **Messaging App** (direct communication)
4. **NLP-powered Chatbot** (24/7 academic support)

## Why EduSphere?

Students often struggle with fragmented academic information and limited access to fast support. EduSphere aims to create a connected, supportive community where users can:
- share ideas and experiences,
- collaborate on academic projects,
- communicate more easily,
- get quick answers through an always-available chatbot.

## Core Modules

### 1) Social Network
- User profiles (students & professors)
- Timeline/feed for posts and interactions
- Groups based on interests and activities
- Community building features to increase engagement

### 2) Forum
- Open discussions for academic and non-academic topics
- Q&A style interactions between students and professors
- A space to share knowledge, solve problems, and collaborate

### 3) Messaging Application
- Direct private communication between users
- Helps reduce friction in student–student and student–professor interaction

### 4) Chatbot (NLP)
An advanced chatbot that provides instant answers about:
- university info and announcements,
- academic staff,
- schedules/program structure,
- general guidance for platform usage.

**NLP approach:** the chatbot uses Natural Language Processing techniques (e.g., similarity measures / TF-IDF-related ideas and string similarity) to improve matching and response quality.

## Expected Impact

- Improved collaboration and communication across the academic community
- Faster access to information and guidance
- A stronger sense of belonging for students through a shared community platform

## Tech Stack

- **Frontend:** HTML, CSS, JavaScript
- **Backend:** PHP
- **Database:** MySQL

## High-Level Architecture

- Client-side UI (HTML/CSS/JS) for interactions and content display
- PHP backend for authentication, business logic, and chatbot logic
- MySQL database for users, posts, groups, forum threads, messages, and chatbot knowledge/data

## Running the Project Locally (Typical)

> Exact steps may vary depending on your folder structure and environment.

### Requirements
- PHP (e.g., 7.4+ recommended)
- MySQL / MariaDB
- Local server (XAMPP / WAMP / MAMP or PHP built-in server)

### Setup
1. Import the database (if a `.sql` file exists in the repo)
2. Configure database credentials in the PHP config file (e.g., `config.php`)
3. Run the project using a local server (XAMPP/WAMP) or:
   ```bash
   php -S localhost:8000
   ```

   <img width="449" height="327" alt="image" src="https://github.com/user-attachments/assets/2b79ac03-86c6-4661-869c-56d31ab69cda" />

   <img width="536" height="193" alt="image" src="https://github.com/user-attachments/assets/3bb5f7bc-49d2-4be3-a8a2-2bfe93936c5b" />


