##Simple doctrine orm grid bundle with build in twitter bootstrap 3 markup

###1. Installation

Add DataGridBundle in your composer.json:
```
{
    "require": {
        "sotnikus/sotnik-grid-bundle": "dev-master"
    }
}
```

Enable the bundle in the kernel:
```php
public function registerBundles()
{
    $bundles = array(
        // ...
        new Sotnik\GridBundle\SotnikGridBundle(),
    );
}
```

Grid dependencies:
* Jquery
* Bootstrap datetimepicker (if you want to use dateTime filter)
* Twitter bootstrap 3

```html
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <title>{% block title %}Welcome!!!!{% endblock %}</title>
        {% block stylesheets %}
            <!-- Latest compiled and minified CSS -->
            <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
            <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap-theme.min.css">
            <link rel="stylesheet" href="http://cdn.rawgit.com/Eonasdan/bootstrap-datetimepicker/master/build/css/bootstrap-datetimepicker.min.css">
            <link rel="stylesheet" href="{{ asset('bundles/sotnikgrid/css/grid.css') }}">
        {% endblock %}
    </head>
    <body>
        <div class="container">
            {% block body %}

            {% endblock %}
        </div>
        {% block javascripts %}
            <script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
            <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>

            <script src="http://cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment-with-locales.js"></script>
            <script src="http://cdn.rawgit.com/Eonasdan/bootstrap-datetimepicker/v4.0.0/src/js/bootstrap-datetimepicker.js"></script>
            <script src="{{ asset('bundles/sotnikgrid/js/grid.js') }}" type="text/javascript"></script>
        {% endblock %}
    </body>
</html>
```

###2. Usage

