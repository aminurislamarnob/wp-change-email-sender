### PR Todo
Describe your PR's. It could be a checklist or description

# PR Guidelines

### Features:

* [ ] My code satisfies feature requirements.
* [ ] I update README file to track the core file changes of the dependant plugins.
* [ ] I use localized text everywhere.

### Coding Standards:
* [ ] My code is readable.
* [ ] My code follow the [WordPress coding standards](https://make.wordpress.org/core/handbook/best-practices/coding-standards/).
* [ ] My code has proper inline documentation.
* [ ] My code follows KISS: Keep it simple, Sweetie (not stupid!).
* [ ] My code follows DRY: Don't Repeat Yourself.
* [ ] I check Grammar and spelling errors.
* [ ] I use [Semantic HTML5](https://www.semrush.com/blog/semantic-html5-guide/#types-of-html-semantic-tags).
* [ ] Padding, margin, font size, font family, adn etc are same for the same HTML component.

### Code Security:
* [ ] My code use [nonce](https://developer.wordpress.org/apis/security/nonces/) to  protect against several types of attacks including CSRF .
* [ ] User data is properly [sanitized](https://developer.wordpress.org/apis/security/sanitizing/).
* [ ] User data is properly [validated](https://developer.wordpress.org/apis/security/data-validation/) e.i. a field is required or not.
* [ ] Data is properly [escaped](https://developer.wordpress.org/apis/security/escaping/) to prevent malformed HTML or script tags.
* [ ] I check  user's [roles and permission](https://developer.wordpress.org/apis/security/user-roles-and-capabilities/) properly.
* [ ] I use [prepare function](https://developer.wordpress.org/reference/classes/wpdb/prepare/) for SQL to prevent the [SQL injection](https://developer.wordpress.org/apis/security/common-vulnerabilities/#sql-injection).
* [ ] Use the  [Plugin Check](https://wordpress.org/plugins/plugin-check/) plugin to check your plugin's coding standards, securities, etc.