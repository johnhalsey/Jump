<p align="center"><img src="/public/images/jump-logo-no-bg.png" width="400" alt="Laravel Logo"></p>

<p align="center">
<a href="https://github.com/johnhalsey/Jump/actions"><img src="https://github.com/johnhalsey/Jump/actions/workflows/tests.yml/badge.svg" alt="Tests Status"></a>
</p>

## About Jump

Project management application built using Laravel, Ineria.js, React.js and TailwindCSS.

Users can create unlimited projects, unlimited tasks for those projects and invite unlimited users to contribute towards to those projects.

Only projedt owners can update any project settings and invite users.

## Tasks Statuses

Tasks can be set into one of 3 standard statuses, "To Do", "In Progress" or "Done".  These statuses belong to the project, 
which get created by default when a project is created, this is to allow the potential to offer customised statuses per 
project on a paid project plan, later down the line.

## Tasks

Tasks can be created directly in the "To Do" status.  Once a task has been created it is automatically given a reference code
and clicked into, to be edited.  The description will initially be blank, but can easily be edited by clicking into the 
descripton box, adding any text (TODO, add a WYSIWYG) and hitting save.

Users can also assign the task to any project user, or update its status.

Users can also add notes to the task, which will show latest at the top.

## Local Installation

You will ideally need Laravel valet or Herd set up on your machine.

- `git clone git@github.com:johnhalsey/Jump.git`
- `cd Jump`
- `composer install`
- `npm instll`
- `cp .env.example .env.example`
- `php artisan key:generate`
- Create a local database ideally called jump
- Update the database creds in the .env file (if you called the db something different)
- `php artisan migrate`
- `php artisan db:seed`
- `npm run dev`
- Open an additional terminal window and ensire you are in the jump directory
- `php artisan queue:work`


## Seeded data

You will now have seeded data in your database.

You can log in with `user0@example.com` (there is also user1, user2... up to user9)

Password: password.

user0 is teh standard projedt owner for all projects

## Running Tests

`php artisan test` will run the whole test suite
