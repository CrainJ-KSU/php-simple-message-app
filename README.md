# Messaging Application (MVC/OOP PHP)

A modern messaging application built using MVC (Model-View-Controller) architecture with Object-Oriented PHP, SQLite, and Bootstrap 5.

## Features
- User registration and login system
- Send direct messages to other users
- Create and join group chats
- View message history
- Clean MVC architecture with separation of concerns

## Requirements
- PHP 7.4 or higher (with SQLite support enabled)
- A web server (Apache, Nginx, or PHP's built-in server)
- Modern web browser

## Architecture

This application follows the MVC design pattern:

### Models (Data Layer)
- `User.php` - Handles user authentication and user data
- `Group.php` - Manages group creation and membership
- `Message.php` - Handles message creation and retrieval

### Views (Presentation Layer)
- `login.php` - Login page
- `signup.php` - Registration page
- `chat.php` - Main chat interface
- `create_group.php` - Group creation form
- `join_group.php` - Group joining interface
- `layouts/` - Reusable header and footer templates

### Controllers (Business Logic Layer)
- `AuthController.php` - Handles authentication (login, signup, logout)
- `ChatController.php` - Manages chat functionality and message sending
- `GroupController.php` - Controls group creation and joining

### Config
- `Database.php` - Singleton database connection class

## Installation Steps

1. **Extract all files** to your web server directory

2. **Ensure proper structure**:
   ```
   mvc-oop/
   ├── index.php
   ├── setup.php
   ├── config/
   ├── models/
   ├── controllers/
   ├── views/
   └── public/
   ```

3. **Run the setup script**:
    - Navigate to: `http://localhost/mvc-oop/setup.php`
    - You should see "Database Setup Complete!"

4. **Start using the application**:
    - Navigate to: `http://localhost/mvc-oop/index.php`
    - Create a new account or login with test accounts

5. **Default test accounts**:
    - Username: `john` / Password: `password123`
    - Username: `jane` / Password: `password123`
    - Username: `bob` / Password: `password123`

## URL Routing

The application uses a simple routing system via the `route` parameter:

- `index.php?route=auth/login` - Login page
- `index.php?route=auth/signup` - Signup page
- `index.php?route=auth/logout` - Logout
- `index.php?route=chat/index` - Chat interface
- `index.php?route=chat/sendMessage` - Send message (POST)
- `index.php?route=group/create` - Create group page
- `index.php?route=group/join` - Join group page

## Key Features of MVC Architecture

### Separation of Concerns
- **Models**: Handle all database operations and business logic
- **Views**: Pure presentation, no business logic
- **Controllers**: Connect models and views, handle user input

### Benefits
✅ **Maintainability**: Easy to update and maintain code  
✅ **Reusability**: Models and views can be reused  
✅ **Testability**: Each component can be tested independently  
✅ **Scalability**: Easy to add new features  
✅ **Team Collaboration**: Different developers can work on different layers

### Design Patterns Used
- **MVC Pattern**: Separates application into three interconnected components
- **Singleton Pattern**: Database connection (only one instance)
- **Front Controller Pattern**: Single entry point (index.php) handles all requests
- **Autoloading**: Automatic class loading without manual requires

## Comparison with Procedural Version

| Feature | Procedural | MVC/OOP |
|---------|-----------|---------|
| Structure | Everything in one file | Separated into Models, Views, Controllers |
| Code Reuse | Copy/paste code | Reusable classes and methods |
| Maintainability | Hard to maintain | Easy to maintain |
| Testing | Difficult | Easy to test individual components |
| Scalability | Limited | Highly scalable |
| Database | Direct queries in files | Centralized in Model classes |
| URL Structure | Multiple PHP files | Single entry point with routing |

## Security Notes

⚠️ **This is a demonstration application!**

For production use, you should implement:
- Password hashing using `password_hash()` and `password_verify()`
- Prepared statements to prevent SQL injection
- CSRF token protection
- Input validation and sanitization
- XSS protection (htmlspecialchars on all output)
- HTTPS for secure communication
- Session security (regenerate session IDs)
- Rate limiting for authentication

## Troubleshooting

- **"Unable to open database"**: Check folder write permissions
- **"Class not found"**: Verify folder structure matches expectations
- **Blank page**: Check PHP error logs, ensure all files are in correct directories
- **Session issues**: Ensure cookies are enabled in your browser

## Browser Compatibility

Works with all modern browsers: Chrome, Firefox, Safari, Edge

## License

This is an educational project demonstrating MVC architecture with PHP.