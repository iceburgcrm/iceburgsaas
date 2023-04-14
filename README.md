IceburgCRM SaaS
===============

This is the repository for the IceburgCRM SaaS project, which powers [IceburgCRM.com](https://www.iceburgcrm.com/) and allows customers to host their own IceburgCRMs.

Screenshots:
<p>
<a href="https://www.iceburg.ca/images/screenst1.jpg" target="_blank">
	<img src="https://www.iceburg.ca/images/screens1.jpg" width="50" />
</a>
<a href="https://www.iceburg.ca/images/screens2.jpg" target="_blank">
	<img src="https://www.iceburg.ca/images/screens2.jpg" width="50" />
</a>
<a href="https://www.iceburg.ca/images/screens3.jpg" target="_blank">
	<img src="https://www.iceburg.ca/images/screens3.jpg" width="50" />
</a>
<a href="https://www.iceburg.ca/images/screens4.jpg" target="_blank">
	<img src="https://www.iceburg.ca/images/screens4.jpg" width="50" />
</a>
<a href="https://www.iceburg.ca/images/screens5.jpg" target="_blank">
	<img src="https://www.iceburg.ca/images/screens5.jpg" width="50" />
</a>
</p>

[Demo](https://www.iceburgcrm.com)

Features
--------

-   Host customer's IceburgCRMs
-   Built with Laravel JetStream, Socialite, Tailwinds, and DaisyUI
-   Integrates with Stripe for payment processing
-   Allows users to authenticate with GitHub using Socialite

Requirements
------------

-   PHP >= 8.1
-   Composer
-   MySQL >= 5.7 or MariaDB >= 10.2
-   Stripe API key
-   GitHub OAuth key for Socialite

Installation
------------

1.  Clone this repository.
2.  Copy `.env.example` to `.env` and configure the database settings, Stripe API key, and GitHub OAuth key for Socialite.
3.  Run `composer install` to install the PHP dependencies.
4.  Run `npm install` to install the Node.js dependencies.
5.  Run `php artisan key:generate` to generate an application key.
6.  Run `php artisan migrate` to run the database migrations.
7.  Run `npm run dev` to compile the assets.
8.  Serve the application with `php artisan serve` or use a web server like Apache or Nginx.
