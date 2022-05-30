# Contact Form

This WordPress plugin provides the [contact_form] shortcode which renders a simple contact form on posts and pages. Also, the plugin adds an admin page where the form submission records can be viewed in a table.

## Implementation details

* `WP_List_Table` is extended for rendering WP admin 'native' table
* All output is escaped and translation ready, all input is sanitized
* Form submission is handled with AJAX, WP nonce is used to verify form submission origin
* Uploaded files are stored in `/upload` directory on the server, only file names are stored in database

## Styles and scripts build instructions

After downloading the code, npm dependencies should be installed in `contact-form` directory by running `npm install`. Afterwards, styles should be built with `npm run build:scss`, and scripts should be minimized with `npm run uglify:js`

