# CakePHP 3.6+ Active Behavior

[![Build Status](https://travis-ci.org/JeanValJeann/cakephp-active-behavior.svg?branch=master)](https://travis-ci.org/JeanValJeann/cakephp-active-behavior)
[![Latest Stable Version](https://poser.pugx.org/jeanvaljean/active-behavior/v/stable)](https://packagist.org/packages/jeanvaljean/active-behavior)
[![Minimum PHP Version](http://img.shields.io/badge/php-%3E%3D%205.6-8892BF.svg)](https://php.net/)
[![License](https://poser.pugx.org/jeanvaljean/active-behavior/license)](https://packagist.org/packages/jeanvaljean/active-behavior)
[![Total Downloads](https://poser.pugx.org/jeanvaljean/active-behavior/downloads)](https://packagist.org/packages/jeanvaljean/active-behavior)

A plugin to manage an active column

This plugin is for CakePHP 3.6+.

## Setup
```
composer require jeanvaljean/active-behavior
```

## Usage

Make sure the table that you want for using the active plugin has a column to store the active state of an entity.
According to the pluging default configuration this column has to be named "active" and has to be:
```php
['type' => 'integer', 'default' => 0, 'limit' => 1, 'null' => false]
```
<b>Note</b> : If you want to use an other column to store the active state of an entity, you can do it, you just have to mention it into pluing configuration using "active_field".

Add the Active behavior in `initialize()` method of your table:
```php
$this->addBehavior('Active.Active');
```
Now the active behavior is available on this table class.

### Configuration
The behavior configuration allow you to mention :
- active_field : the table's field you want for storing the active state of an entity
- group : the table's field you want to use for grouping the active behavior on specific entities
- keep_active : to make sure that always one entity is active
- multiple : to authorize several entities to be active

The default plugin configuration is as below :
```php
$this->addBehavior('Active.Active', [
	'active_field' => 'active', // the table's field you want for storing the active state of an entity
	'group' => '', // the table's field you want to use for grouping the active behavior on specific entities
	'keep_active' => true, // to make sure that always one entity is active
	'multiple' => false // to authorize several entities to be active
]);
```





