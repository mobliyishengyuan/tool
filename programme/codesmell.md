简介
=====


Code Smells Within Classes
=====
Comments
------
There's a fine line between comments that illuminate and comments that obscure. Are the comments necessary? Do they explain "why" and not "what"? Can you refactor the code so the comments aren't required? And remember, you're writing comments for people, not machines.
Long Method
------
All other things being equal, a shorter method is easier to read, easier to understand, and easier to troubleshoot. Refactor long methods into smaller methods if you can.
Long Parameter List	The more parameters a method has, the more complex it is. Limit the number of parameters you need in a given method, or use an object to combine the parameters.
Duplicated code
------
Duplicated code is the bane of software development. Stamp out duplication whenever possible. You should always be on the lookout for more subtle cases of near-duplication, too. Don't Repeat Yourself!
Conditional Complexity
------
Watch out for large conditional logic blocks, particularly blocks that tend to grow larger or change significantly over time. Consider alternative object-oriented approaches such as decorator, strategy, or state.
Combinitorial Explosion
------
You have lots of code that does almost the same thing.. but with tiny variations in data or behavior. This can be difficult to refactor-- perhaps using generics or an interpreter?
Large Class
------
Large classes, like long methods, are difficult to read, understand, and troubleshoot. Does the class contain too many responsibilities? Can the large class be restructured or broken into smaller classes?
Type Embedded in Name
------
Avoid placing types in method names; it's not only redundant, but it forces you to change the name if the type changes.
Uncommunicative Name
------
Does the name of the method succinctly describe what that method does? Could you read the method's name to another developer and have them explain to you what it does? If not, rename it or rewrite it.
Inconsistent Names
------
Pick a set of standard terminology and stick to it throughout your methods. For example, if you have Open(), you should probably have Close().
Dead Code
------
Ruthlessly delete code that isn't being used. That's why we have source control systems!
Speculative Generality
------
Write code to solve today's problems, and worry about tomorrow's problems when they actually materialize. Everyone loses in the "what if.." school of design. You (Probably) Aren't Gonna Need It.
Oddball Solution
------
There should only be one way of solving the same problem in your code. If you find an oddball solution, it could be a case of poorly duplicated code-- or it could be an argument for the adapter model, if you really need multiple solutions to the same problem.
Temporary Field
------
Watch out for objects that contain a lot of optional or unnecessary fields. If you're passing an object as a parameter to a method, make sure that you're using all of it and not cherry-picking single fields.


Code Smells Between Classes
=====

Link
=====
http://blog.codinghorror.com/code-smells/
http://www.nowamagic.net/librarys/veda/detail/2053
