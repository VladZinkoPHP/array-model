Zingle Community - Array Model
==============================

## What and why?

Model associative array's as objects. This is useful when you would like some description 
of what is in associative array for future reference. Often times, in code you're also 
need to model certain kinds of behavior around data from various sources (api responses, 
arbitrary database queries, etc.).

## Usage

Extend the `ZingleCom\ArrayModel\AbstractModel` class and add annotations to describe the
kinds of data it models. Example:

~~~ php
use ZingleCom\ArrayModel\AbstractModel;

/**
 * @method int getId()
 * @method string getUsername()
 * @method bool isActive()
 * @method array getFriends()
 */
class SomeApiReponse extends AbstractModel {}

// later in code
// @var array $response
$response = $httpClient->get('/user/123');

// $response suppose arr now contains an associative array like:
// [
//      'id' => 123,
//      'username' => 'bob',
//      'active' => true,
//      'friends' => [234, 345],
// ]
$model = new SomeApiResponse($response);
echo $model->getId(); // prints 123
echo $model->getUsername(); // prints bob
echo $model->isActive() ? 'YES' : 'NO'; // prints YES
echo json_encode($model->getFriends()) // prints [234, 345]
~~~

## Contributing

Open a PR against master. If you see any serious concerns email me at `zach dot quintana 
at gmail dot com`.

Happy coding!
