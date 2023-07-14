# Crudwizard

[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE.md)
[![Latest Stable Version](https://img.shields.io/packagist/v/sentgine/crudwizard.svg)](https://packagist.org/sentgine/crudwizard)
[![Total Downloads](https://img.shields.io/packagist/dt/sentgine/crudwizard.svg)](https://packagist.org/packages/sentgine/crudwizard)

Crudwizard is a powerful Laravel package designed to simplify the process of generating CRUD (Create, Read, Update, Delete) functionality for your Laravel applications. With Crudwizard, you can effortlessly create and manage your database tables, models, controllers, views, and routes with just a few simple commands.

## Features

- Efficient CRUD Generation: Crudwizard provides a streamlined approach to generating CRUD operations, allowing you to create database migrations, models, controllers, views, test, factory, and routes in a matter of seconds.
- Customizable Templates: The package offers a range of customizable view, enabling you to tailor the generated code to match your project's specific requirements.

## Requirements

- Laravel 10.x or higher.
- PHP 8.0 or higher.
- Use Tailwind CSS to make your life easier

## Installation

(1) You can install the package via Composer by running the following command:

```bash
composer require sentgine/crudwizard
```
(2) You must publish the config and view files for better control.
```bash
php artisan vendor:publish --tag="crudwizard"
```
(3) Then, at the root of your laravel project, type the command below. It will ask you the name of the controller and the form fields.
```bash
php artisan crudwizard:generate
```
or

```bash
php artisan crudwizard:generate --resource="post"
```
(4) You can also specify a prefix. It will then create the controller under the Dev/Admin and the views under the folder dev/admin.

```bash
php artisan crudwizard:generate --resource="dev/admin"
```

(5) To get you started quickly, I would advise you to copy and paste the entire Tailwind CSS @layer component directive below. You can customize this later.

```css
@layer components {
    /* Common */
    .crudwizard-resource-container {
      @apply w-full p-10 grid grid-cols-1 gap-4;
    }
    .crudwizard-top-container {
        @apply grid grid-cols-1 gap-5 md:flex justify-between whitespace-nowrap;
    }
    .crudwizard-top-sub-container {
        @apply grid grid-cols-1 gap-3 md:flex;
    }
    .crudwizard-resource-title {
        @apply font-semibold text-2xl;
    }    

    /* Table */
    .crudwizard-table-container {
        @apply rounded-md border grid grid-cols-1 gap-3;
    }
    .crudwizard-table {
        @apply text-left w-full shadow-lg;
    }
    .crudwizard-thead {
        @apply bg-gray-700 text-white;
    }
    .crudwizard-body {
        @apply text-left;
    }
    .crudwizard-th {
        @apply p-2;
    }
    .crudwizard-th-action {
        @apply w-[200px];
    }
    .crudwizard-tr {
        @apply border-b;
    }
    .crudwizard-td {
        @apply px-2;
    }
    .crudwizard-td-action {
        @apply border p-2 px-3 grid grid-cols-1 gap-3  md:w-auto md:flex;
    }
    .crudwizard-pagination-container {
        @apply mt-2;
    }
    .crudwizard-resource-no-results {
        @apply text-left;
    }

    /* Form */
    .crudwizard-invalid-input {
        @apply border-2 border-red-600;
    }
    .crudwizard-invalid {
        @apply text-red-600 text-sm;
    }
    .crudwizard-form-container {
        @apply border p-5 w-full sm:w-8/12 md:w-7/12 lg:w-5/12;
    }
    .crudwizard-form {
        @apply grid grid-cols-1 gap-4;
    }
    .crudwizard-form-fieldset {
        @apply grid grid-cols-1 gap-2;
    }
    .crudwizard-form-text-input {
        @apply border py-1 px-2;
    }

    /* Buttons */
    .crudwizard-button {
        @apply text-white px-2 py-2 rounded text-sm uppercase flex justify-center items-center gap-2;
    }
    .crudwizard-back-button {
        @apply crudwizard-button bg-gray-100 hover:bg-gray-200 text-gray-800;
    }
    .crudwizard-save-button {
        @apply crudwizard-button bg-green-800 hover:bg-green-900;
    }
    .crudwizard-add-new-button {
        @apply crudwizard-button bg-blue-800 hover:bg-blue-900;
    }
    .crudwizard-show-button {
        @apply crudwizard-button bg-purple-800 hover:bg-purple-900 text-xs;
    } 
    .crudwizard-edit-button {
        @apply crudwizard-button bg-yellow-800 hover:bg-yellow-900 text-xs;
    }
    .crudwizard-delete-button {
        @apply crudwizard-button bg-red-800 hover:bg-red-900 text-xs;
    }
    .crudwizard-modal-delete-yes-button {
        @apply crudwizard-delete-button mt-5 min-w-[100px];
    }
    .crudwizard-modal-delete-cancel-button {
        @apply crudwizard-button bg-gray-500 mt-5 min-w-[100px];
    }
    
    /* Modal */
    .crudwizard-modal-container {
        @apply fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50;
    }
    .crudwizard-modal-subcontainer {
        @apply bg-white rounded-lg p-6 shadow-xl grid grid-cols-1 gap-2;
    }
    .crudwizard-heading {
        @apply text-lg font-bold;
    }
    .crudwizard-button-container {
        @apply flex justify-between;
    }
  }
```

(6) Make sure you run your Laravel local server.
```bash
php artisan serve
```

(7) Open another terminal, and run the command below to compile and build your frontend asssets.
```bash
npm run dev
```

## Changelog
Please see the [CHANGELOG](https://github.com/sentgine/crudwizard/CHANGELOG.md) file for details on what has changed.

## Security
If you discover any security-related issues, please email sentgine@gmail.com instead of using the issue tracker.

## Credits
Crudwizard is built and maintained by Adrian Alconera. Visit my [YOUTUBE](https://www.youtube.com/@sentgine) channel!

## License
The MIT License (MIT). Please see the [LICENSE](https://github.com/sentgine/crudwizard/LICENSE) file for more information.