# Activity package technical test

For this test you will be asked to build a simple Laravel package for logging
user activity on models, so they can see a history of their data.

## Introduction
It is a very useful feature of web applications to be able to show users how and
when their data was changed throughout the lifetime of their account. Laravel
has many features that make keeping track of activity easier through events.  
The package should be easy to use out of the box but contain some customisation
options to tailor it to specific applications.  
There will also be some follow-up questions asking how the package might be
improved with more development.

## Test specifications
There are a number of things that this package should have:
- The means to create a table for storing the user activity (with the possibility for the developer to change the migration)
- A model called `Action` that relates to this table and holds the logic for an individual event
    - An action should know the type of action (create, update, delete)
    - Each action should be related to a "performer" model and a "subject" model
        - For simplicity, you can assume that any application using this package would have a `users` table and that the `User` model is the "performer"
        - You can assume that the performer is the currently authenticated user
        - The "subject" model could be any/all models defined in the application (including the `User`)
    - An action should be able to output a translated string summarising the action, including whoever performed the action and the item that it was performed on
- A trait called `HasActions` to be added to the models that should have their events recorded into actions
    - The trait should allow the developer to access all actions performed on an item
    - The logic for generating actions based on Laravel events should be in the trait
- A trait called `PerformsActions` which can be added to the `User` to allow the developer to easily fetch the users activity
- Tests verifying the behaviour, the Laravel Package test framework [orchestra](https://packages.tools/testbench) is already installed and set up.

## Developing the package
Feel free to fork this repository to get a base for the package. Some needed
folders are already generated as well as some empty test cases to be filled in.

When you have completed it, the test suite should be passing, and it should be
usable in a new Laravel project.

### Requirements
This is a very basic project which doesn't require anything more than the
minimum for a Laravel project.  
You can get it running inside a
[Homestead](https://laravel.com/docs/9.x/homestead#main-content) virtual machine
or on any machine with the following software:
- PHP >= 8.0
- Composer >= 2.0
- SQlite with the PHP extension

### Installation
To get started with this project:
1. fork it into your GitHub account
2. Clone the forked repository onto your PC
3. `cd` into the cloned repository
4. Run `composer install`
5. Run `composer test` -> You should see all tests failing

## Considerations
There are a few things we are looking out for in the code:
- Self commenting code where possible and clear comments if not
- Sensible well-thought-out approach to the problems in the test
- Not reinventing the wheel, using existing functions/services when it makes sense
- Consideration for large scale data (millions of actions)
- Efficiency when it makes sense (i.e. no need to save a few milliseconds at the expense of readability)

## Questions for further development
This test should only be a very basic implementation of the package, not
production ready.  
Following are some questions about making it a more complete package and some
other considerations that could be included.

You may write the answers in this README as part of the submission. (If you want
to implement the answer to some of these questions in the code then feel free,
with some comments referencing the question and giving some context).

1. What aspects of this package could be customised with a config file
2. How would you go about storing more information about the event (i.e. what fields were updated, from what to what)?
3. How would you increase the security of the package, ensuring only authorised performers can see the activity for an item?
4. If a performer or item is deleted from the system, what would their actions say in their summary? How could this be improved?
5. Suppose the developer wants to record other types of actions that are more specific, i.e. "Task was completed by ____" how could that be implemented?
6. What should be considered when developing the package to scale?
7. What should happen when an event is triggered but there is no authenticated user, e.g. in a queued job?
8. If you had more time to work on this, how would you improve it?


## 1. What aspects of this package could be customised with a config file

This package supports customization through a configuration file. Key configurable options include:

### 1.1 We can configure log levels for each action type:

- `ACTIONS_CREATE_LOG_LEVEL`
- `ACTIONS_UPDATE_LOG_LEVEL`
- `ACTIONS_DELETE_LOG_LEVEL`

Each can be set to one of three levels:

| Level | Description                                 |
|-------|---------------------------------------------|
| 0     | Disable logging for this action type       |
| 1     | Enable logging without metadata             |
| 2     | Enable logging with metadata                 |

### 1.2 Alternatively, a single config `ACTIONS_LOG_LEVEL` can apply the same level to all action types, but I prefer the individual control.

### 1.3 We can customize which columns to exclude from the metadata log or from the summary view of returned metadata.

### 1.4 We can customize the database table name used for the actions model.

### 1.5 Support of polymorphic references allowing the "performer" to be any model class, not limited to users.

---

## 2. How would you go about storing more information about the event (i.e. what fields were updated, from what to what)?

- We can save a snapshot of the model state in the metadata column on delete, similar to how it is done on create.

---

## 3. How would you increase the security of the package, ensuring only authorised performers can see the activity for an item?

To ensure only authorized users can view activity logs:

- Use Laravel’s authorization policies to control access to action records.
- Implement query scopes in the `HasActions` trait to filter visible actions based on permissions.

---

## 4. If a performer or item is deleted from the system, what would their actions say in their summary? How could this be improved?

When a performer or item is deleted:

- The relation will return `null`.
- By default, the summary shows "Unknown" instead of the performer's name.
- This can be improved by using **soft deletes** to retain related information.

---

## 5. Suppose the developer wants to record other types of actions that are more specific, i.e. "Task was completed by ____" how could that be implemented?

To record more specific or custom actions, such as `"Task was completed by ____"`, you can extend the model with a method to provide custom action details:

```php
static::updated(function ($model) {
    $action = [];
    if (method_exists($model::class, 'customUpdateAction')) {
        $action = $model->customUpdateAction();
    }
    $actionType = $action['action_type'] ?? 'update';
    $metadata = $action['metadata'] ?? $model->getChanges();
    static::recordAction($actionType, $model, $metadata);
});
```

## 6. What should be considered when developing the package to scale?

When designing the package to scale effectively, consider the following:

- **Clarity:** Understand the *what*, *why*, *how*, *where*, *when*, and *who* of the data and actions being tracked.
- **Performance:** Optimize for speed and responsiveness to handle increasing loads.
- **Scalability & Sustainability:** Ensure the system can grow without sacrificing stability or maintainability.
- **Cost Efficiency:** Balance resource usage with budget constraints.
- **Reliability:** Design for fault tolerance and consistent uptime.

Additionally, focus on:

- **Database Indexing:** To speed up queries on large datasets.
- **Event-Driven Architecture:** Utilize events and queues/jobs to handle processing asynchronously.
- **Housekeeping:** Implement pruning, archiving, or cleanup strategies to manage log size over time.

---

## 7. What should happen when an event is triggered but there is no authenticated user, e.g. in a queued job?

In cases where an event is triggered without an authenticated user (such as in queued jobs or system processes):

- Log the event using a special identifier like `SYSTEM_ID` or `null`.
- Represent the performer with labels such as `"System"`, `"Web"`, or `"App"`.

---

## 8. If you had more time to work on this, how would you improve it?

If additional development time were available, potential improvements include:

- Expanding configuration options for more granular control.
- Enhancing security features and access control mechanisms.
- Improving performance through further optimization.
- Adding support for more detailed custom action types and richer metadata.
- Incorporating best practices around scalability and maintenance as outlined above.


## Using AI tools
There is no way to know if this test is completed by AI or not, but we know what an AI's solution would look like so this is an opportunity to show that you can write better code than AI.

## Links that may be useful
- https://laravel.com/docs/9.x/eloquent#events-using-closures
- https://www.archybold.com/blog/post/booting-eloquent-model-traits
- https://laravel.com/docs/9.x/eloquent-relationships
