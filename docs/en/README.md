# Yii Arrays

The Yii Arrays package provides a helper with static methods allowing you to deal with arrays more efficiently.

## Method Reference

### ArrayHelper

- applyModifiers 
- filter — Filters array according to rules specified
- getColumn — Returns the values of a specified column in an array
- getObjectVars — Returns the public member variables of an object
- getValue — Retrieves the value of an array element or object property with the given key or property name
- getValueByPath — Retrieves the value of an array element or object property with the key path
- htmlDecode — Decodes HTML entities into the corresponding characters in an array of strings
- htmlEncode — Encodes special characters in an array of strings into HTML entities
- index — Indexes and/or groups the array according to a specified key
- isAssociative — Returns a value indicating whether the given array is an associative array
- isIn — Check whether an array or `\Traversable` contains an element
- isIndexed — Returns a value indicating whether the given array is an indexed array
- isSubset — Checks whether an array or `\Traversable` is a subset of another array or `\Traversable`
- keyExists — Checks if the given array contains the specified key
- map — Builds a map (key-value pairs) from a multidimensional array or an array of objects
- merge — Merges two or more arrays into one recursively
- remove — Removes an item from an array and returns the value
- removeByPath — Removes an item from an array by key path and returns the value
- removeValue — Removes items with matching values from the array and returns the removed items
- setValue — Writes a value into an associative array at the key specified
- setValueByPath — Writes a value into an associative array at the key path specified
- toArray — Converts an object or an array of objects into an array

### ArraySorter

- multisort — Sorts an array of objects or arrays (with the same structure) by one or several keys
