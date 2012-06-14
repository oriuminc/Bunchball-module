Bunchball Module Readme
=======================
This module provides an API and architecture for sending website events to
the Bunchball gamification service.

Administration
--------------
/admin/config/people/bunchball

Plugins
-------

Other Available Modules - these plugins can be enabled an required to send
specific events to the Bunchball service.

-   bunchball

    The main module.  API and configuration screens only—doesn't send data to 
    Bunchball service.

-   bunchball_user_interaction

    Sends login, registration and user profile action information to the 
    Bunchball service. Defines plugin interface for other user-related plugins.

-   bunchball_groups

    Add user to a Bunchball group based on values of user profile fields.

-   bunchball_user_roles

    Add user to a Drupal role based on their Bunchball level.

-   bunchball_entities

    Send action information to the Bunchball service when a user creates, edits 
    or comments on content. Defines plugin interface for other content-related plugins.

-   bunchball_fivestar

    Send action information to the Bunchball service when a user rates content 
    using the Fivestar module.

-   bunchball_poll

    Send action information to the Bunchball service when a user votes in a Poll.

-   bunchball_webform

    Send action information to the Bunchball service when a user submits a Webform.

-   bunchball_nitro

    Client-side Javascript module sends action information to the Bunchball 
    service when user views content or plays certain media.