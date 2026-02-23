
## Usage

1. **Register** a new account on signup.php
2. **Login** with your credentials
3. **Send Direct Messages**: Select a user from the dropdown and type your message
4. **Create Group**: Click "Create Group" and give it a name
5. **Join Group**: Click "Join Group" and enter the group ID
6. **View Messages**: Messages refresh when you reload the page or send a message

## Security Notes

⚠️ **This is a demonstration app using early 2000s coding practices!**

For production use, you should implement:
- Password hashing (use `password_hash()` and `password_verify()`)
- Prepared statements to prevent SQL injection
- CSRF protection
- Input validation and sanitization
- HTTPS for secure communication
- XSS protection

## Troubleshooting

- **"Unable to open database"**: Make sure the folder has write permissions
- **Blank page**: Check PHP error logs, ensure SQLite extension is enabled
- **Session issues**: Ensure cookies are enabled in your browser

## Browser Compatibility

Works with all modern browsers: Chrome, Firefox, Safari, Edge
