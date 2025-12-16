# Company Website - PHP

A modern, responsive company website built with PHP, featuring a clean design and professional layout.

## Features

- **Responsive Design**: Works seamlessly on desktop, tablet, and mobile devices
- **Modern UI**: Clean, professional design with smooth animations
- **Multiple Pages**: Home, About, Services, and Contact pages
- **Contact Form**: Functional contact form with validation
- **Easy Customization**: Centralized configuration file for easy updates

## File Structure

```
Company Profile/
├── index.php          # Home page
├── about.php          # About us page
├── services.php       # Services page
├── contact.php        # Contact page with form
├── header.php         # Shared header component
├── footer.php         # Shared footer component
├── config.php         # Site configuration
├── assets/
│   ├── css/
│   │   └── style.css  # Main stylesheet
│   └── js/
│       └── script.js  # JavaScript functionality
└── README.md          # This file
```

## Setup Instructions

### 1. Local Development (XAMPP/WAMP/MAMP)

1. **Install a local server**:
   - Windows: [XAMPP](https://www.apachefriends.org/)
   - Mac: [MAMP](https://www.mamp.info/)
   - Linux: Install Apache and PHP

2. **Place files in web directory**:
   - XAMPP: `C:\xampp\htdocs\Company Profile\`
   - MAMP: `/Applications/MAMP/htdocs/Company Profile/`
   - Linux: `/var/www/html/Company Profile/`

3. **Start the server**:
   - XAMPP: Start Apache from the control panel
   - MAMP: Start servers from the application

4. **Access the website**:
   - Open browser and go to: `http://localhost/Company Profile/`

### 2. Configuration

Edit `config.php` to customize:
- Company name
- Email address
- Phone number
- Physical address
- Database settings (if needed)

### 3. Contact Form Setup

The contact form is ready to use. To enable email sending:

1. **For local testing**: The form will display a success message but won't send emails unless you configure a mail server.

2. **For production**: 
   - Uncomment the `mail()` function in `contact.php` (line 30)
   - Configure your server's mail settings
   - Or integrate with a service like PHPMailer, SendGrid, or Mailgun

### 4. Customization

#### Change Colors
Edit the CSS variables in `assets/css/style.css`:
```css
:root {
    --primary-color: #2563eb;    /* Main brand color */
    --secondary-color: #1e40af;  /* Secondary color */
    --accent-color: #3b82f6;     /* Accent color */
}
```

#### Update Content
- Edit page content directly in the PHP files
- Modify navigation links in `header.php`
- Update footer information in `footer.php`

#### Add Images
Replace placeholder images by:
1. Adding images to `assets/images/` folder
2. Updating the image paths in the PHP files

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers

## Technologies Used

- PHP 7.4+
- HTML5
- CSS3 (with CSS Variables)
- JavaScript (Vanilla JS)
- Font Awesome Icons

## License

This project is open source and available for use.

## Support

For questions or issues, please contact: info@yourcompany.com

